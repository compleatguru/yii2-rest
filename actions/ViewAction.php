<?php

namespace platx\rest\actions;

use yii\db\ActiveRecord;
use platx\httperror\HttpError;
use Yii;


/**
 * Class ViewAction
 * @package platx\rest\actions
 */
class ViewAction extends Action
{
    /**
     * @param $id
     * @return ActiveRecord
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        /** @var ActiveRecord $model */
        $model = new $this->modelClass();

        $model = $model->findOne(['id' => $id]);

        if (empty($model)) {
            HttpError::the404('Not found');
        }

        return $model;
    }
}