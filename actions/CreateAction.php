<?php

namespace platx\rest\actions;

use yii\db\ActiveRecord;
use platx\httperror\HttpError;
use Yii;
use yii\base\Action;


/**
 * Class CreateAction
 * @package platx\rest\actions
 */
class CreateAction extends Action
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
     * @return ActiveRecord
     * @throws \yii\web\NotFoundHttpException
     */
    public function run()
    {
        /** @var ActiveRecord $model */
        $model = new $this->modelClass();

        $post = Yii::$app->request->post();

        if ($post) {
            $model->setAttributes($post);

            if($model->save()) {
                return $model;
            } else {
                return HttpError::validateError('Validation error', $model->errors);
            }
        }

        HttpError::the400('Attributes are empty!');
        return false;
    }
}