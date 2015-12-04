<?php

namespace platx\rest;

use yii\db\ActiveRecord;
use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\data\Sort;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;


/**
 * Базовая форма фильтрации
 * @package platx\rest
 */
class SearchForm extends Model
{
    /**
     * @var string
     */
    public $modelClass;

    /**
     * @var ActiveQuery
     */
    public $query;

    /**
     * @var integer
     */
    public $id;

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
            ['id', 'safe'],
            ['modelClass', 'string'],
            ['query', 'safe'],
            ['modelClass', 'required', 'when' => function($model) {
                return !$model->query;
            }],
            ['query', 'required', 'when' => function($model) {
                return !$model->modelClass;
            }],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'modelClass' => 'Модель для фильтрации',
            'query' => 'Запрос',
        ];
    }

    /**
     * @param array $attributes
     * @return ActiveQuery
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

            return $this->query;
        }

        return null;
    }

    /**
     *
     */
    protected function filterAttributes()
    {
        $this->query->andFilterWhere(['id' => $this->id]);
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
            $config = ['class' => Sort::className()];
            if ($this->id !== null) {
                $config['sortParam'] = $this->id . '-sort';
            }
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

}