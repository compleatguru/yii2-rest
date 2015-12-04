<?php

namespace frontend\modules\api\base\actions;

use yii\db\ActiveRecord;
use platx\httperror\HttpError;
use Yii;
use yii\base\Action;


/**
 * Class DeleteAction
 * @package platx\rest\actions
 */
class DeleteAction extends Action
{
    /**
     * @var string
     */
    public $modelClass;

    /**
     * @throws \yii\web\HttpException
     */
    public function init()
    {
        if (!$this->modelClass) {
            HttpError::the500('$modelClass property must be set!');
        }

        parent::init();
    }

    /**]
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

        if($model->delete()) {
            return true;
        } else {
            return false;
        }
    }
}