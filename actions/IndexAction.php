<?php

namespace frontend\modules\api\base\actions;

use platx\rest\Controller;
use platx\rest\SearchForm;
use platx\rest\Serializer;
use platx\httperror\HttpError;
use Yii;
use yii\base\Action;


/**
 * Class IndexAction
 * @property Controller $controller
 * @package platx\rest\actions
 */
class IndexAction extends Action
{
    /**
     * @var string
     */
    public $searchFormClass;

    /**
     * @throws \yii\web\HttpException
     */
    public function init()
    {
        if (!$this->searchFormClass) {
            HttpError::the500('$searchFormClass property must be set!');
        }

        parent::init();
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function run($offset = 0, $limit = 10)
    {
        /** @var SearchForm $searchForm */
        $searchForm = new $this->searchFormClass();

        $get = Yii::$app->request->get();

        $query = $searchForm->buildQuery($get);

        if(!$query) {
            return HttpError::validateError('Validation error', $searchForm->errors);
        }

        $countAll = $query->count();
        $models = $query->offset($offset)->limit($limit)->all();
        $models = (new Serializer())->serializeModels($models);
        $countCurrent = count($models);

        return [
            'offset' => $offset,
            'limit' => $limit,
            'count_all' => (int) $countAll,
            'count_current' => $countCurrent,
            'items' => $models,
        ];
    }
}