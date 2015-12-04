<?php

namespace platx\rest;

use platx\httperror\HttpError;
use Yii;
use yii\web\Response;


/**
 * Базовый контроллер
 * @package platx\rest
 */
class Controller extends \yii\rest\Controller
{
    /**
     * @var string Модель с которой работает контроллер
     */
    public $modelClass;

    /**
     * @var string Форма фильтрации с которой работает контроллер
     */
    public $searchFormClass;

    /**
     * @throws \yii\web\HttpException
     */
    public function init()
    {
        if (is_null($this->modelClass)) {
            HttpError::the500('$modelClass property must be set!');
        }
        if (is_null($this->searchFormClass)) {
            HttpError::the500('$searchFormClass property must be set!');
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