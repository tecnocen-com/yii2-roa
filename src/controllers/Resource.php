<?php

namespace tecnocen\roa\controllers;

use tecnocen\roa\actions;
use tecnocen\roa\FileRecord;
use Yii;
use yii\base\InvalidRouteException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;

/**
 * Resource Controller with OAuth2 Support.
 *
 * @author  Angel (Faryshta) Guevara <aguevara@tecnocen.com>
 */
class Resource extends \yii\rest\ActiveController
{
    /**
     * @var string[] list of rest actions defined by default.
     */
    const DEFAULT_REST_ACTIONS = [
        'index',
        'view',
        'create',
        'update',
        'delete',
        'file-stream', // download files
        'options',
    ];

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
     * @var string[] $attribute => $param pairs to filter the queries.
     */
    public $filterParams = [];

    /**
     * @var string scenario to be used when updating a record.
     */
    public $updateScenario = ActiveRecord::SCENARIO_DEFAULT;

    /**
     * @var string scenario to be used when creating a new record.
     */
    public $createScenario = ActiveRecord::SCENARIO_DEFAULT;

    /**
     * @var string[] array used in `actions\Create::fileAttributes`
     * @see actions\LoadFileTrait::$fileAttributes
     */
    public $createFileAttributes = [];

    /**
     * @var string[] array used in `actions\Update::fileAttributes`
     * @see actions\LoadFileTrait::$fileAttributes
     */
    public $updateFileAttributes = [];

    /**
     * @var string the message shown when no register is found.
     */
    public $notFoundMessage = 'The record "{id}" does not exists.';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            // content negotiator, autenticator, etc moved by default to
            // api container
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => $this->verbs(),
            ],
        ];
    }

    /**
     * @inheritdoc
     *
     * @throws MethodNotAllowedHttpException in ROA if the resource is reached
     * means the route is valid but the HTTP Method used might not.
     */
    public function runAction($id, $params = [])
    {
        try {
            return parent::runAction($id, $params);
        } catch (InvalidRouteException $e) {
            throw new MethodNotAllowedHttpException(
                'Method Not Allowed. This URL can only handle the following '
                    . 'request methods: '
                    . implode(', ', $this->fetchActionAllowedMethods($id))
                    . '.',
                0,
                $e
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $index = $this->searchClass
            ? [
                'class' => actions\Index::class,
                'searchClass' => $this->searchClass,
                'formName' => $this->searchFormName,
            ]
            : [
                'class' => \yii\rest\IndexAction::class,
                'modelClass' => $this->modelClass,
                'prepareDataProvider' => [$this, 'indexProvider'],
            ];
        $interfaces = class_implements($this->modelClass);
        $fileStream = isset($interfaces[FileRecord::class])
            ? [
                'class' => actions\FileStream::class,
                'modelClass' => $this->modelClass,
                'findModel' => [$this, 'findModel'],
            ]
            : null;

        return [
            'index' => $index,
            'view' => [
                'class' => actions\View::class,
                'modelClass' => $this->modelClass,
                'findModel' => [$this, 'findModel'],
            ],
            'update' => [
                'class' => actions\Update::class,
                'modelClass' => $this->modelClass,
                'findModel' => [$this, 'findModel'],
                'scenario' => $this->updateScenario,
                'fileAttributes' => $this->updateFileAttributes,
            ],
            'create' => [
                'class' => actions\Create::class,
                'modelClass' => $this->modelClass,
                'scenario' => $this->createScenario,
                'fileAttributes' => $this->createFileAttributes,
            ],
            'delete' => [
                'class' => actions\Delete::class,
                'modelClass' => $this->modelClass,
                'findModel' => [$this, 'findModel'],
            ],
            'file-stream' => $fileStream,
            'options' => [
                'class' => \yii\rest\OptionsAction::class,
            ],
        ];
    }

    /**
     * Creates a data provider for the request.
     *
     * @return ActiveDataProvider
     */
    public function indexProvider()
    {
        return new ActiveDataProvider(['query' => $this->indexQuery()]);
    }

    /**
     * Finds the record based on the provided id or throws an exception.
     * @param int $id the unique identifier for the record.
     * @return ActiveRecord
     * @throws NotFoundHttpException if the record can't be found.
     */
    public function findModel($id)
    {
        if (null === ($model = $this->findQuery($id)->one())) {
            throw new NotFoundHttpException(
                strtr($this->notFoundMessage, ['{id}' => $id])
            );
        }

        return $model;
    }

    /**
     * Creates the query to be used by the `findOne()` method.
     *
     * @param int $id the unique identifier
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

        $condition = [];
        foreach ($this->filterParams as $attribute => $param) {
            if (is_int($attribute)) {
                $attribute = $param;
            }
            $condition[$attribute] = Yii::$app->request->getQueryParam($param);
        }
        $query->andFilterWhere($condition);

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
            'update' => ['PUT', 'PATCH', 'POST'],
            'delete' => ['DELETE'],
            'file-stream' => ['GET'],
            'options' => ['OPTIONS'],
        ];
    }

    /**
     * @return string[] actions which serve a single record.
     */
    protected function listRecordActions(): array
    {
        return ['view', 'update', 'delete'];
    }

    /**
     * @return string[] actions which serve a collection of records.
     */
    protected function listCollectionActions(): array
    {
        return ['index', 'create'];
    }

    /**
     * @param string $actionId
     *
     * @return string[] which HTTP Methods are allowed for each action id.
     */
    protected function fetchActionAllowedMethods(string $actionId): array
    {
        $recordActions = $this->listRecordActions();
        $collectionActions = $this->listCollectionActions();
        $verbs = $this->verbs();
        $allowedVerbs = ['OPTIONS'];

        if (in_array($actionId, $recordActions)) {
            foreach ($recordActions as $action) {
                $allowedVerbs = array_merge(
                    $allowedVerbs,
                    ArrayHelper::getValue($verbs, $action, [])
                );
            }
        } elseif (in_array($actionId, $collectionActions)) {
            foreach ($collectionActions as $action) {
                $allowedVerbs = array_merge(
                    $allowedVerbs,
                    ArrayHelper::getValue($verbs, $action, [])
                );
            }
        } else {
            $allowedVerbs = array_merge(
                $allowedVerbs,
                ArrayHelper::getValue($verbs, $actionId, [])
            );
        }

        return array_map('strtoupper', array_unique($allowedVerbs));
    }
}
