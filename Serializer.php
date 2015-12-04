<?php

namespace platx\rest;

use Yii;
use yii\base\Arrayable;
use yii\base\Object;
use yii\helpers\ArrayHelper;


/**
 * Class Serializer
 * @package platx\rest
 */
class Serializer extends Object
{
    /**
     * Serializes a set of models.
     * @param array $models
     * @return array the array representation of the models
     */
    public function serializeModels(array $models)
    {
        list ($fields, $expand) = $this->getRequestedFields();
        foreach ($models as $i => $model) {
            if ($model instanceof Arrayable) {
                $models[$i] = $model->toArray($fields, $expand);
            } elseif (is_array($model)) {
                $models[$i] = ArrayHelper::toArray($model);
            }
        }

        return $models;
    }

    /**
     * @return array the names of the requested fields. The first element is an array
     * representing the list of default fields requested, while the second element is
     * an array of the extra fields requested in addition to the default fields.
     * @see Model::fields()
     * @see Model::extraFields()
     */
    protected function getRequestedFields()
    {
        $fields = Yii::$app->request->get('fields');
        $expand = Yii::$app->request->get('expand');

        return [
            preg_split('/\s*,\s*/', $fields, -1, PREG_SPLIT_NO_EMPTY),
            preg_split('/\s*,\s*/', $expand, -1, PREG_SPLIT_NO_EMPTY),
        ];
    }

}