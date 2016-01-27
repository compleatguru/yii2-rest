Yii2 REST
=========
Rest classes for using in your api module. For **individual use**!

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist platx/yii2-rest "*"
```

or add

```
"platx/yii2-rest": "*"
```

to the require section of your `composer.json` file.


Usage
-----

In your model you can set fields and extraFields for using in API:

```php
class MyModel extends \yii\db\ActiveRecord
{
    public function fields()
    {
        $items = parent::fields();
        unset($items['id']);
        return $items;
    }
    public function extraFields()
    {
        return [
            'custom_field' => 'customField'
        ];
    }
    public function getCustomField()
    {
        return 'Custom value';
    }
}
```


Create search form class for your ActiveRecord model:

```php
class MySearchForm extends \platx\rest\SearchForm 
{
    public $modelClass = '\common\models\MyModel';
    public function rules()
    {
        return \yii\helpers\ArrayHelper::merge(parent::rules(), [
            // HERE PUT YOUR VALIDATION RULES
        ]);
    }
    public function attributeLabels()
    {
        return \yii\helpers\ArrayHelper::merge(parent::attributeLabels(), [
            // HERE PUT YOUR LABELS FOR ATTRIBUTES
        ]);
    }
    public function filterAttributes()
    {
        parent::filterAttributes();
        // HERE type your filter rules, like:
        // $this->query->andFilterWhere(['like', 'attribute', $this->attribute]);
        // ...
    }
}
```

Extend your controller from \platx\rest\Controller, like this:

```php
class MyController extends \platx\rest\Controller 
{
    public function actions()
    {
        public $modelClass = '\common\models\MyModel';
        public $searchFormClass = '\common\forms\MySearchForm';
        return [
            'index' => [
                'class' => '\platx\rest\actions\IndexAction',
                'searchFormClass' => $this->searchFormClass
            ],
            'view' => [
                'class' => '\platx\rest\actions\ViewAction',
                'modelClass' => $this->modelClass
            ],
            'create' => [
                'class' => '\platx\rest\actions\CreateAction',
                'modelClass' => $this->modelClass
            ],
            'update' => [
                'class' => '\platx\rest\actions\UpdateAction',
                'modelClass' => $this->modelClass
            ],
            'delete' => [
                'class' => '\platx\rest\actions\DeleteAction',
                'modelClass' => $this->modelClass
            ],
        ];
    }
}
```

In API Index action you can use query builder:
`/api/my?where[id][in]=1,2,3`
or
`/api/my?where={"id":{"in":[1,2,3]}}`

List of available operators:

| Operator      | Value         | Description |
| ------------- | ------------- | ------------- |
| eq            | One           | Parameter equals value |
| neq           | One           | Parameter not equals value |
| like          | One           | Value is a substring of parameter |
| nlike         | One           | Value is not a substring of parameter |
| in            | Many          | Attribute equals one of many values |
| nin           | Many          | Attribute not equals one of many values |
| gt            | One           | Attribute greater than value |
| gteq          | One           | Attribute greater than or equals value |
| lt            | One           | Attribute less than value |
| lteq          | One           | Attribute less than or equals value |


In your config add this rules to UrlManager rules, if you want to use api like api module:

```php
'rules'=>[
    ...
    'GET <module:api>/<controller:[\w-]+>' => '<module>/<controller>/index',
    'POST <module:api>/<controller:[\w-]+>' => '<module>/<controller>/create',
    'GET <module:api>/<controller:[\w-]+>/<id:\d+>' => '<module>/<controller>/view',
    'PUT <module:api>/<controller:[\w-]+>/<id:\d+>' => '<module>/<controller>/update',
    'DELETE <module:api>/<controller:[\w-]+>/<id:\d+>' => '<module>/<controller>/delete',
    '<module:api>/<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>' => '<module>/<controller>/<action>',
    '<module:api>/<controller:[\w-]+>/<action:[\w-]+>' => '<module>/<controller>/<action>',
    ...
]
```

And now you can use api, for example:

```
GET http://your-site.com/api/my - List of records
GET http://your-site.com/api/my?offset=2&limit=5 - Select records from second to seventh
GET http://your-site.com/api/my?where={"id": {"gteq": 2, "lt": 5}} - Select records with id greater than or equal 2 and less than 5
GET http://your-site.com/api/my?sort=-created_at - List of records with sorting descending by created_at attribute
GET http://your-site.com/api/my?sort=created_at - List of records with sorting ascending by created_at attribute
GET http://your-site.com/api/my?fields=id - List of records with selecting just id attribute
GET http://your-site.com/api/my?fields=id,name - List of records with selecting just id and name attribute
GET http://your-site.com/api/my?expand=custom_field - List of records with selecting fields value from extraFields array in our model
GET http://your-site.com/api/my/1 - Get one record by id attribute
POST http://your-site.com/api/my - Create new record
PUT http://your-site.com/api/my/1 - Update record attributes
DELETE http://your-site.com/api/my/1 - Delete record
```