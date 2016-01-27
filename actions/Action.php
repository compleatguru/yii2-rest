<?php

namespace platx\rest\actions;

use platx\httperror\HttpError;
use Yii;


/**
 * Class Action
 * @package platx\rest\actions
 */
class Action extends \yii\rest\Action
{
    /**
     * @var bool
     */
    public $needAuthorize = false;

    /**
     * @return bool
     * @throws \yii\web\ForbiddenHttpException
     */
    public function beforeRun()
    {
        if ($this->needAuthorize && Yii::$app->user->isGuest) {
            HttpError::the403();
        }

        return parent::beforeRun();
    }
}