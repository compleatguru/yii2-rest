<?php

namespace platx\rest;

use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\data\Sort;


/**
 * Base search form
 * @package platx\rest
 */
class SearchForm extends Model
{
    /**
     * @var string
     */
    public $modelClass;

    /**
     * @var \yii\db\ActiveQuery
     */
    public $query;

    /**
     * @var bool
     */
    public $whereEnabled = true;

    /**
     * @var array|string|null
     */
    public $where;

    /**
     * @var bool
     */
    public $enableMultiSort = true;

    /**
     * @var string
     */
    protected $_sort;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['modelClass', 'string'],
            ['query', 'safe'],
            ['modelClass', 'required', 'when' => function($model) {
                return !$model->query;
            }],
            ['query', 'required', 'when' => function($model) {
                return !$model->modelClass;
            }],
            ['where', 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'modelClass' => 'Model class for filter',
            'query' => 'Query',
            'where' => 'Query filter',
        ];
    }

    /**
     * @param array $attributes
     * @return \yii\db\ActiveQuery
     */
    public function buildQuery($attributes = [])
    {
        $this->setAttributes($attributes);

        if(!empty($this->modelClass) && empty($this->query)) {
            /** @var ActiveRecord $model */
            $model = new $this->modelClass();
            $this->query = $model->find();
        }

        if($this->validate()) {
            if (!empty($this->query)) {
                $this->filterAttributes();
            }

            if (($sort = $this->getSort()) !== false) {
                $this->query->addOrderBy($sort->getOrders());
            }

            if ($this->whereEnabled && $this->where) {
                $this->query = $this->setWhere($this->query, $this->where);
            }

            return $this->query;
        }

        return null;
    }

    /**
     *
     */
    protected function filterAttributes()
    {

    }

    /**
     * @return Sort|boolean the sorting object. If this is false, it means the sorting is disabled.
     */
    public function getSort()
    {
        if ($this->_sort === null) {
            $this->setSort([]);
        }

        return $this->_sort;
    }

    /**
     * @inheritdoc
     */
    public function setSort($value)
    {
        if (is_array($value)) {
            $config = ['class' => Sort::className(), 'enableMultiSort' => $this->enableMultiSort];
            $this->_sort = Yii::createObject(array_merge($config, $value));
        } elseif ($value instanceof Sort || $value === false) {
            $this->_sort = $value;
        } else {
            throw new InvalidParamException('Only Sort instance, configuration array or false is allowed.');
        }

        if (($sort = $this->getSort()) !== false && $this->query instanceof ActiveQueryInterface) {
            /* @var $model Model */
            $model = new $this->query->modelClass;
            if (empty($sort->attributes)) {
                foreach ($model->attributes() as $attribute) {
                    $sort->attributes[$attribute] = [
                        'asc' => [$attribute => SORT_ASC],
                        'desc' => [$attribute => SORT_DESC],
                        'label' => $model->getAttributeLabel($attribute),
                    ];
                }
            } else {
                foreach ($sort->attributes as $attribute => $config) {
                    if (!isset($config['label'])) {
                        $sort->attributes[$attribute]['label'] = $model->getAttributeLabel($attribute);
                    }
                }
            }
        }
    }

    /**
     * @param $query
     * @param $params
     * @return null|\yii\db\Query|\yii\db\ActiveQuery
     */
    public function setWhere($query, $params)
    {
        return (new ApiQuery())->set($params)->build($query);
    }

}