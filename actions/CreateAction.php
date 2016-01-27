<?php

namespace platx\rest\actions;

use Yii;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;


/**
 * Class CreateAction
 * @package platx\rest\actions
 */
class CreateAction extends Action
{
    /**
     * @var string
     */
    public $viewAction = 'view';

    /**
     * @return \yii\db\ActiveRecord
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        /** @var \yii\db\ActiveRecord $model */
        $model = new $this->modelClass();

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }
}