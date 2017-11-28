<?php

namespace platx\rest;

use yii\base\InvalidParamException;
use yii\base\Object;
use yii\mongodb\Query;
use yii\helpers\Json;


/**
 * Class ApiQuery
 * @package platx\rest
 */
class ApiQuery extends Object
{
    const OPERATOR_EQUAL = 'eq';
    const OPERATOR_NOT_EQUAL = 'neq';
    const OPERATOR_LIKE = 'like';
    const OPERATOR_NOT_LIKE = 'nlike';
    const OPERATOR_IN = 'in';
    const OPERATOR_NOT_IN = 'nin';
    const OPERATOR_GREATER_THAN = 'gt';
    const OPERATOR_GREATER_THAN_OR_EQUAL = 'gteq';
    const OPERATOR_LESS_THAN = 'lt';
    const OPERATOR_LESS_THAN_OR_EQUAL = 'lteq';

    /**
     * @var array Query parameters
     */
    public $params;

    /**
     * @var array Query operators
     */
    private $_operators = [
        self::OPERATOR_EQUAL => '=',
        self::OPERATOR_NOT_EQUAL => '!=',
        self::OPERATOR_LIKE => 'like',
        self::OPERATOR_NOT_LIKE => 'not like',
        self::OPERATOR_IN => 'IN',
        self::OPERATOR_NOT_IN => 'NOT IN',
        self::OPERATOR_GREATER_THAN => '>',
        self::OPERATOR_GREATER_THAN_OR_EQUAL => '>=',
        self::OPERATOR_LESS_THAN => '<',
        self::OPERATOR_LESS_THAN_OR_EQUAL => '<=',
    ];

    /**
     *
     */
    public function init()
    {
        parent::init();

        $this->set($this->params);
    }

    /**
     * @param null $query
     * @return null|Query
     */
    public function build($query = null)
    {
        if (is_null($query)) {
            $query = new Query();
        } elseif (!($query instanceof Query)) {
            throw new InvalidParamException("Query must be a Query object.");
        }

        if (is_array($this->params)) {
            foreach($this->params as $param => $operators)
            {
                foreach ($operators as $operator => $value) {
                    $this->_checkOperator($operator);

                    if (is_string($value) && strpos($value, ',')) {
                        $value = explode(',', $value);
                    }
                    $this->_checkValue($value, $operator);

                    $query->andWhere([$this->_operators[$operator], $param, $value]);
                }
            }
        }

        return $query;
    }

    /**
     * @param $params
     * @return $this
     */
    public function set($params)
    {
        if (!empty($params) && !is_array($params)) {
            $params = Json::decode($params);
        }

        $this->params = $params;

        return $this;
    }

    /**
     * @param $operator
     */
    private function _checkOperator($operator)
    {
        if(!isset($this->_operators[$operator])) {
            throw new InvalidParamException("Invalid operator '$operator'.");
        }
    }

    /**
     * @param $value
     * @param null $operator
     * @return bool
     */
    private function _checkValue($value, $operator = null)
    {
        if (!is_null($value)) {
            if(!is_null($operator)) {
                if($operator == self::OPERATOR_IN || $operator == self::OPERATOR_NOT_IN) {
                    if(is_array($value) || (is_string($value) && strpos($value, ','))) {
                        return true;
                    }
                } else {
                    if(!is_array($value) && !strpos($value, ',')) {
                        return true;
                    }
                }
            } else {
                return true;
            }
        }

        $value = print_r($value, true);
        throw new InvalidParamException("Invalid value '$value' for operator '$operator'.");
    }
}
