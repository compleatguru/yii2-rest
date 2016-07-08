<?php

namespace platx\rest\actions;

use platx\httperror\HttpError;
use platx\rest\Serializer;
use Yii;
use yii\base\InvalidConfigException;


/**
 * Class IndexAction
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
        if ($this->searchFormClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$searchFormClass must be set.');
        }
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function run($offset = 0, $limit = 10)
    {
        /** @var \platx\rest\SearchForm $searchForm */
        $searchForm = new $this->searchFormClass();

        $get = Yii::$app->request->get();

        $query = $searchForm->buildQuery($get);

        if(!$query) {
            return HttpError::validateError('Validation error', $searchForm->errors);
        }

        $queryCount = clone $query;
        $countAll = (int) $queryCount->limit(-1)->offset(-1)->orderBy([])->count('*');
        $query->offset($offset);
        if($limit) {
            $query->limit($limit);
        }
        $models = $query->all();
        $models = (new Serializer())->serializeModels($models);
        $countCurrent = count($models);

        $result = [
            'offset' => $offset,
            'limit' => $limit,
            'count_all' => (int) $countAll,
            'count_current' => $countCurrent,
            'items' => $models,
        ];

        return $result;
    }
}