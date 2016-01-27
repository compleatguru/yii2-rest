<?php

namespace platx\rest;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\Response;


/**
 * Base rest controller
 * @package platx\rest
 */
class Controller extends \yii\rest\Controller
{
    /**
     * @var string
     */
    public $modelClass;

    /**
     * @var string
     */
    public $searchFormClass;

    /**
     * @throws \yii\web\HttpException
     */
    public function init()
    {
        if ($this->modelClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$modelClass must be set.');
        }
        if ($this->searchFormClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$searchFormClass must be set.');
        }

        parent::init();
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats'] = ['application/json' => Response::FORMAT_JSON];
        return $behaviors;
    }

    /**
     * @return array
     */
    public function verbs()
    {
        return [
            'create' => ['post'],
            'update' => ['put', 'post', 'update'],
            'delete' => ['post', 'delete'],
            'index' => ['get'],
            'view' => ['get'],
        ];
    }
}