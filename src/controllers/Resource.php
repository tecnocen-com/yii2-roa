<?php

namespace tecnocen\roa\controllers;

use tecnocen\roa\actions;
use tecnocen\roa\FileRecord;
use Yii;
use yii\base\InvalidRouteException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
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
                'actions' => $this->buildAllowedVerbs(),
            ],
        ];
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
    public function indexProvider(): ActiveDataProvider
    {
        return new ActiveDataProvider(['query' => $this->indexQuery()]);
    }

    /**
     * Finds the record based on the provided id or throws an exception.
     * @param int $id the unique identifier for the record.
     * @return ActiveRecord
     * @throws NotFoundHttpException if the record can't be found.
     */
    public function findModel($id): ActiveRecord
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
    public function findQuery($id): ActiveQuery
    {
        return $this->baseQuery()->andWhere([$this->idAttribute => $id]);
    }

    /**
     * Creates the query to be used by the `index` action when `$searchClass` is
     * not set.
     *
     * @return ActiveQuery
     */
    public function indexQuery(): ActiveQuery
    {
        return $this->baseQuery();
    }

    /**
     * @return ActiveQuery
     */
    protected function baseQuery(): ActiveQuery
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
     * Builds the HTTP Methods allowed for each action.
     *
     * Since ROA Resources differentiate routes on record routes and collection
     * rules it was needed to organize the action into record action and
     * collection actions and make sure that all record/collection actions
     * returned the same allowed verbs since they are using the same route.
     *
     * @return string[] which HTTP Methods are allowed for each action id.
     * @see VerbFilter::$verbs
     */
    protected function buildAllowedVerbs(): array
    {
        $verbs = $this->verbs();
        $recordActions = $this->listRecordActions();
        $collectionActions = $this->listCollectionActions();
        $recordVerbs = ['OPTIONS'];
        $collectionVerbs = ['OPTIONS'];

        foreach ($recordActions as $action) {
            $recordVerbs = array_merge(
                $recordVerbs,
                ArrayHelper::getValue($verbs, $action, [])
            );
        }

        $recordVerbs = array_values(array_unique(
            array_map('strtoupper', $recordVerbs)
        ));

        foreach ($collectionActions as $action) {
            $collectionVerbs = array_merge(
                $collectionVerbs,
                ArrayHelper::getValue($verbs, $action, [])
            );
        }

        $collectionVerbs = array_values(array_unique(
            array_map('strtoupper', $collectionVerbs)
        ));

        $allowedVerbs = ['options' => 'OPTIONS'];
        foreach ($verbs as $action => $defaultVerbs) {
            if (in_array($action, $recordActions)) {
                $allowedVerbs[$action] = $recordVerbs;
            } elseif (in_array($action, $collectionActions)) {
                $allowedVerbs[$action] = $collectionVerbs;
            } else {
                $allowedVerbs[$action] = $defaultVerbs;
            }
        }

        foreach (self::DEFAULT_REST_ACTIONS as $action) {
            if (!isset($allowedVerbs[$action])) {
                if (in_array($action, $recordActions)) {
                    $allowedVerbs[$action] = $recordVerbs;
                } elseif (in_array($action, $collectionActions)) {
                    $allowedVerbs[$action] = $collectionVerbs;
                }
            }
        }

        return $allowedVerbs;
    }
}
