<?php

namespace tecnocen\roa\controllers;

use Yii;
use filsh\yii2\oauth2server\filters\ErrorToExceptionFilter;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;
use yii\filters\Cors;
use yii\filters\HostControl;
use yii\filters\HttpCache;
use yii\filters\PageCache;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Resource Controller with OAuth2 Support.
 *
 * @author  Angel (Faryshta) Guevara <aguevara@tecnocen.com>
 */
class OAuth2Resource extends \yii\rest\ActiveController
{
    const DEFAULT_REST_ACTIONS = [
        'index',
        'view',
        'create',
        'update',
        'delete',
        'options',
    ];

    /**
     * @var boolean whether to enable http and page cache.
     */
    public $enableCache = false;

    /**
     * @var string name of the attribute for the model. It will be used by the
     * cache behaviors.
     */
    public $updatedAtAttribute = 'updated_at';

    /**
     * @var string name of the attribute to be used on `findModel()`.
     */
    public $idAttribute = 'id';

    /**
     * @var string attribute name used to filter only the records associated to
     * the logged user.
     * If `null` then no filter will be added.
     */
    public $userAttribute;

    /**
     * @var string class name for the model to be used on the search.
     * Must implement `tecnocen\roa\ResourceSearchInterface`
     */
    public $searchClass;

    /**
     * @var string name of the form which will hold the GET parameters to filter
     * results on a search request.
     */
    public $searchFormName = '';

    /**
     * @var string scenario to be used when updating a record.
     */
    public $updateScenario = ActiveRecord::SCENARIO_DEFAULT;

    /**
     * @var string scenario to be used when creating a new record.
     */
    public $createScenario = ActiveRecord::SCENARIO_DEFAULT;

    /**
     * @var string the message shown when no register is found.
     */
    public $notFoundMessage = 'The record "{id}" does not exists.';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => CompositeAuth::class,
                'authMethods' => [
                    ['class' => HttpBearerAuth::class],
                    [
                        'class' => QueryParamAuth::class,
                        // !Important, GET request parameter to get the token.
                        'tokenParam' => 'accessToken',
                    ],
                ],
            ],
            'exceptionFilter' => ['class' => ErrorToExceptionFilter::class],
            'access' => [
                'class' => AccessControl::class,
                'except' => ['options'],
                'rules' => $this->accessRules(),
            ],
            'cors' => [
                'class' => Cors::class,
                'cors' => $this->cors(),
            ],
            'allowedHosts' => [
                'class' => HostControl::class,
                'allowedHosts' => $this->allowedHosts(),
            ],
            'httpIndexCache' => [
                'class' => HttpCache::class,
                'only' => ['index'],
                'enabled' => $this->enableCache,
                'lastModified' => [$this, 'lastModifiedIndex'],
            ],
            'pageIndexCache' => [
                'class' => PageCache::class,
                'enabled' => $this->enableCache,
            ],
            
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = ArrayHelper::merge(parent::actions(), [
            'index' => [
                'prepareDataProvider' => [$this, 'indexProvider'],
            ],
            'view' => ['findModel' => [$this, 'findModel']],
            'update' => [
                'findModel' => [$this, 'findModel'],
                'scenario' => $this->updateScenario,
            ],
            'create' => ['scenario' => $this->createScenario],
            'delete' => ['findModel' => [$this, 'findModel']],
        ]);

        foreach (array_diff(
            self::DEFAULT_REST_ACTIONS,
            array_keys($this->verbs())
        ) as $unsupportedAction) {
            unset($actions[$unsupportedAction]);
        }

        return $actions;
    }

    /**
     * @return integer unix timestamp
     */
    public function lastModifiedIndex($action, $params)
    {
        $modelClass = $this->modelClass;
        return $modelClass::find()->max($this->updatedAtAttribute);
    }

    /**
     * Creates a data provider for the request.
     *
     * @return ActiveRecord|ActiveDataProvider the search model if there are
     * validation errors and the data provider in any other case.
     */
    public function indexProvider()
    {
        if (empty($this->searchClass)) {
            return new ActiveDataProvider(['query' => $this->indexQuery()]);
        }

        $searchClass = $this->searchClass;
        $searchModel = new $searchClass();
        $searchModel->load(
            Yii::$app->request->queryParams,
            $this->searchFormName
        );

        return $searchModel->validate() ? $searchModel->search() : $searchModel;
    }

    /**
     * Finds the record based on the provided id or throws an exception.
     * @param integer $id the unique identifier for the record.
     * @return ActiveRecord
     * @throws NotFoundHttpException if the record can't be found.
     */
    public function findModel($id)
    {
        if (null === ($model = $this->findQuery($id)->one())) {
            throw new NotFoundHttpException(
                Yii::t('tecnocen/roa', $this->notFoundMessage, ['id' => $id])
            );
        }
        return $model;
    }

    /**
     * Creates the query to be used by the `findOne()` method.
     *
     * @param integer $id the unique identifier
     * @return ActiveQuery
     */
    public function findQuery($id)
    {
        return $this->baseQuery()->andWhere([$this->idAttribute => $id]);
    }

    /**
     * Creates the query to be used by the `index` action when `$searchClass` is
     * not set.
     *
     * @return ActiveQuery
     */
    public function indexQuery()
    {
        return $this->baseQuery();
    }

    /**
     * @return ActiveQuery
     */
    protected function baseQuery()
    {
        $modelClass = $this->modelClass;
        $query = $modelClass::find();
        if (isset($this->userAttribute)) {
            $query->andWhere([$this->userAttribute => Yii::$app->user->id]);
        }
        return $query;
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
            'options' => ['OPTIONS'],
        ];
    }

    /**
     * @return array list of rules to access the controller.
     * @see http://www.yiiframework.com/doc-2.0/yii-filters-accesscontrol.html#$rules-detail
     */
    protected function accessRules()
    {
        return [['allow' => true, 'roles' => ['@']]];
    }

    /**
     * @return array list of CORS headers
     * @see http://www.yiiframework.com/doc-2.0/yii-filters-cors.html#$cors-detail
     */
    protected function cors()
    {
        return  [
            'Origin' => ['localhost', Yii::$app->request->serverName],
            'Access-Control-Request-Method' => $this->httpMethods(),
            'Access-Control-Request-Headers' => ['*'],
            'Access-Control-Allow-Credentials' => null,
            'Access-Control-Max-Age' => 86400,
            'Access-Control-Expose-Headers' => [],
        ];
    }

    /**
     * @return string[] list of allowed hosts to generate links.
     * @see http://www.yiiframework.com/doc-2.0/yii-filters-hostcontrol.html#allowedHosts-detail
     */
    protected function allowedHosts()
    {
        return [Yii::$app->request->serverName];
    }

    /**
     * @return string[] list of allowed http methods for this controller.
     * @see verbs()
     */
    private function httpMethods()
    {
        $methods = [];
        foreach ($this->verbs() as $verbMethods) {
            $methods = array_unique(array_merge($methods, $verbMethods));
        }
        return $methods;
    }
}
