<?php

namespace platx\rest\actions;

use platx\httperror\HttpError;
use Yii;
use yii\web\ServerErrorHttpException;


/**
 * Class UpdateAction
 * @package platx\rest\actions
 */
class UpdateAction extends Action
{
    /**
     * @param $id
     * @return \yii\db\ActiveRecord
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        /** @var \yii\db\ActiveRecord $model */
        $model = new $this->modelClass();

        $model = $model->findOne(['id' => $id]);

        if (empty($model)) {
            HttpError::the404('Not found');
        }

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }
}