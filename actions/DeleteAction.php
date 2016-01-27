<?php

namespace platx\rest\actions;

use platx\httperror\HttpError;
use Yii;
use yii\web\ServerErrorHttpException;


/**
 * Class DeleteAction
 * @package platx\rest\actions
 */
class DeleteAction extends Action
{
    /**
     * @param $id
     * @throws ServerErrorHttpException
     * @throws \Exception
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

        if ($model->delete() === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }
}