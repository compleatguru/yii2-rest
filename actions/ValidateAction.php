<?php

namespace platx\rest\actions;

use Yii;
use yii\web\ServerErrorHttpException;


/**
 * Class ValidateAction
 * @package platx\rest\actions
 */
class ValidateAction extends Action
{
    /**
     * @return \yii\db\ActiveRecord
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        /** @var \yii\db\ActiveRecord $model */
        $model = new $this->modelClass();
        $fields = Yii::$app->request->get('fields');
        $fields = preg_split('/\s*,\s*/', $fields, -1, PREG_SPLIT_NO_EMPTY);
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->validate($fields)) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(200);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }
}