<?php

namespace platx\rest;

use yii\db\ActiveRecord;
use platx\httperror\HttpError;
use Yii;
use yii\base\Action;


/**
 * Class UpdateAction
 * @package frontend\modules\api\base\actions
 */
class UpdateAction extends Action
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