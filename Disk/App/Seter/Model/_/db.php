<?php


interface ActiveRecordInterface
{
    /**
     * Returns the primary key **name(s)** for this AR class.
     *
     * Note that an array should be returned even when the record only has a single primary key.
     *
     * For the primary key **value** see [[getPrimaryKey()]] instead.
     *
     * @return string[] the primary key name(s) for this AR class.
     */
    public static function primaryKey();

    /**
     * Returns the list of all attribute names of the record.
     * @return array list of attribute names.
     */
    public function attributes();

    /**
     * Returns the named attribute value.
     * If this record is the result of a query and the attribute is not loaded,
     * null will be returned.
     * @param string $name the attribute name
     * @return mixed the attribute value. Null if the attribute is not set or does not exist.
     * @see hasAttribute()
     */
    public function getAttribute($name);

    /**
     * Sets the named attribute value.
     * @param string $name the attribute name.
     * @param mixed $value the attribute value.
     * @see hasAttribute()
     */
    public function setAttribute($name, $value);

    /**
     * Returns a value indicating whether the record has an attribute with the specified name.
     * @param string $name the name of the attribute
     * @return boolean whether the record has an attribute with the specified name.
     */
    public function hasAttribute($name);

    /**
     * Returns the primary key value(s).
     * @param boolean $asArray whether to return the primary key value as an array. If true,
     * the return value will be an array with attribute names as keys and attribute values as values.
     * Note that for composite primary keys, an array will always be returned regardless of this parameter value.
     * @return mixed the primary key value. An array (attribute name => attribute value) is returned if the primary key
     * is composite or `$asArray` is true. A string is returned otherwise (null will be returned if
     * the key value is null).
     */
    public function getPrimaryKey($asArray = false);

    /**
     * Returns the old primary key value(s).
     * This refers to the primary key value that is populated into the record
     * after executing a find method (e.g. find(), findOne()).
     * The value remains unchanged even if the primary key attribute is manually assigned with a different value.
     * @param boolean $asArray whether to return the primary key value as an array. If true,
     * the return value will be an array with column name as key and column value as value.
     * If this is false (default), a scalar value will be returned for non-composite primary key.
     * @property mixed The old primary key value. An array (column name => column value) is
     * returned if the primary key is composite. A string is returned otherwise (null will be
     * returned if the key value is null).
     * @return mixed the old primary key value. An array (column name => column value) is returned if the primary key
     * is composite or `$asArray` is true. A string is returned otherwise (null will be returned if
     * the key value is null).
     */
    public function getOldPrimaryKey($asArray = false);

    /**
     * Returns a value indicating whether the given set of attributes represents the primary key for this model
     * @param array $keys the set of attributes to check
     * @return boolean whether the given set of attributes represents the primary key for this model
     */
    public static function isPrimaryKey($keys);

    /**
     * Creates an [[ActiveQueryInterface]] instance for query purpose.
     *
     * The returned [[ActiveQueryInterface]] instance can be further customized by calling
     * methods defined in [[ActiveQueryInterface]] before `one()` or `all()` is called to return
     * populated ActiveRecord instances. For example,
     *
     * ```php
     * // find the customer whose ID is 1
     * $customer = Customer::find()->where(['id' => 1])->one();
     *
     * // find all active customers and order them by their age:
     * $customers = Customer::find()
     *     ->where(['status' => 1])
     *     ->orderBy('age')
     *     ->all();
     * ```
     *
     * This method is also called by [[BaseActiveRecord::hasOne()]] and [[BaseActiveRecord::hasMany()]] to
     * create a relational query.
     *
     * You may override this method to return a customized query. For example,
     *
     * ```php
     * class Customer extends ActiveRecord
     * {
     *     public static function find()
     *     {
     *         // use CustomerQuery instead of the default ActiveQuery
     *         return new CustomerQuery(get_called_class());
     *     }
     * }
     * ```
     *
     * The following code shows how to apply a default condition for all queries:
     *
     * ```php
     * class Customer extends ActiveRecord
     * {
     *     public static function find()
     *     {
     *         return parent::find()->where(['deleted' => false]);
     *     }
     * }
     *
     * // Use andWhere()/orWhere() to apply the default condition
     * // SELECT FROM customer WHERE `deleted`=:deleted AND age>30
     * $customers = Customer::find()->andWhere('age>30')->all();
     *
     * // Use where() to ignore the default condition
     * // SELECT FROM customer WHERE age>30
     * $customers = Customer::find()->where('age>30')->all();
     *
     * @return ActiveQueryInterface the newly created [[ActiveQueryInterface]] instance.
     */
    public static function find();

    /**
     * Returns a single active record model instance by a primary key or an array of column values.
     *
     * The method accepts:
     *
     *  - a scalar value (integer or string): query by a single primary key value and return the
     *    corresponding record (or null if not found).
     *  - a non-associative array: query by a list of primary key values and return the
     *    first record (or null if not found).
     *  - an associative array of name-value pairs: query by a set of attribute values and return a single record
     *    matching all of them (or null if not found). Note that `['id' => 1, 2]` is treated as a non-associative array.
     *
     * That this method will automatically call the `one()` method and return an [[ActiveRecordInterface|ActiveRecord]]
     * instance. For example,
     *
     * ```php
     * // find a single customer whose primary key value is 10
     * $customer = Customer::findOne(10);
     *
     * // the above code is equivalent to:
     * $customer = Customer::find()->where(['id' => 10])->one();
     *
     * // find the first customer whose age is 30 and whose status is 1
     * $customer = Customer::findOne(['age' => 30, 'status' => 1]);
     *
     * // the above code is equivalent to:
     * $customer = Customer::find()->where(['age' => 30, 'status' => 1])->one();
     * ```
     *
     * @param mixed $condition primary key value or a set of column values
     * @return static|null ActiveRecord instance matching the condition, or null if nothing matches.
     */
    public static function findOne($condition);

    /**
     * Returns a list of active record models that match the specified primary key value(s) or a set of column values.
     *
     * The method accepts:
     *
     *  - a scalar value (integer or string): query by a single primary key value and return an array containing the
     *    corresponding record (or an empty array if not found).
     *  - a non-associative array: query by a list of primary key values and return the
     *    corresponding records (or an empty array if none was found).
     *    Note that an empty condition will result in an empty result as it will be interpreted as a search for
     *    primary keys and not an empty `WHERE` condition.
     *  - an associative array of name-value pairs: query by a set of attribute values and return an array of records
     *    matching all of them (or an empty array if none was found). Note that `['id' => 1, 2]` is treated as
     *    a non-associative array.
     *
     * This method will automatically call the `all()` method and return an array of [[ActiveRecordInterface|ActiveRecord]]
     * instances. For example,
     *
     * ```php
     * // find the customers whose primary key value is 10
     * $customers = Customer::findAll(10);
     *
     * // the above code is equivalent to:
     * $customers = Customer::find()->where(['id' => 10])->all();
     *
     * // find the customers whose primary key value is 10, 11 or 12.
     * $customers = Customer::findAll([10, 11, 12]);
     *
     * // the above code is equivalent to:
     * $customers = Customer::find()->where(['id' => [10, 11, 12]])->all();
     *
     * // find customers whose age is 30 and whose status is 1
     * $customers = Customer::findAll(['age' => 30, 'status' => 1]);
     *
     * // the above code is equivalent to:
     * $customers = Customer::find()->where(['age' => 30, 'status' => 1])->all();
     * ```
     *
     * @param mixed $condition primary key value or a set of column values
     * @return array an array of ActiveRecord instance, or an empty array if nothing matches.
     */
    public static function findAll($condition);

    /**
     * Updates records using the provided attribute values and conditions.
     * For example, to change the status to be 1 for all customers whose status is 2:
     *
     * ~~~
     * Customer::updateAll(['status' => 1], ['status' => '2']);
     * ~~~
     *
     * @param array $attributes attribute values (name-value pairs) to be saved for the record.
     * Unlike [[update()]] these are not going to be validated.
     * @param array $condition the condition that matches the records that should get updated.
     * Please refer to [[QueryInterface::where()]] on how to specify this parameter.
     * An empty condition will match all records.
     * @return integer the number of rows updated
     */
    public static function updateAll($attributes, $condition = null);

    /**
     * Deletes records using the provided conditions.
     * WARNING: If you do not specify any condition, this method will delete ALL rows in the table.
     *
     * For example, to delete all customers whose status is 3:
     *
     * ~~~
     * Customer::deleteAll([status = 3]);
     * ~~~
     *
     * @param array $condition the condition that matches the records that should get deleted.
     * Please refer to [[QueryInterface::where()]] on how to specify this parameter.
     * An empty condition will match all records.
     * @return integer the number of rows deleted
     */
    public static function deleteAll($condition = null);

    /**
     * Saves the current record.
     *
     * This method will call [[insert()]] when [[getIsNewRecord()|isNewRecord]] is true, or [[update()]]
     * when [[getIsNewRecord()|isNewRecord]] is false.
     *
     * For example, to save a customer record:
     *
     * ~~~
     * $customer = new Customer; // or $customer = Customer::findOne($id);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->save();
     * ~~~
     *
     * @param boolean $runValidation whether to perform validation before saving the record.
     * If the validation fails, the record will not be saved to database. `false` will be returned
     * in this case.
     * @param array $attributeNames list of attributes that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     * @return boolean whether the saving succeeds
     */
    public function save($runValidation = true, $attributeNames = null);

    /**
     * Inserts the record into the database using the attribute values of this record.
     *
     * Usage example:
     *
     * ```php
     * $customer = new Customer;
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->insert();
     * ```
     *
     * @param boolean $runValidation whether to perform validation before saving the record.
     * If the validation fails, the record will not be inserted into the database.
     * @param array $attributes list of attributes that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     * @return boolean whether the attributes are valid and the record is inserted successfully.
     */
    public function insert($runValidation = true, $attributes = null);

    /**
     * Saves the changes to this active record into the database.
     *
     * Usage example:
     *
     * ```php
     * $customer = Customer::findOne($id);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->update();
     * ```
     *
     * @param boolean $runValidation whether to perform validation before saving the record.
     * If the validation fails, the record will not be inserted into the database.
     * @param array $attributeNames list of attributes that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     * @return integer|boolean the number of rows affected, or false if validation fails
     * or updating process is stopped for other reasons.
     * Note that it is possible that the number of rows affected is 0, even though the
     * update execution is successful.
     */
    public function update($runValidation = true, $attributeNames = null);

    /**
     * Deletes the record from the database.
     *
     * @return integer|boolean the number of rows deleted, or false if the deletion is unsuccessful for some reason.
     * Note that it is possible that the number of rows deleted is 0, even though the deletion execution is successful.
     */
    public function delete();

    /**
     * Returns a value indicating whether the current record is new (not saved in the database).
     * @return boolean whether the record is new and should be inserted when calling [[save()]].
     */
    public function getIsNewRecord();

    /**
     * Returns a value indicating whether the given active record is the same as the current one.
     * Two [[getIsNewRecord()|new]] records are considered to be not equal.
     * @param static $record record to compare to
     * @return boolean whether the two active records refer to the same row in the same database table.
     */
    public function equals($record);

    /**
     * Returns the relation object with the specified name.
     * A relation is defined by a getter method which returns an object implementing the [[ActiveQueryInterface]]
     * (normally this would be a relational [[ActiveQuery]] object).
     * It can be declared in either the ActiveRecord class itself or one of its behaviors.
     * @param string $name the relation name
     * @param boolean $throwException whether to throw exception if the relation does not exist.
     * @return ActiveQueryInterface the relational query object
     */
    public function getRelation($name, $throwException = true);

    /**
     * Establishes the relationship between two records.
     *
     * The relationship is established by setting the foreign key value(s) in one record
     * to be the corresponding primary key value(s) in the other record.
     * The record with the foreign key will be saved into database without performing validation.
     *
     * If the relationship involves a junction table, a new row will be inserted into the
     * junction table which contains the primary key values from both records.
     *
     * This method requires that the primary key value is not null.
     *
     * @param string $name the case sensitive name of the relationship.
     * @param static $model the record to be linked with the current one.
     * @param array $extraColumns additional column values to be saved into the junction table.
     * This parameter is only meaningful for a relationship involving a junction table
     * (i.e., a relation set with `[[ActiveQueryInterface::via()]]`.)
     */
    public function link($name, $model, $extraColumns = []);

    /**
     * Destroys the relationship between two records.
     *
     * The record with the foreign key of the relationship will be deleted if `$delete` is true.
     * Otherwise, the foreign key will be set null and the record will be saved without validation.
     *
     * @param string $name the case sensitive name of the relationship.
     * @param static $model the model to be unlinked from the current one.
     * @param boolean $delete whether to delete the model that contains the foreign key.
     * If false, the model's foreign key will be set null and saved.
     * If true, the model containing the foreign key will be deleted.
     */
    public function unlink($name, $model, $delete = false);

    /**
     * Returns the connection used by this AR class.
     * @return mixed the database connection used by this AR class.
     */
    public static function getDb();
}








class Model extends Component implements IteratorAggregate, ArrayAccess, Arrayable
{
    use ArrayableTrait;

    /**
     * The name of the default scenario.
     */
    const SCENARIO_DEFAULT = 'default';
    /**
     * @event ModelEvent an event raised at the beginning of [[validate()]]. You may set
     * [[ModelEvent::isValid]] to be false to stop the validation.
     */
    const EVENT_BEFORE_VALIDATE = 'beforeValidate';
    /**
     * @event Event an event raised at the end of [[validate()]]
     */
    const EVENT_AFTER_VALIDATE = 'afterValidate';

    /**
     * @var array validation errors (attribute name => array of errors)
     */
    private $_errors;
    /**
     * @var ArrayObject list of validators
     */
    private $_validators;
    /**
     * @var string current scenario
     */
    private $_scenario = self::SCENARIO_DEFAULT;


    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * Each rule is an array with the following structure:
     *
     * ~~~
     * [
     *     ['attribute1', 'attribute2'],
     *     'validator type',
     *     'on' => ['scenario1', 'scenario2'],
     *     ...other parameters...
     * ]
     * ~~~
     *
     * where
     *
     *  - attribute list: required, specifies the attributes array to be validated, for single attribute you can pass string;
     *  - validator type: required, specifies the validator to be used. It can be a built-in validator name,
     *    a method name of the model class, an anonymous function, or a validator class name.
     *  - on: optional, specifies the [[scenario|scenarios]] array when the validation
     *    rule can be applied. If this option is not set, the rule will apply to all scenarios.
     *  - additional name-value pairs can be specified to initialize the corresponding validator properties.
     *    Please refer to individual validator class API for possible properties.
     *
     * A validator can be either an object of a class extending [[Validator]], or a model class method
     * (called *inline validator*) that has the following signature:
     *
     * ~~~
     * // $params refers to validation parameters given in the rule
     * function validatorName($attribute, $params)
     * ~~~
     *
     * In the above `$attribute` refers to currently validated attribute name while `$params` contains an array of
     * validator configuration options such as `max` in case of `string` validator. Currently validate attribute value
     * can be accessed as `$this->[$attribute]`.
     *
     * Yii also provides a set of [[Validator::builtInValidators|built-in validators]].
     * They each has an alias name which can be used when specifying a validation rule.
     *
     * Below are some examples:
     *
     * ~~~
     * [
     *     // built-in "required" validator
     *     [['username', 'password'], 'required'],
     *     // built-in "string" validator customized with "min" and "max" properties
     *     ['username', 'string', 'min' => 3, 'max' => 12],
     *     // built-in "compare" validator that is used in "register" scenario only
     *     ['password', 'compare', 'compareAttribute' => 'password2', 'on' => 'register'],
     *     // an inline validator defined via the "authenticate()" method in the model class
     *     ['password', 'authenticate', 'on' => 'login'],
     *     // a validator of class "DateRangeValidator"
     *     ['dateRange', 'DateRangeValidator'],
     * ];
     * ~~~
     *
     * Note, in order to inherit rules defined in the parent class, a child class needs to
     * merge the parent rules with child rules using functions such as `array_merge()`.
     *
     * @return array validation rules
     * @see scenarios()
     */
    public function rules()
    {
        return [];
    }

    /**
     * Returns a list of scenarios and the corresponding active attributes.
     * An active attribute is one that is subject to validation in the current scenario.
     * The returned array should be in the following format:
     *
     * ```php
     * [
     *     'scenario1' => ['attribute11', 'attribute12', ...],
     *     'scenario2' => ['attribute21', 'attribute22', ...],
     *     ...
     * ]
     * ```
     *
     * By default, an active attribute is considered safe and can be massively assigned.
     * If an attribute should NOT be massively assigned (thus considered unsafe),
     * please prefix the attribute with an exclamation character (e.g. `'!rank'`).
     *
     * The default implementation of this method will return all scenarios found in the [[rules()]]
     * declaration. A special scenario named [[SCENARIO_DEFAULT]] will contain all attributes
     * found in the [[rules()]]. Each scenario will be associated with the attributes that
     * are being validated by the validation rules that apply to the scenario.
     *
     * @return array a list of scenarios and the corresponding active attributes.
     */
    public function scenarios()
    {
        $scenarios = [self::SCENARIO_DEFAULT => []];
        foreach ($this->getValidators() as $validator) {
            foreach ($validator->on as $scenario) {
                $scenarios[$scenario] = [];
            }
            foreach ($validator->except as $scenario) {
                $scenarios[$scenario] = [];
            }
        }
        $names = array_keys($scenarios);

        foreach ($this->getValidators() as $validator) {
            if (empty($validator->on) && empty($validator->except)) {
                foreach ($names as $name) {
                    foreach ($validator->attributes as $attribute) {
                        $scenarios[$name][$attribute] = true;
                    }
                }
            } elseif (empty($validator->on)) {
                foreach ($names as $name) {
                    if (!in_array($name, $validator->except, true)) {
                        foreach ($validator->attributes as $attribute) {
                            $scenarios[$name][$attribute] = true;
                        }
                    }
                }
            } else {
                foreach ($validator->on as $name) {
                    foreach ($validator->attributes as $attribute) {
                        $scenarios[$name][$attribute] = true;
                    }
                }
            }
        }

        foreach ($scenarios as $scenario => $attributes) {
            if (!empty($attributes)) {
                $scenarios[$scenario] = array_keys($attributes);
            }
        }

        return $scenarios;
    }

    /**
     * Returns the form name that this model class should use.
     *
     * The form name is mainly used by [[\yii\widgets\ActiveForm]] to determine how to name
     * the input fields for the attributes in a model. If the form name is "A" and an attribute
     * name is "b", then the corresponding input name would be "A[b]". If the form name is
     * an empty string, then the input name would be "b".
     *
     * By default, this method returns the model class name (without the namespace part)
     * as the form name. You may override it when the model is used in different forms.
     *
     * @return string the form name of this model class.
     */
    public function formName()
    {
        $reflector = new ReflectionClass($this);

        return $reflector->getShortName();
    }

    /**
     * Returns the list of attribute names.
     * By default, this method returns all public non-static properties of the class.
     * You may override this method to change the default behavior.
     * @return array list of attribute names.
     */
    public function attributes()
    {
        $class = new ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }

        return $names;
    }

    /**
     * Returns the attribute labels.
     *
     * Attribute labels are mainly used for display purpose. For example, given an attribute
     * `firstName`, we can declare a label `First Name` which is more user-friendly and can
     * be displayed to end users.
     *
     * By default an attribute label is generated using [[generateAttributeLabel()]].
     * This method allows you to explicitly specify attribute labels.
     *
     * Note, in order to inherit labels defined in the parent class, a child class needs to
     * merge the parent labels with child labels using functions such as `array_merge()`.
     *
     * @return array attribute labels (name => label)
     * @see generateAttributeLabel()
     */
    public function attributeLabels()
    {
        return [];
    }

    /**
     * Returns the attribute hints.
     *
     * Attribute hints are mainly used for display purpose. For example, given an attribute
     * `isPublic`, we can declare a hint `Whether the post should be visible for not logged in users`,
     * which provides user-friendly description of the attribute meaning and can be displayed to end users.
     *
     * Unlike label hint will not be generated, if its explicit declaration is omitted.
     *
     * Note, in order to inherit hints defined in the parent class, a child class needs to
     * merge the parent hints with child hints using functions such as `array_merge()`.
     *
     * @return array attribute hints (name => hint)
     * @since 2.0.4
     */
    public function attributeHints()
    {
        return [];
    }

    /**
     * Performs the data validation.
     *
     * This method executes the validation rules applicable to the current [[scenario]].
     * The following criteria are used to determine whether a rule is currently applicable:
     *
     * - the rule must be associated with the attributes relevant to the current scenario;
     * - the rules must be effective for the current scenario.
     *
     * This method will call [[beforeValidate()]] and [[afterValidate()]] before and
     * after the actual validation, respectively. If [[beforeValidate()]] returns false,
     * the validation will be cancelled and [[afterValidate()]] will not be called.
     *
     * Errors found during the validation can be retrieved via [[getErrors()]],
     * [[getFirstErrors()]] and [[getFirstError()]].
     *
     * @param array $attributeNames list of attribute names that should be validated.
     * If this parameter is empty, it means any attribute listed in the applicable
     * validation rules should be validated.
     * @param boolean $clearErrors whether to call [[clearErrors()]] before performing validation
     * @return boolean whether the validation is successful without any error.
     * @throws InvalidParamException if the current scenario is unknown.
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        if ($clearErrors) {
            $this->clearErrors();
        }

        if (!$this->beforeValidate()) {
            return false;
        }

        $scenarios = $this->scenarios();
        $scenario = $this->getScenario();
        if (!isset($scenarios[$scenario])) {
            throw new InvalidParamException("Unknown scenario: $scenario");
        }

        if ($attributeNames === null) {
            $attributeNames = $this->activeAttributes();
        }

        foreach ($this->getActiveValidators() as $validator) {
            $validator->validateAttributes($this, $attributeNames);
        }
        $this->afterValidate();

        return !$this->hasErrors();
    }

    /**
     * This method is invoked before validation starts.
     * The default implementation raises a `beforeValidate` event.
     * You may override this method to do preliminary checks before validation.
     * Make sure the parent implementation is invoked so that the event can be raised.
     * @return boolean whether the validation should be executed. Defaults to true.
     * If false is returned, the validation will stop and the model is considered invalid.
     */
    public function beforeValidate()
    {
        $event = new ModelEvent;
        $this->trigger(self::EVENT_BEFORE_VALIDATE, $event);

        return $event->isValid;
    }

    /**
     * This method is invoked after validation ends.
     * The default implementation raises an `afterValidate` event.
     * You may override this method to do postprocessing after validation.
     * Make sure the parent implementation is invoked so that the event can be raised.
     */
    public function afterValidate()
    {
        $this->trigger(self::EVENT_AFTER_VALIDATE);
    }

    /**
     * Returns all the validators declared in [[rules()]].
     *
     * This method differs from [[getActiveValidators()]] in that the latter
     * only returns the validators applicable to the current [[scenario]].
     *
     * Because this method returns an ArrayObject object, you may
     * manipulate it by inserting or removing validators (useful in model behaviors).
     * For example,
     *
     * ~~~
     * $model->validators[] = $newValidator;
     * ~~~
     *
     * @return ArrayObject|\yii\validators\Validator[] all the validators declared in the model.
     */
    public function getValidators()
    {
        if ($this->_validators === null) {
            $this->_validators = $this->createValidators();
        }
        return $this->_validators;
    }

    /**
     * Returns the validators applicable to the current [[scenario]].
     * @param string $attribute the name of the attribute whose applicable validators should be returned.
     * If this is null, the validators for ALL attributes in the model will be returned.
     * @return \yii\validators\Validator[] the validators applicable to the current [[scenario]].
     */
    public function getActiveValidators($attribute = null)
    {
        $validators = [];
        $scenario = $this->getScenario();
        foreach ($this->getValidators() as $validator) {
            if ($validator->isActive($scenario) && ($attribute === null || in_array($attribute, $validator->attributes, true))) {
                $validators[] = $validator;
            }
        }
        return $validators;
    }

    /**
     * Creates validator objects based on the validation rules specified in [[rules()]].
     * Unlike [[getValidators()]], each time this method is called, a new list of validators will be returned.
     * @return ArrayObject validators
     * @throws InvalidConfigException if any validation rule configuration is invalid
     */
    public function createValidators()
    {
        $validators = new ArrayObject;
        foreach ($this->rules() as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
            } elseif (is_array($rule) && isset($rule[0], $rule[1])) { // attributes, validator type
                $validator = Validator::createValidator($rule[1], $this, (array) $rule[0], array_slice($rule, 2));
                $validators->append($validator);
            } else {
                throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
            }
        }
        return $validators;
    }

    /**
     * Returns a value indicating whether the attribute is required.
     * This is determined by checking if the attribute is associated with a
     * [[\yii\validators\RequiredValidator|required]] validation rule in the
     * current [[scenario]].
     *
     * Note that when the validator has a conditional validation applied using
     * [[\yii\validators\RequiredValidator::$when|$when]] this method will return
     * `false` regardless of the `when` condition because it may be called be
     * before the model is loaded with data.
     *
     * @param string $attribute attribute name
     * @return boolean whether the attribute is required
     */
    public function isAttributeRequired($attribute)
    {
        foreach ($this->getActiveValidators($attribute) as $validator) {
            if ($validator instanceof RequiredValidator && $validator->when === null) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns a value indicating whether the attribute is safe for massive assignments.
     * @param string $attribute attribute name
     * @return boolean whether the attribute is safe for massive assignments
     * @see safeAttributes()
     */
    public function isAttributeSafe($attribute)
    {
        return in_array($attribute, $this->safeAttributes(), true);
    }

    /**
     * Returns a value indicating whether the attribute is active in the current scenario.
     * @param string $attribute attribute name
     * @return boolean whether the attribute is active in the current scenario
     * @see activeAttributes()
     */
    public function isAttributeActive($attribute)
    {
        return in_array($attribute, $this->activeAttributes(), true);
    }

    /**
     * Returns the text label for the specified attribute.
     * @param string $attribute the attribute name
     * @return string the attribute label
     * @see generateAttributeLabel()
     * @see attributeLabels()
     */
    public function getAttributeLabel($attribute)
    {
        $labels = $this->attributeLabels();
        return isset($labels[$attribute]) ? $labels[$attribute] : $this->generateAttributeLabel($attribute);
    }

    /**
     * Returns the text hint for the specified attribute.
     * @param string $attribute the attribute name
     * @return string the attribute hint
     * @see attributeHints()
     * @since 2.0.4
     */
    public function getAttributeHint($attribute)
    {
        $hints = $this->attributeHints();
        return isset($hints[$attribute]) ? $hints[$attribute] : '';
    }

    /**
     * Returns a value indicating whether there is any validation error.
     * @param string|null $attribute attribute name. Use null to check all attributes.
     * @return boolean whether there is any error.
     */
    public function hasErrors($attribute = null)
    {
        return $attribute === null ? !empty($this->_errors) : isset($this->_errors[$attribute]);
    }

    /**
     * Returns the errors for all attribute or a single attribute.
     * @param string $attribute attribute name. Use null to retrieve errors for all attributes.
     * @property array An array of errors for all attributes. Empty array is returned if no error.
     * The result is a two-dimensional array. See [[getErrors()]] for detailed description.
     * @return array errors for all attributes or the specified attribute. Empty array is returned if no error.
     * Note that when returning errors for all attributes, the result is a two-dimensional array, like the following:
     *
     * ~~~
     * [
     *     'username' => [
     *         'Username is required.',
     *         'Username must contain only word characters.',
     *     ],
     *     'email' => [
     *         'Email address is invalid.',
     *     ]
     * ]
     * ~~~
     *
     * @see getFirstErrors()
     * @see getFirstError()
     */
    public function getErrors($attribute = null)
    {
        if ($attribute === null) {
            return $this->_errors === null ? [] : $this->_errors;
        } else {
            return isset($this->_errors[$attribute]) ? $this->_errors[$attribute] : [];
        }
    }

    /**
     * Returns the first error of every attribute in the model.
     * @return array the first errors. The array keys are the attribute names, and the array
     * values are the corresponding error messages. An empty array will be returned if there is no error.
     * @see getErrors()
     * @see getFirstError()
     */
    public function getFirstErrors()
    {
        if (empty($this->_errors)) {
            return [];
        } else {
            $errors = [];
            foreach ($this->_errors as $name => $es) {
                if (!empty($es)) {
                    $errors[$name] = reset($es);
                }
            }

            return $errors;
        }
    }

    /**
     * Returns the first error of the specified attribute.
     * @param string $attribute attribute name.
     * @return string the error message. Null is returned if no error.
     * @see getErrors()
     * @see getFirstErrors()
     */
    public function getFirstError($attribute)
    {
        return isset($this->_errors[$attribute]) ? reset($this->_errors[$attribute]) : null;
    }

    /**
     * Adds a new error to the specified attribute.
     * @param string $attribute attribute name
     * @param string $error new error message
     */
    public function addError($attribute, $error = '')
    {
        $this->_errors[$attribute][] = $error;
    }

    /**
     * Adds a list of errors.
     * @param array $items a list of errors. The array keys must be attribute names.
     * The array values should be error messages. If an attribute has multiple errors,
     * these errors must be given in terms of an array.
     * You may use the result of [[getErrors()]] as the value for this parameter.
     * @since 2.0.2
     */
    public function addErrors(array $items)
    {
        foreach ($items as $attribute => $errors) {
            if (is_array($errors)) {
                foreach($errors as $error) {
                    $this->addError($attribute, $error);
                }
            } else {
                $this->addError($attribute, $errors);
            }
        }
    }

    /**
     * Removes errors for all attributes or a single attribute.
     * @param string $attribute attribute name. Use null to remove errors for all attribute.
     */
    public function clearErrors($attribute = null)
    {
        if ($attribute === null) {
            $this->_errors = [];
        } else {
            unset($this->_errors[$attribute]);
        }
    }

    /**
     * Generates a user friendly attribute label based on the give attribute name.
     * This is done by replacing underscores, dashes and dots with blanks and
     * changing the first letter of each word to upper case.
     * For example, 'department_name' or 'DepartmentName' will generate 'Department Name'.
     * @param string $name the column name
     * @return string the attribute label
     */
    public function generateAttributeLabel($name)
    {
        return Inflector::camel2words($name, true);
    }

    /**
     * Returns attribute values.
     * @param array $names list of attributes whose value needs to be returned.
     * Defaults to null, meaning all attributes listed in [[attributes()]] will be returned.
     * If it is an array, only the attributes in the array will be returned.
     * @param array $except list of attributes whose value should NOT be returned.
     * @return array attribute values (name => value).
     */
    public function getAttributes($names = null, $except = [])
    {
        $values = [];
        if ($names === null) {
            $names = $this->attributes();
        }
        foreach ($names as $name) {
            $values[$name] = $this->$name;
        }
        foreach ($except as $name) {
            unset($values[$name]);
        }

        return $values;
    }

    /**
     * Sets the attribute values in a massive way.
     * @param array $values attribute values (name => value) to be assigned to the model.
     * @param boolean $safeOnly whether the assignments should only be done to the safe attributes.
     * A safe attribute is one that is associated with a validation rule in the current [[scenario]].
     * @see safeAttributes()
     * @see attributes()
     */
    public function setAttributes($values, $safeOnly = true)
    {
        if (is_array($values)) {
            $attributes = array_flip($safeOnly ? $this->safeAttributes() : $this->attributes());
            foreach ($values as $name => $value) {
                if (isset($attributes[$name])) {
                    $this->$name = $value;
                } elseif ($safeOnly) {
                    $this->onUnsafeAttribute($name, $value);
                }
            }
        }
    }

    /**
     * This method is invoked when an unsafe attribute is being massively assigned.
     * The default implementation will log a warning message if YII_DEBUG is on.
     * It does nothing otherwise.
     * @param string $name the unsafe attribute name
     * @param mixed $value the attribute value
     */
    public function onUnsafeAttribute($name, $value)
    {
        if (YII_DEBUG) {
            Yii::trace("Failed to set unsafe attribute '$name' in '" . get_class($this) . "'.", __METHOD__);
        }
    }

    /**
     * Returns the scenario that this model is used in.
     *
     * Scenario affects how validation is performed and which attributes can
     * be massively assigned.
     *
     * @return string the scenario that this model is in. Defaults to [[SCENARIO_DEFAULT]].
     */
    public function getScenario()
    {
        return $this->_scenario;
    }

    /**
     * Sets the scenario for the model.
     * Note that this method does not check if the scenario exists or not.
     * The method [[validate()]] will perform this check.
     * @param string $value the scenario that this model is in.
     */
    public function setScenario($value)
    {
        $this->_scenario = $value;
    }

    /**
     * Returns the attribute names that are safe to be massively assigned in the current scenario.
     * @return string[] safe attribute names
     */
    public function safeAttributes()
    {
        $scenario = $this->getScenario();
        $scenarios = $this->scenarios();
        if (!isset($scenarios[$scenario])) {
            return [];
        }
        $attributes = [];
        foreach ($scenarios[$scenario] as $attribute) {
            if ($attribute[0] !== '!') {
                $attributes[] = $attribute;
            }
        }

        return $attributes;
    }

    /**
     * Returns the attribute names that are subject to validation in the current scenario.
     * @return string[] safe attribute names
     */
    public function activeAttributes()
    {
        $scenario = $this->getScenario();
        $scenarios = $this->scenarios();
        if (!isset($scenarios[$scenario])) {
            return [];
        }
        $attributes = $scenarios[$scenario];
        foreach ($attributes as $i => $attribute) {
            if ($attribute[0] === '!') {
                $attributes[$i] = substr($attribute, 1);
            }
        }

        return $attributes;
    }

    /**
     * Populates the model with the data from end user.
     * The data to be loaded is `$data[formName]`, where `formName` refers to the value of [[formName()]].
     * If [[formName()]] is empty, the whole `$data` array will be used to populate the model.
     * The data being populated is subject to the safety check by [[setAttributes()]].
     * @param array $data the data array. This is usually `$_POST` or `$_GET`, but can also be any valid array
     * supplied by end user.
     * @param string $formName the form name to be used for loading the data into the model.
     * If not set, [[formName()]] will be used.
     * @return boolean whether the model is successfully populated with some data.
     */
    public function load($data, $formName = null)
    {
        $scope = $formName === null ? $this->formName() : $formName;
        if ($scope === '' && !empty($data)) {
            $this->setAttributes($data);

            return true;
        } elseif (isset($data[$scope])) {
            $this->setAttributes($data[$scope]);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Populates a set of models with the data from end user.
     * This method is mainly used to collect tabular data input.
     * The data to be loaded for each model is `$data[formName][index]`, where `formName`
     * refers to the value of [[formName()]], and `index` the index of the model in the `$models` array.
     * If [[formName()]] is empty, `$data[index]` will be used to populate each model.
     * The data being populated to each model is subject to the safety check by [[setAttributes()]].
     * @param array $models the models to be populated. Note that all models should have the same class.
     * @param array $data the data array. This is usually `$_POST` or `$_GET`, but can also be any valid array
     * supplied by end user.
     * @param string $formName the form name to be used for loading the data into the models.
     * If not set, it will use the [[formName()]] value of the first model in `$models`.
     * This parameter is available since version 2.0.1.
     * @return boolean whether at least one of the models is successfully populated.
     */
    public static function loadMultiple($models, $data, $formName = null)
    {
        if ($formName === null) {
            /* @var $first Model */
            $first = reset($models);
            if ($first === false) {
                return false;
            }
            $formName = $first->formName();
        }

        $success = false;
        foreach ($models as $i => $model) {
            /* @var $model Model */
            if ($formName == '') {
                if (!empty($data[$i])) {
                    $model->load($data[$i], '');
                    $success = true;
                }
            } elseif (!empty($data[$formName][$i])) {
                $model->load($data[$formName][$i], '');
                $success = true;
            }
        }

        return $success;
    }

    /**
     * Validates multiple models.
     * This method will validate every model. The models being validated may
     * be of the same or different types.
     * @param array $models the models to be validated
     * @param array $attributeNames list of attribute names that should be validated.
     * If this parameter is empty, it means any attribute listed in the applicable
     * validation rules should be validated.
     * @return boolean whether all models are valid. False will be returned if one
     * or multiple models have validation error.
     */
    public static function validateMultiple($models, $attributeNames = null)
    {
        $valid = true;
        /* @var $model Model */
        foreach ($models as $model) {
            $valid = $model->validate($attributeNames) && $valid;
        }

        return $valid;
    }

    /**
     * Returns the list of fields that should be returned by default by [[toArray()]] when no specific fields are specified.
     *
     * A field is a named element in the returned array by [[toArray()]].
     *
     * This method should return an array of field names or field definitions.
     * If the former, the field name will be treated as an object property name whose value will be used
     * as the field value. If the latter, the array key should be the field name while the array value should be
     * the corresponding field definition which can be either an object property name or a PHP callable
     * returning the corresponding field value. The signature of the callable should be:
     *
     * ```php
     * function ($field, $model) {
     *     // return field value
     * }
     * ```
     *
     * For example, the following code declares four fields:
     *
     * - `email`: the field name is the same as the property name `email`;
     * - `firstName` and `lastName`: the field names are `firstName` and `lastName`, and their
     *   values are obtained from the `first_name` and `last_name` properties;
     * - `fullName`: the field name is `fullName`. Its value is obtained by concatenating `first_name`
     *   and `last_name`.
     *
     * ```php
     * return [
     *     'email',
     *     'firstName' => 'first_name',
     *     'lastName' => 'last_name',
     *     'fullName' => function ($model) {
     *         return $model->first_name . ' ' . $model->last_name;
     *     },
     * ];
     * ```
     *
     * In this method, you may also want to return different lists of fields based on some context
     * information. For example, depending on [[scenario]] or the privilege of the current application user,
     * you may return different sets of visible fields or filter out some fields.
     *
     * The default implementation of this method returns [[attributes()]] indexed by the same attribute names.
     *
     * @return array the list of field names or field definitions.
     * @see toArray()
     */
    public function fields()
    {
        $fields = $this->attributes();

        return array_combine($fields, $fields);
    }

    /**
     * Returns an iterator for traversing the attributes in the model.
     * This method is required by the interface IteratorAggregate.
     * @return ArrayIterator an iterator for traversing the items in the list.
     */
    public function getIterator()
    {
        $attributes = $this->getAttributes();
        return new ArrayIterator($attributes);
    }

    /**
     * Returns whether there is an element at the specified offset.
     * This method is required by the SPL interface `ArrayAccess`.
     * It is implicitly called when you use something like `isset($model[$offset])`.
     * @param mixed $offset the offset to check on
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->$offset !== null;
    }

    /**
     * Returns the element at the specified offset.
     * This method is required by the SPL interface `ArrayAccess`.
     * It is implicitly called when you use something like `$value = $model[$offset];`.
     * @param mixed $offset the offset to retrieve element.
     * @return mixed the element at the offset, null if no element is found at the offset
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Sets the element at the specified offset.
     * This method is required by the SPL interface `ArrayAccess`.
     * It is implicitly called when you use something like `$model[$offset] = $item;`.
     * @param integer $offset the offset to set element
     * @param mixed $item the element value
     */
    public function offsetSet($offset, $item)
    {
        $this->$offset = $item;
    }

    /**
     * Sets the element value at the specified offset to null.
     * This method is required by the SPL interface `ArrayAccess`.
     * It is implicitly called when you use something like `unset($model[$offset])`.
     * @param mixed $offset the offset to unset element
     */
    public function offsetUnset($offset)
    {
        $this->$offset = null;
    }
}








abstract class BaseActiveRecord extends Model implements ActiveRecordInterface
{
    /**
     * @event Event an event that is triggered when the record is initialized via [[init()]].
     */
    const EVENT_INIT = 'init';
    /**
     * @event Event an event that is triggered after the record is created and populated with query result.
     */
    const EVENT_AFTER_FIND = 'afterFind';
    /**
     * @event ModelEvent an event that is triggered before inserting a record.
     * You may set [[ModelEvent::isValid]] to be false to stop the insertion.
     */
    const EVENT_BEFORE_INSERT = 'beforeInsert';
    /**
     * @event Event an event that is triggered after a record is inserted.
     */
    const EVENT_AFTER_INSERT = 'afterInsert';
    /**
     * @event ModelEvent an event that is triggered before updating a record.
     * You may set [[ModelEvent::isValid]] to be false to stop the update.
     */
    const EVENT_BEFORE_UPDATE = 'beforeUpdate';
    /**
     * @event Event an event that is triggered after a record is updated.
     */
    const EVENT_AFTER_UPDATE = 'afterUpdate';
    /**
     * @event ModelEvent an event that is triggered before deleting a record.
     * You may set [[ModelEvent::isValid]] to be false to stop the deletion.
     */
    const EVENT_BEFORE_DELETE = 'beforeDelete';
    /**
     * @event Event an event that is triggered after a record is deleted.
     */
    const EVENT_AFTER_DELETE = 'afterDelete';

    /**
     * @var array attribute values indexed by attribute names
     */
    private $_attributes = [];
    /**
     * @var array|null old attribute values indexed by attribute names.
     * This is `null` if the record [[isNewRecord|is new]].
     */
    private $_oldAttributes;
    /**
     * @var array related models indexed by the relation names
     */
    private $_related = [];


    /**
     * @inheritdoc
     * @return static|null ActiveRecord instance matching the condition, or `null` if nothing matches.
     */
    public static function findOne($condition)
    {
        return static::findByCondition($condition)->one();
    }

    /**
     * @inheritdoc
     * @return static[] an array of ActiveRecord instances, or an empty array if nothing matches.
     */
    public static function findAll($condition)
    {
        return static::findByCondition($condition)->all();
    }

    /**
     * Finds ActiveRecord instance(s) by the given condition.
     * This method is internally called by [[findOne()]] and [[findAll()]].
     * @param mixed $condition please refer to [[findOne()]] for the explanation of this parameter
     * @return ActiveQueryInterface the newly created [[ActiveQueryInterface|ActiveQuery]] instance. 
     * @throws InvalidConfigException if there is no primary key defined
     * @internal
     */
    protected static function findByCondition($condition)
    {
        $query = static::find();

        if (!ArrayHelper::isAssociative($condition)) {
            // query by primary key
            $primaryKey = static::primaryKey();
            if (isset($primaryKey[0])) {
                $condition = [$primaryKey[0] => $condition];
            } else {
                throw new InvalidConfigException('"' . get_called_class() . '" must have a primary key.');
            }
        }

        return $query->andWhere($condition);
    }

    /**
     * Updates the whole table using the provided attribute values and conditions.
     * For example, to change the status to be 1 for all customers whose status is 2:
     *
     * ~~~
     * Customer::updateAll(['status' => 1], 'status = 2');
     * ~~~
     *
     * @param array $attributes attribute values (name-value pairs) to be saved into the table
     * @param string|array $condition the conditions that will be put in the WHERE part of the UPDATE SQL.
     * Please refer to [[Query::where()]] on how to specify this parameter.
     * @return integer the number of rows updated
     * @throws NotSupportedException if not overridden
     */
    public static function updateAll($attributes, $condition = '')
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }

    /**
     * Updates the whole table using the provided counter changes and conditions.
     * For example, to increment all customers' age by 1,
     *
     * ~~~
     * Customer::updateAllCounters(['age' => 1]);
     * ~~~
     *
     * @param array $counters the counters to be updated (attribute name => increment value).
     * Use negative values if you want to decrement the counters.
     * @param string|array $condition the conditions that will be put in the WHERE part of the UPDATE SQL.
     * Please refer to [[Query::where()]] on how to specify this parameter.
     * @return integer the number of rows updated
     * @throws NotSupportedException if not overrided
     */
    public static function updateAllCounters($counters, $condition = '')
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }

    /**
     * Deletes rows in the table using the provided conditions.
     * WARNING: If you do not specify any condition, this method will delete ALL rows in the table.
     *
     * For example, to delete all customers whose status is 3:
     *
     * ~~~
     * Customer::deleteAll('status = 3');
     * ~~~
     *
     * @param string|array $condition the conditions that will be put in the WHERE part of the DELETE SQL.
     * Please refer to [[Query::where()]] on how to specify this parameter.
     * @param array $params the parameters (name => value) to be bound to the query.
     * @return integer the number of rows deleted
     * @throws NotSupportedException if not overrided
     */
    public static function deleteAll($condition = '', $params = [])
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }

    /**
     * Returns the name of the column that stores the lock version for implementing optimistic locking.
     *
     * Optimistic locking allows multiple users to access the same record for edits and avoids
     * potential conflicts. In case when a user attempts to save the record upon some staled data
     * (because another user has modified the data), a [[StaleObjectException]] exception will be thrown,
     * and the update or deletion is skipped.
     *
     * Optimistic locking is only supported by [[update()]] and [[delete()]].
     *
     * To use Optimistic locking:
     *
     * 1. Create a column to store the version number of each row. The column type should be `BIGINT DEFAULT 0`.
     *    Override this method to return the name of this column.
     * 2. Add a `required` validation rule for the version column to ensure the version value is submitted.
     * 3. In the Web form that collects the user input, add a hidden field that stores
     *    the lock version of the recording being updated.
     * 4. In the controller action that does the data updating, try to catch the [[StaleObjectException]]
     *    and implement necessary business logic (e.g. merging the changes, prompting stated data)
     *    to resolve the conflict.
     *
     * @return string the column name that stores the lock version of a table row.
     * If null is returned (default implemented), optimistic locking will not be supported.
     */
    public function optimisticLock()
    {
        return null;
    }

    /**
     * PHP getter magic method.
     * This method is overridden so that attributes and related objects can be accessed like properties.
     *
     * @param string $name property name
     * @throws \yii\base\InvalidParamException if relation name is wrong
     * @return mixed property value
     * @see getAttribute()
     */
    public function __get($name)
    {
        if (isset($this->_attributes[$name]) || array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[$name];
        } elseif ($this->hasAttribute($name)) {
            return null;
        } else {
            if (isset($this->_related[$name]) || array_key_exists($name, $this->_related)) {
                return $this->_related[$name];
            }
            $value = parent::__get($name);
            if ($value instanceof ActiveQueryInterface) {
                return $this->_related[$name] = $value->findFor($name, $this);
            } else {
                return $value;
            }
        }
    }

    /**
     * PHP setter magic method.
     * This method is overridden so that AR attributes can be accessed like properties.
     * @param string $name property name
     * @param mixed $value property value
     */
    public function __set($name, $value)
    {
        if ($this->hasAttribute($name)) {
            $this->_attributes[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * Checks if a property value is null.
     * This method overrides the parent implementation by checking if the named attribute is null or not.
     * @param string $name the property name or the event name
     * @return boolean whether the property value is null
     */
    public function __isset($name)
    {
        try {
            return $this->__get($name) !== null;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Sets a component property to be null.
     * This method overrides the parent implementation by clearing
     * the specified attribute value.
     * @param string $name the property name or the event name
     */
    public function __unset($name)
    {
        if ($this->hasAttribute($name)) {
            unset($this->_attributes[$name]);
        } elseif (array_key_exists($name, $this->_related)) {
            unset($this->_related[$name]);
        } elseif ($this->getRelation($name, false) === null) {
            parent::__unset($name);
        }
    }

    /**
     * Declares a `has-one` relation.
     * The declaration is returned in terms of a relational [[ActiveQuery]] instance
     * through which the related record can be queried and retrieved back.
     *
     * A `has-one` relation means that there is at most one related record matching
     * the criteria set by this relation, e.g., a customer has one country.
     *
     * For example, to declare the `country` relation for `Customer` class, we can write
     * the following code in the `Customer` class:
     *
     * ~~~
     * public function getCountry()
     * {
     *     return $this->hasOne(Country::className(), ['id' => 'country_id']);
     * }
     * ~~~
     *
     * Note that in the above, the 'id' key in the `$link` parameter refers to an attribute name
     * in the related class `Country`, while the 'country_id' value refers to an attribute name
     * in the current AR class.
     *
     * Call methods declared in [[ActiveQuery]] to further customize the relation.
     *
     * @param string $class the class name of the related record
     * @param array $link the primary-foreign key constraint. The keys of the array refer to
     * the attributes of the record associated with the `$class` model, while the values of the
     * array refer to the corresponding attributes in **this** AR class.
     * @return ActiveQueryInterface the relational query object.
     */
    public function hasOne($class, $link)
    {
        /* @var $class ActiveRecordInterface */
        /* @var $query ActiveQuery */
        $query = $class::find();
        $query->primaryModel = $this;
        $query->link = $link;
        $query->multiple = false;
        return $query;
    }

    /**
     * Declares a `has-many` relation.
     * The declaration is returned in terms of a relational [[ActiveQuery]] instance
     * through which the related record can be queried and retrieved back.
     *
     * A `has-many` relation means that there are multiple related records matching
     * the criteria set by this relation, e.g., a customer has many orders.
     *
     * For example, to declare the `orders` relation for `Customer` class, we can write
     * the following code in the `Customer` class:
     *
     * ~~~
     * public function getOrders()
     * {
     *     return $this->hasMany(Order::className(), ['customer_id' => 'id']);
     * }
     * ~~~
     *
     * Note that in the above, the 'customer_id' key in the `$link` parameter refers to
     * an attribute name in the related class `Order`, while the 'id' value refers to
     * an attribute name in the current AR class.
     *
     * Call methods declared in [[ActiveQuery]] to further customize the relation.
     *
     * @param string $class the class name of the related record
     * @param array $link the primary-foreign key constraint. The keys of the array refer to
     * the attributes of the record associated with the `$class` model, while the values of the
     * array refer to the corresponding attributes in **this** AR class.
     * @return ActiveQueryInterface the relational query object.
     */
    public function hasMany($class, $link)
    {
        /* @var $class ActiveRecordInterface */
        /* @var $query ActiveQuery */
        $query = $class::find();
        $query->primaryModel = $this;
        $query->link = $link;
        $query->multiple = true;
        return $query;
    }

    /**
     * Populates the named relation with the related records.
     * Note that this method does not check if the relation exists or not.
     * @param string $name the relation name (case-sensitive)
     * @param ActiveRecordInterface|array|null $records the related records to be populated into the relation.
     */
    public function populateRelation($name, $records)
    {
        $this->_related[$name] = $records;
    }

    /**
     * Check whether the named relation has been populated with records.
     * @param string $name the relation name (case-sensitive)
     * @return boolean whether relation has been populated with records.
     */
    public function isRelationPopulated($name)
    {
        return array_key_exists($name, $this->_related);
    }

    /**
     * Returns all populated related records.
     * @return array an array of related records indexed by relation names.
     */
    public function getRelatedRecords()
    {
        return $this->_related;
    }

    /**
     * Returns a value indicating whether the model has an attribute with the specified name.
     * @param string $name the name of the attribute
     * @return boolean whether the model has an attribute with the specified name.
     */
    public function hasAttribute($name)
    {
        return isset($this->_attributes[$name]) || in_array($name, $this->attributes());
    }

    /**
     * Returns the named attribute value.
     * If this record is the result of a query and the attribute is not loaded,
     * null will be returned.
     * @param string $name the attribute name
     * @return mixed the attribute value. Null if the attribute is not set or does not exist.
     * @see hasAttribute()
     */
    public function getAttribute($name)
    {
        return isset($this->_attributes[$name]) ? $this->_attributes[$name] : null;
    }

    /**
     * Sets the named attribute value.
     * @param string $name the attribute name
     * @param mixed $value the attribute value.
     * @throws InvalidParamException if the named attribute does not exist.
     * @see hasAttribute()
     */
    public function setAttribute($name, $value)
    {
        if ($this->hasAttribute($name)) {
            $this->_attributes[$name] = $value;
        } else {
            throw new InvalidParamException(get_class($this) . ' has no attribute named "' . $name . '".');
        }
    }

    /**
     * Returns the old attribute values.
     * @return array the old attribute values (name-value pairs)
     */
    public function getOldAttributes()
    {
        return $this->_oldAttributes === null ? [] : $this->_oldAttributes;

    }

    /**
     * Sets the old attribute values.
     * All existing old attribute values will be discarded.
     * @param array|null $values old attribute values to be set.
     * If set to `null` this record is considered to be [[isNewRecord|new]].
     */
    public function setOldAttributes($values)
    {
        $this->_oldAttributes = $values;
    }

    /**
     * Returns the old value of the named attribute.
     * If this record is the result of a query and the attribute is not loaded,
     * null will be returned.
     * @param string $name the attribute name
     * @return mixed the old attribute value. Null if the attribute is not loaded before
     * or does not exist.
     * @see hasAttribute()
     */
    public function getOldAttribute($name)
    {
        return isset($this->_oldAttributes[$name]) ? $this->_oldAttributes[$name] : null;
    }

    /**
     * Sets the old value of the named attribute.
     * @param string $name the attribute name
     * @param mixed $value the old attribute value.
     * @throws InvalidParamException if the named attribute does not exist.
     * @see hasAttribute()
     */
    public function setOldAttribute($name, $value)
    {
        if (isset($this->_oldAttributes[$name]) || $this->hasAttribute($name)) {
            $this->_oldAttributes[$name] = $value;
        } else {
            throw new InvalidParamException(get_class($this) . ' has no attribute named "' . $name . '".');
        }
    }

    /**
     * Marks an attribute dirty.
     * This method may be called to force updating a record when calling [[update()]],
     * even if there is no change being made to the record.
     * @param string $name the attribute name
     */
    public function markAttributeDirty($name)
    {
        unset($this->_oldAttributes[$name]);
    }

    /**
     * Returns a value indicating whether the named attribute has been changed.
     * @param string $name the name of the attribute.
     * @param bool $identical whether the comparison of new and old value is made for
     * identical values using `===`, defaults to `true`. Otherwise `==` is used for comparison.
     * This parameter is available since version 2.0.4.
     * @return bool whether the attribute has been changed
     */
    public function isAttributeChanged($name, $identical = true)
    {
        if (isset($this->_attributes[$name], $this->_oldAttributes[$name])) {
            if ($identical) {
                return $this->_attributes[$name] !== $this->_oldAttributes[$name];
            } else {
                return $this->_attributes[$name] != $this->_oldAttributes[$name];
            }
        } else {
            return isset($this->_attributes[$name]) || isset($this->_oldAttributes[$name]);
        }
    }

    /**
     * Returns the attribute values that have been modified since they are loaded or saved most recently.
     * @param string[]|null $names the names of the attributes whose values may be returned if they are
     * changed recently. If null, [[attributes()]] will be used.
     * @return array the changed attribute values (name-value pairs)
     */
    public function getDirtyAttributes($names = null)
    {
        if ($names === null) {
            $names = $this->attributes();
        }
        $names = array_flip($names);
        $attributes = [];
        if ($this->_oldAttributes === null) {
            foreach ($this->_attributes as $name => $value) {
                if (isset($names[$name])) {
                    $attributes[$name] = $value;
                }
            }
        } else {
            foreach ($this->_attributes as $name => $value) {
                if (isset($names[$name]) && (!array_key_exists($name, $this->_oldAttributes) || $value !== $this->_oldAttributes[$name])) {
                    $attributes[$name] = $value;
                }
            }
        }
        return $attributes;
    }

    /**
     * Saves the current record.
     *
     * This method will call [[insert()]] when [[isNewRecord]] is true, or [[update()]]
     * when [[isNewRecord]] is false.
     *
     * For example, to save a customer record:
     *
     * ~~~
     * $customer = new Customer;  // or $customer = Customer::findOne($id);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->save();
     * ~~~
     *
     *
     * @param boolean $runValidation whether to perform validation before saving the record.
     * If the validation fails, the record will not be saved to database.
     * @param array $attributeNames list of attribute names that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     * @return boolean whether the saving succeeds
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->getIsNewRecord()) {
            return $this->insert($runValidation, $attributeNames);
        } else {
            return $this->update($runValidation, $attributeNames) !== false;
        }
    }

    /**
     * Saves the changes to this active record into the associated database table.
     *
     * This method performs the following steps in order:
     *
     * 1. call [[beforeValidate()]] when `$runValidation` is true. If validation
     *    fails, it will skip the rest of the steps;
     * 2. call [[afterValidate()]] when `$runValidation` is true.
     * 3. call [[beforeSave()]]. If the method returns false, it will skip the
     *    rest of the steps;
     * 4. save the record into database. If this fails, it will skip the rest of the steps;
     * 5. call [[afterSave()]];
     *
     * In the above step 1, 2, 3 and 5, events [[EVENT_BEFORE_VALIDATE]],
     * [[EVENT_BEFORE_UPDATE]], [[EVENT_AFTER_UPDATE]] and [[EVENT_AFTER_VALIDATE]]
     * will be raised by the corresponding methods.
     *
     * Only the [[dirtyAttributes|changed attribute values]] will be saved into database.
     *
     * For example, to update a customer record:
     *
     * ~~~
     * $customer = Customer::findOne($id);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->update();
     * ~~~
     *
     * Note that it is possible the update does not affect any row in the table.
     * In this case, this method will return 0. For this reason, you should use the following
     * code to check if update() is successful or not:
     *
     * ~~~
     * if ($this->update() !== false) {
     *     // update successful
     * } else {
     *     // update failed
     * }
     * ~~~
     *
     * @param boolean $runValidation whether to perform validation before saving the record.
     * If the validation fails, the record will not be inserted into the database.
     * @param array $attributeNames list of attribute names that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     * @return integer|boolean the number of rows affected, or false if validation fails
     * or [[beforeSave()]] stops the updating process.
     * @throws StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being updated is outdated.
     * @throws Exception in case update failed.
     */
    public function update($runValidation = true, $attributeNames = null)
    {
        if ($runValidation && !$this->validate($attributeNames)) {
            return false;
        }
        return $this->updateInternal($attributeNames);
    }

    /**
     * Updates the specified attributes.
     *
     * This method is a shortcut to [[update()]] when data validation is not needed
     * and only a small set attributes need to be updated.
     *
     * You may specify the attributes to be updated as name list or name-value pairs.
     * If the latter, the corresponding attribute values will be modified accordingly.
     * The method will then save the specified attributes into database.
     *
     * Note that this method will **not** perform data validation and will **not** trigger events.
     *
     * @param array $attributes the attributes (names or name-value pairs) to be updated
     * @return integer the number of rows affected.
     */
    public function updateAttributes($attributes)
    {
        $attrs = [];
        foreach ($attributes as $name => $value) {
            if (is_integer($name)) {
                $attrs[] = $value;
            } else {
                $this->$name = $value;
                $attrs[] = $name;
            }
        }

        $values = $this->getDirtyAttributes($attrs);
        if (empty($values)) {
            return 0;
        }

        $rows = $this->updateAll($values, $this->getOldPrimaryKey(true));

        foreach ($values as $name => $value) {
            $this->_oldAttributes[$name] = $this->_attributes[$name];
        }

        return $rows;
    }

    /**
     * @see update()
     * @param array $attributes attributes to update
     * @return integer number of rows updated
     * @throws StaleObjectException
     */
    protected function updateInternal($attributes = null)
    {
        if (!$this->beforeSave(false)) {
            return false;
        }
        $values = $this->getDirtyAttributes($attributes);
        if (empty($values)) {
            $this->afterSave(false, $values);
            return 0;
        }
        $condition = $this->getOldPrimaryKey(true);
        $lock = $this->optimisticLock();
        if ($lock !== null) {
            $values[$lock] = $this->$lock + 1;
            $condition[$lock] = $this->$lock;
        }
        // We do not check the return value of updateAll() because it's possible
        // that the UPDATE statement doesn't change anything and thus returns 0.
        $rows = $this->updateAll($values, $condition);

        if ($lock !== null && !$rows) {
            throw new StaleObjectException('The object being updated is outdated.');
        }

        $changedAttributes = [];
        foreach ($values as $name => $value) {
            $changedAttributes[$name] = isset($this->_oldAttributes[$name]) ? $this->_oldAttributes[$name] : null;
            $this->_oldAttributes[$name] = $value;
        }
        $this->afterSave(false, $changedAttributes);

        return $rows;
    }

    /**
     * Updates one or several counter columns for the current AR object.
     * Note that this method differs from [[updateAllCounters()]] in that it only
     * saves counters for the current AR object.
     *
     * An example usage is as follows:
     *
     * ~~~
     * $post = Post::findOne($id);
     * $post->updateCounters(['view_count' => 1]);
     * ~~~
     *
     * @param array $counters the counters to be updated (attribute name => increment value)
     * Use negative values if you want to decrement the counters.
     * @return boolean whether the saving is successful
     * @see updateAllCounters()
     */
    public function updateCounters($counters)
    {
        if ($this->updateAllCounters($counters, $this->getOldPrimaryKey(true)) > 0) {
            foreach ($counters as $name => $value) {
                if (!isset($this->_attributes[$name])) {
                    $this->_attributes[$name] = $value;
                } else {
                    $this->_attributes[$name] += $value;
                }
                $this->_oldAttributes[$name] = $this->_attributes[$name];
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Deletes the table row corresponding to this active record.
     *
     * This method performs the following steps in order:
     *
     * 1. call [[beforeDelete()]]. If the method returns false, it will skip the
     *    rest of the steps;
     * 2. delete the record from the database;
     * 3. call [[afterDelete()]].
     *
     * In the above step 1 and 3, events named [[EVENT_BEFORE_DELETE]] and [[EVENT_AFTER_DELETE]]
     * will be raised by the corresponding methods.
     *
     * @return integer|false the number of rows deleted, or false if the deletion is unsuccessful for some reason.
     * Note that it is possible the number of rows deleted is 0, even though the deletion execution is successful.
     * @throws StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being deleted is outdated.
     * @throws Exception in case delete failed.
     */
    public function delete()
    {
        $result = false;
        if ($this->beforeDelete()) {
            // we do not check the return value of deleteAll() because it's possible
            // the record is already deleted in the database and thus the method will return 0
            $condition = $this->getOldPrimaryKey(true);
            $lock = $this->optimisticLock();
            if ($lock !== null) {
                $condition[$lock] = $this->$lock;
            }
            $result = $this->deleteAll($condition);
            if ($lock !== null && !$result) {
                throw new StaleObjectException('The object being deleted is outdated.');
            }
            $this->_oldAttributes = null;
            $this->afterDelete();
        }

        return $result;
    }

    /**
     * Returns a value indicating whether the current record is new.
     * @return boolean whether the record is new and should be inserted when calling [[save()]].
     */
    public function getIsNewRecord()
    {
        return $this->_oldAttributes === null;
    }

    /**
     * Sets the value indicating whether the record is new.
     * @param boolean $value whether the record is new and should be inserted when calling [[save()]].
     * @see getIsNewRecord()
     */
    public function setIsNewRecord($value)
    {
        $this->_oldAttributes = $value ? null : $this->_attributes;
    }

    /**
     * Initializes the object.
     * This method is called at the end of the constructor.
     * The default implementation will trigger an [[EVENT_INIT]] event.
     * If you override this method, make sure you call the parent implementation at the end
     * to ensure triggering of the event.
     */
    public function init()
    {
        parent::init();
        $this->trigger(self::EVENT_INIT);
    }

    /**
     * This method is called when the AR object is created and populated with the query result.
     * The default implementation will trigger an [[EVENT_AFTER_FIND]] event.
     * When overriding this method, make sure you call the parent implementation to ensure the
     * event is triggered.
     */
    public function afterFind()
    {
        $this->trigger(self::EVENT_AFTER_FIND);
    }

    /**
     * This method is called at the beginning of inserting or updating a record.
     * The default implementation will trigger an [[EVENT_BEFORE_INSERT]] event when `$insert` is true,
     * or an [[EVENT_BEFORE_UPDATE]] event if `$insert` is false.
     * When overriding this method, make sure you call the parent implementation like the following:
     *
     * ~~~
     * public function beforeSave($insert)
     * {
     *     if (parent::beforeSave($insert)) {
     *         // ...custom code here...
     *         return true;
     *     } else {
     *         return false;
     *     }
     * }
     * ~~~
     *
     * @param boolean $insert whether this method called while inserting a record.
     * If false, it means the method is called while updating a record.
     * @return boolean whether the insertion or updating should continue.
     * If false, the insertion or updating will be cancelled.
     */
    public function beforeSave($insert)
    {
        $event = new ModelEvent;
        $this->trigger($insert ? self::EVENT_BEFORE_INSERT : self::EVENT_BEFORE_UPDATE, $event);

        return $event->isValid;
    }

    /**
     * This method is called at the end of inserting or updating a record.
     * The default implementation will trigger an [[EVENT_AFTER_INSERT]] event when `$insert` is true,
     * or an [[EVENT_AFTER_UPDATE]] event if `$insert` is false. The event class used is [[AfterSaveEvent]].
     * When overriding this method, make sure you call the parent implementation so that
     * the event is triggered.
     * @param boolean $insert whether this method called while inserting a record.
     * If false, it means the method is called while updating a record.
     * @param array $changedAttributes The old values of attributes that had changed and were saved.
     * You can use this parameter to take action based on the changes made for example send an email
     * when the password had changed or implement audit trail that tracks all the changes.
     * `$changedAttributes` gives you the old attribute values while the active record (`$this`) has
     * already the new, updated values.
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->trigger($insert ? self::EVENT_AFTER_INSERT : self::EVENT_AFTER_UPDATE, new AfterSaveEvent([
            'changedAttributes' => $changedAttributes
        ]));
    }

    /**
     * This method is invoked before deleting a record.
     * The default implementation raises the [[EVENT_BEFORE_DELETE]] event.
     * When overriding this method, make sure you call the parent implementation like the following:
     *
     * ~~~
     * public function beforeDelete()
     * {
     *     if (parent::beforeDelete()) {
     *         // ...custom code here...
     *         return true;
     *     } else {
     *         return false;
     *     }
     * }
     * ~~~
     *
     * @return boolean whether the record should be deleted. Defaults to true.
     */
    public function beforeDelete()
    {
        $event = new ModelEvent;
        $this->trigger(self::EVENT_BEFORE_DELETE, $event);

        return $event->isValid;
    }

    /**
     * This method is invoked after deleting a record.
     * The default implementation raises the [[EVENT_AFTER_DELETE]] event.
     * You may override this method to do postprocessing after the record is deleted.
     * Make sure you call the parent implementation so that the event is raised properly.
     */
    public function afterDelete()
    {
        $this->trigger(self::EVENT_AFTER_DELETE);
    }

    /**
     * Repopulates this active record with the latest data.
     * @return boolean whether the row still exists in the database. If true, the latest data
     * will be populated to this active record. Otherwise, this record will remain unchanged.
     */
    public function refresh()
    {
        /* @var $record BaseActiveRecord */
        $record = $this->findOne($this->getPrimaryKey(true));
        if ($record === null) {
            return false;
        }
        foreach ($this->attributes() as $name) {
            $this->_attributes[$name] = isset($record->_attributes[$name]) ? $record->_attributes[$name] : null;
        }
        $this->_oldAttributes = $this->_attributes;
        $this->_related = [];

        return true;
    }

    /**
     * Returns a value indicating whether the given active record is the same as the current one.
     * The comparison is made by comparing the table names and the primary key values of the two active records.
     * If one of the records [[isNewRecord|is new]] they are also considered not equal.
     * @param ActiveRecordInterface $record record to compare to
     * @return boolean whether the two active records refer to the same row in the same database table.
     */
    public function equals($record)
    {
        if ($this->getIsNewRecord() || $record->getIsNewRecord()) {
            return false;
        }

        return get_class($this) === get_class($record) && $this->getPrimaryKey() === $record->getPrimaryKey();
    }

    /**
     * Returns the primary key value(s).
     * @param boolean $asArray whether to return the primary key value as an array. If true,
     * the return value will be an array with column names as keys and column values as values.
     * Note that for composite primary keys, an array will always be returned regardless of this parameter value.
     * @property mixed The primary key value. An array (column name => column value) is returned if
     * the primary key is composite. A string is returned otherwise (null will be returned if
     * the key value is null).
     * @return mixed the primary key value. An array (column name => column value) is returned if the primary key
     * is composite or `$asArray` is true. A string is returned otherwise (null will be returned if
     * the key value is null).
     */
    public function getPrimaryKey($asArray = false)
    {
        $keys = $this->primaryKey();
        if (!$asArray && count($keys) === 1) {
            return isset($this->_attributes[$keys[0]]) ? $this->_attributes[$keys[0]] : null;
        } else {
            $values = [];
            foreach ($keys as $name) {
                $values[$name] = isset($this->_attributes[$name]) ? $this->_attributes[$name] : null;
            }

            return $values;
        }
    }

    /**
     * Returns the old primary key value(s).
     * This refers to the primary key value that is populated into the record
     * after executing a find method (e.g. find(), findOne()).
     * The value remains unchanged even if the primary key attribute is manually assigned with a different value.
     * @param boolean $asArray whether to return the primary key value as an array. If true,
     * the return value will be an array with column name as key and column value as value.
     * If this is false (default), a scalar value will be returned for non-composite primary key.
     * @property mixed The old primary key value. An array (column name => column value) is
     * returned if the primary key is composite. A string is returned otherwise (null will be
     * returned if the key value is null).
     * @return mixed the old primary key value. An array (column name => column value) is returned if the primary key
     * is composite or `$asArray` is true. A string is returned otherwise (null will be returned if
     * the key value is null).
     * @throws Exception if the AR model does not have a primary key
     */
    public function getOldPrimaryKey($asArray = false)
    {
        $keys = $this->primaryKey();
        if (empty($keys)) {
            throw new Exception(get_class($this) . ' does not have a primary key. You should either define a primary key for the corresponding table or override the primaryKey() method.');
        }
        if (!$asArray && count($keys) === 1) {
            return isset($this->_oldAttributes[$keys[0]]) ? $this->_oldAttributes[$keys[0]] : null;
        } else {
            $values = [];
            foreach ($keys as $name) {
                $values[$name] = isset($this->_oldAttributes[$name]) ? $this->_oldAttributes[$name] : null;
            }

            return $values;
        }
    }

    /**
     * Populates an active record object using a row of data from the database/storage.
     *
     * This is an internal method meant to be called to create active record objects after
     * fetching data from the database. It is mainly used by [[ActiveQuery]] to populate
     * the query results into active records.
     *
     * When calling this method manually you should call [[afterFind()]] on the created
     * record to trigger the [[EVENT_AFTER_FIND|afterFind Event]].
     *
     * @param BaseActiveRecord $record the record to be populated. In most cases this will be an instance
     * created by [[instantiate()]] beforehand.
     * @param array $row attribute values (name => value)
     */
    public static function populateRecord($record, $row)
    {
        $columns = array_flip($record->attributes());
        foreach ($row as $name => $value) {
            if (isset($columns[$name])) {
                $record->_attributes[$name] = $value;
            } elseif ($record->canSetProperty($name)) {
                $record->$name = $value;
            }
        }
        $record->_oldAttributes = $record->_attributes;
    }

    /**
     * Creates an active record instance.
     *
     * This method is called together with [[populateRecord()]] by [[ActiveQuery]].
     * It is not meant to be used for creating new records directly.
     *
     * You may override this method if the instance being created
     * depends on the row data to be populated into the record.
     * For example, by creating a record based on the value of a column,
     * you may implement the so-called single-table inheritance mapping.
     * @param array $row row data to be populated into the record.
     * @return static the newly created active record
     */
    public static function instantiate($row)
    {
        return new static;
    }

    /**
     * Returns whether there is an element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param mixed $offset the offset to check on
     * @return boolean whether there is an element at the specified offset.
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    /**
     * Returns the relation object with the specified name.
     * A relation is defined by a getter method which returns an [[ActiveQueryInterface]] object.
     * It can be declared in either the Active Record class itself or one of its behaviors.
     * @param string $name the relation name
     * @param boolean $throwException whether to throw exception if the relation does not exist.
     * @return ActiveQueryInterface|ActiveQuery the relational query object. If the relation does not exist
     * and `$throwException` is false, null will be returned.
     * @throws InvalidParamException if the named relation does not exist.
     */
    public function getRelation($name, $throwException = true)
    {
        $getter = 'get' . $name;
        try {
            // the relation could be defined in a behavior
            $relation = $this->$getter();
        } catch (UnknownMethodException $e) {
            if ($throwException) {
                throw new InvalidParamException(get_class($this) . ' has no relation named "' . $name . '".', 0, $e);
            } else {
                return null;
            }
        }
        if (!$relation instanceof ActiveQueryInterface) {
            if ($throwException) {
                throw new InvalidParamException(get_class($this) . ' has no relation named "' . $name . '".');
            } else {
                return null;
            }
        }

        if (method_exists($this, $getter)) {
            // relation name is case sensitive, trying to validate it when the relation is defined within this class
            $method = new \ReflectionMethod($this, $getter);
            $realName = lcfirst(substr($method->getName(), 3));
            if ($realName !== $name) {
                if ($throwException) {
                    throw new InvalidParamException('Relation names are case sensitive. ' . get_class($this) . " has a relation named \"$realName\" instead of \"$name\".");
                } else {
                    return null;
                }
            }
        }

        return $relation;
    }

    /**
     * Establishes the relationship between two models.
     *
     * The relationship is established by setting the foreign key value(s) in one model
     * to be the corresponding primary key value(s) in the other model.
     * The model with the foreign key will be saved into database without performing validation.
     *
     * If the relationship involves a junction table, a new row will be inserted into the
     * junction table which contains the primary key values from both models.
     *
     * Note that this method requires that the primary key value is not null.
     *
     * @param string $name the case sensitive name of the relationship
     * @param ActiveRecordInterface $model the model to be linked with the current one.
     * @param array $extraColumns additional column values to be saved into the junction table.
     * This parameter is only meaningful for a relationship involving a junction table
     * (i.e., a relation set with [[ActiveRelationTrait::via()]] or `[[ActiveQuery::viaTable()]]`.)
     * @throws InvalidCallException if the method is unable to link two models.
     */
    public function link($name, $model, $extraColumns = [])
    {
        $relation = $this->getRelation($name);

        if ($relation->via !== null) {
            if ($this->getIsNewRecord() || $model->getIsNewRecord()) {
                throw new InvalidCallException('Unable to link models: the models being linked cannot be newly created.');
            }
            if (is_array($relation->via)) {
                /* @var $viaRelation ActiveQuery */
                list($viaName, $viaRelation) = $relation->via;
                $viaClass = $viaRelation->modelClass;
                // unset $viaName so that it can be reloaded to reflect the change
                unset($this->_related[$viaName]);
            } else {
                $viaRelation = $relation->via;
                $viaTable = reset($relation->via->from);
            }
            $columns = [];
            foreach ($viaRelation->link as $a => $b) {
                $columns[$a] = $this->$b;
            }
            foreach ($relation->link as $a => $b) {
                $columns[$b] = $model->$a;
            }
            foreach ($extraColumns as $k => $v) {
                $columns[$k] = $v;
            }
            if (is_array($relation->via)) {
                /* @var $viaClass ActiveRecordInterface */
                /* @var $record ActiveRecordInterface */
                $record = new $viaClass();
                foreach ($columns as $column => $value) {
                    $record->$column = $value;
                }
                $record->insert(false);
            } else {
                /* @var $viaTable string */
                static::getDb()->createCommand()
                    ->insert($viaTable, $columns)->execute();
            }
        } else {
            $p1 = $model->isPrimaryKey(array_keys($relation->link));
            $p2 = $this->isPrimaryKey(array_values($relation->link));
            if ($p1 && $p2) {
                if ($this->getIsNewRecord() && $model->getIsNewRecord()) {
                    throw new InvalidCallException('Unable to link models: at most one model can be newly created.');
                } elseif ($this->getIsNewRecord()) {
                    $this->bindModels(array_flip($relation->link), $this, $model);
                } else {
                    $this->bindModels($relation->link, $model, $this);
                }
            } elseif ($p1) {
                $this->bindModels(array_flip($relation->link), $this, $model);
            } elseif ($p2) {
                $this->bindModels($relation->link, $model, $this);
            } else {
                throw new InvalidCallException('Unable to link models: the link defining the relation does not involve any primary key.');
            }
        }

        // update lazily loaded related objects
        if (!$relation->multiple) {
            $this->_related[$name] = $model;
        } elseif (isset($this->_related[$name])) {
            if ($relation->indexBy !== null) {
                $indexBy = $relation->indexBy;
                $this->_related[$name][$model->$indexBy] = $model;
            } else {
                $this->_related[$name][] = $model;
            }
        }
    }

    /**
     * Destroys the relationship between two models.
     *
     * The model with the foreign key of the relationship will be deleted if `$delete` is true.
     * Otherwise, the foreign key will be set null and the model will be saved without validation.
     *
     * @param string $name the case sensitive name of the relationship.
     * @param ActiveRecordInterface $model the model to be unlinked from the current one.
     * You have to make sure that the model is really related with the current model as this method
     * does not check this.
     * @param boolean $delete whether to delete the model that contains the foreign key.
     * If false, the model's foreign key will be set null and saved.
     * If true, the model containing the foreign key will be deleted.
     * @throws InvalidCallException if the models cannot be unlinked
     */
    public function unlink($name, $model, $delete = false)
    {
        $relation = $this->getRelation($name);

        if ($relation->via !== null) {
            if (is_array($relation->via)) {
                /* @var $viaRelation ActiveQuery */
                list($viaName, $viaRelation) = $relation->via;
                $viaClass = $viaRelation->modelClass;
                unset($this->_related[$viaName]);
            } else {
                $viaRelation = $relation->via;
                $viaTable = reset($relation->via->from);
            }
            $columns = [];
            foreach ($viaRelation->link as $a => $b) {
                $columns[$a] = $this->$b;
            }
            foreach ($relation->link as $a => $b) {
                $columns[$b] = $model->$a;
            }
            $nulls = [];
            foreach (array_keys($columns) as $a) {
                $nulls[$a] = null;
            }
            if (is_array($relation->via)) {
                /* @var $viaClass ActiveRecordInterface */
                if ($delete) {
                    $viaClass::deleteAll($columns);
                } else {
                    $viaClass::updateAll($nulls, $columns);
                }
            } else {
                /* @var $viaTable string */
                /* @var $command Command */
                $command = static::getDb()->createCommand();
                if ($delete) {
                    $command->delete($viaTable, $columns)->execute();
                } else {
                    $command->update($viaTable, $nulls, $columns)->execute();
                }
            }
        } else {
            $p1 = $model->isPrimaryKey(array_keys($relation->link));
            $p2 = $this->isPrimaryKey(array_values($relation->link));
            if ($p2) {
                foreach ($relation->link as $a => $b) {
                    $model->$a = null;
                }
                $delete ? $model->delete() : $model->save(false);
            } elseif ($p1) {
                foreach ($relation->link as $a => $b) {
                    if (is_array($this->$b)) { // relation via array valued attribute
                        if (($key = array_search($model->$a, $this->$b, false)) !== false) {
                            $values = $this->$b;
                            unset($values[$key]);
                            $this->$b = array_values($values);
                        }
                    } else {
                        $this->$b = null;
                    }
                }
                $delete ? $this->delete() : $this->save(false);
            } else {
                throw new InvalidCallException('Unable to unlink models: the link does not involve any primary key.');
            }
        }

        if (!$relation->multiple) {
            unset($this->_related[$name]);
        } elseif (isset($this->_related[$name])) {
            /* @var $b ActiveRecordInterface */
            foreach ($this->_related[$name] as $a => $b) {
                if ($model->getPrimaryKey() == $b->getPrimaryKey()) {
                    unset($this->_related[$name][$a]);
                }
            }
        }
    }

    /**
     * Destroys the relationship in current model.
     *
     * The model with the foreign key of the relationship will be deleted if `$delete` is true.
     * Otherwise, the foreign key will be set null and the model will be saved without validation.
     *
     * Note that to destroy the relationship without removing records make sure your keys can be set to null
     *
     * @param string $name the case sensitive name of the relationship.
     * @param boolean $delete whether to delete the model that contains the foreign key.
     */
    public function unlinkAll($name, $delete = false)
    {
        $relation = $this->getRelation($name);

        if ($relation->via !== null) {
            if (is_array($relation->via)) {
                /* @var $viaRelation ActiveQuery */
                list($viaName, $viaRelation) = $relation->via;
                $viaClass = $viaRelation->modelClass;
                unset($this->_related[$viaName]);
            } else {
                $viaRelation = $relation->via;
                $viaTable = reset($relation->via->from);
            }
            $condition = [];
            $nulls = [];
            foreach ($viaRelation->link as $a => $b) {
                $nulls[$a] = null;
                $condition[$a] = $this->$b;
            }
            if (!empty($viaRelation->where)) {
                $condition = ['and', $condition, $viaRelation->where];
            }
            if (is_array($relation->via)) {
                /* @var $viaClass ActiveRecordInterface */
                if ($delete) {
                    $viaClass::deleteAll($condition);
                } else {
                    $viaClass::updateAll($nulls, $condition);
                }
            } else {
                /* @var $viaTable string */
                /* @var $command Command */
                $command = static::getDb()->createCommand();
                if ($delete) {
                    $command->delete($viaTable, $condition)->execute();
                } else {
                    $command->update($viaTable, $nulls, $condition)->execute();
                }
            }
        } else {
            /* @var $relatedModel ActiveRecordInterface */
            $relatedModel = $relation->modelClass;
            if (!$delete && count($relation->link) == 1 && is_array($this->{$b = reset($relation->link)})) {
                // relation via array valued attribute
                $this->$b = [];
                $this->save(false);
            } else {
                $nulls = [];
                $condition = [];
                foreach ($relation->link as $a => $b) {
                    $nulls[$a] = null;
                    $condition[$a] = $this->$b;
                }
                if (!empty($relation->where)) {
                    $condition = ['and', $condition, $relation->where];
                }
                if ($delete) {
                    $relatedModel::deleteAll($condition);
                } else {
                    $relatedModel::updateAll($nulls, $condition);
                }
            }
        }

        unset($this->_related[$name]);
    }

    /**
     * @param array $link
     * @param ActiveRecordInterface $foreignModel
     * @param ActiveRecordInterface $primaryModel
     * @throws InvalidCallException
     */
    private function bindModels($link, $foreignModel, $primaryModel)
    {
        foreach ($link as $fk => $pk) {
            $value = $primaryModel->$pk;
            if ($value === null) {
                throw new InvalidCallException('Unable to link models: the primary key of ' . get_class($primaryModel) . ' is null.');
            }
            if (is_array($foreignModel->$fk)) { // relation via array valued attribute
                $foreignModel->$fk = array_merge($foreignModel->$fk, [$value]);
            } else {
                $foreignModel->$fk = $value;
            }
        }
        $foreignModel->save(false);
    }

    /**
     * Returns a value indicating whether the given set of attributes represents the primary key for this model
     * @param array $keys the set of attributes to check
     * @return boolean whether the given set of attributes represents the primary key for this model
     */
    public static function isPrimaryKey($keys)
    {
        $pks = static::primaryKey();
        if (count($keys) === count($pks)) {
            return count(array_intersect($keys, $pks)) === count($pks);
        } else {
            return false;
        }
    }

    /**
     * Returns the text label for the specified attribute.
     * If the attribute looks like `relatedModel.attribute`, then the attribute will be received from the related model.
     * @param string $attribute the attribute name
     * @return string the attribute label
     * @see generateAttributeLabel()
     * @see attributeLabels()
     */
    public function getAttributeLabel($attribute)
    {
        $labels = $this->attributeLabels();
        if (isset($labels[$attribute])) {
            return ($labels[$attribute]);
        } elseif (strpos($attribute, '.')) {
            $attributeParts = explode('.', $attribute);
            $neededAttribute = array_pop($attributeParts);

            $relatedModel = $this;
            foreach ($attributeParts as $relationName) {
                if ($relatedModel->isRelationPopulated($relationName) && $relatedModel->$relationName instanceof self) {
                    $relatedModel = $relatedModel->$relationName;
                } else {
                    try {
                        $relation = $relatedModel->getRelation($relationName);
                    } catch (InvalidParamException $e) {
                        return $this->generateAttributeLabel($attribute);
                    }
                    $relatedModel = new $relation->modelClass;
                }
            }

            $labels = $relatedModel->attributeLabels();
            if (isset($labels[$neededAttribute])) {
                return $labels[$neededAttribute];
            }
        }

        return $this->generateAttributeLabel($attribute);
    }

    /**
     * Returns the text hint for the specified attribute.
     * If the attribute looks like `relatedModel.attribute`, then the attribute will be received from the related model.
     * @param string $attribute the attribute name
     * @return string the attribute hint
     * @see attributeHints()
     * @since 2.0.4
     */
    public function getAttributeHint($attribute)
    {
        $hints = $this->attributeHints();
        if (isset($hints[$attribute])) {
            return ($hints[$attribute]);
        } elseif (strpos($attribute, '.')) {
            $attributeParts = explode('.', $attribute);
            $neededAttribute = array_pop($attributeParts);

            $relatedModel = $this;
            foreach ($attributeParts as $relationName) {
                if ($relatedModel->isRelationPopulated($relationName) && $relatedModel->$relationName instanceof self) {
                    $relatedModel = $relatedModel->$relationName;
                } else {
                    try {
                        $relation = $relatedModel->getRelation($relationName);
                    } catch (InvalidParamException $e) {
                        return '';
                    }
                    $relatedModel = new $relation->modelClass;
                }
            }

            $hints = $relatedModel->attributeHints();
            if (isset($hints[$neededAttribute])) {
                return $hints[$neededAttribute];
            }
        }
        return '';
    }

    /**
     * @inheritdoc
     *
     * The default implementation returns the names of the columns whose values have been populated into this record.
     */
    public function fields()
    {
        $fields = array_keys($this->_attributes);

        return array_combine($fields, $fields);
    }

    /**
     * @inheritdoc
     *
     * The default implementation returns the names of the relations that have been populated into this record.
     */
    public function extraFields()
    {
        $fields = array_keys($this->getRelatedRecords());

        return array_combine($fields, $fields);
    }

    /**
     * Sets the element value at the specified offset to null.
     * This method is required by the SPL interface `ArrayAccess`.
     * It is implicitly called when you use something like `unset($model[$offset])`.
     * @param mixed $offset the offset to unset element
     */
    public function offsetUnset($offset)
    {
        if (property_exists($this, $offset)) {
            $this->$offset = null;
        } else {
            unset($this->$offset);
        }
    }
}




class ActiveRecord extends BaseActiveRecord
{
    /**
     * The insert operation. This is mainly used when overriding [[transactions()]] to specify which operations are transactional.
     */
    const OP_INSERT = 0x01;
    /**
     * The update operation. This is mainly used when overriding [[transactions()]] to specify which operations are transactional.
     */
    const OP_UPDATE = 0x02;
    /**
     * The delete operation. This is mainly used when overriding [[transactions()]] to specify which operations are transactional.
     */
    const OP_DELETE = 0x04;
    /**
     * All three operations: insert, update, delete.
     * This is a shortcut of the expression: OP_INSERT | OP_UPDATE | OP_DELETE.
     */
    const OP_ALL = 0x07;


    /**
     * Loads default values from database table schema
     *
     * You may call this method to load default values after creating a new instance:
     *
     * ```php
     * // class Customer extends \yii\db\ActiveRecord
     * $customer = new Customer();
     * $customer->loadDefaultValues();
     * ```
     *
     * @param boolean $skipIfSet whether existing value should be preserved.
     * This will only set defaults for attributes that are `null`.
     * @return static the model instance itself.
     */
    public function loadDefaultValues($skipIfSet = true)
    {
        foreach ($this->getTableSchema()->columns as $column) {
            if ($column->defaultValue !== null && (!$skipIfSet || $this->{$column->name} === null)) {
                $this->{$column->name} = $column->defaultValue;
            }
        }
        return $this;
    }

    /**
     * Returns the database connection used by this AR class.
     * By default, the "db" application component is used as the database connection.
     * You may override this method if you want to use a different database connection.
     * @return Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->getDb();
    }

    /**
     * Creates an [[ActiveQuery]] instance with a given SQL statement.
     *
     * Note that because the SQL statement is already specified, calling additional
     * query modification methods (such as `where()`, `order()`) on the created [[ActiveQuery]]
     * instance will have no effect. However, calling `with()`, `asArray()` or `indexBy()` is
     * still fine.
     *
     * Below is an example:
     *
     * ~~~
     * $customers = Customer::findBySql('SELECT * FROM customer')->all();
     * ~~~
     *
     * @param string $sql the SQL statement to be executed
     * @param array $params parameters to be bound to the SQL statement during execution.
     * @return ActiveQuery the newly created [[ActiveQuery]] instance
     */
    public static function findBySql($sql, $params = [])
    {
        $query = static::find();
        $query->sql = $sql;

        return $query->params($params);
    }

    /**
     * Finds ActiveRecord instance(s) by the given condition.
     * This method is internally called by [[findOne()]] and [[findAll()]].
     * @param mixed $condition please refer to [[findOne()]] for the explanation of this parameter
     * @return ActiveQueryInterface the newly created [[ActiveQueryInterface|ActiveQuery]] instance. 
     * @throws InvalidConfigException if there is no primary key defined
     * @internal
     */
    protected static function findByCondition($condition)
    {
        $query = static::find();

        if (!ArrayHelper::isAssociative($condition)) {
            // query by primary key
            $primaryKey = static::primaryKey();
            if (isset($primaryKey[0])) {
                $pk = $primaryKey[0];
                if (!empty($query->join) || !empty($query->joinWith)) {
                    $pk = static::tableName() . '.' . $pk;
                }
                $condition = [$pk => $condition];
            } else {
                throw new InvalidConfigException('"' . get_called_class() . '" must have a primary key.');
            }
        }

        return $query->andWhere($condition);
    }

    /**
     * Updates the whole table using the provided attribute values and conditions.
     * For example, to change the status to be 1 for all customers whose status is 2:
     *
     * ~~~
     * Customer::updateAll(['status' => 1], 'status = 2');
     * ~~~
     *
     * @param array $attributes attribute values (name-value pairs) to be saved into the table
     * @param string|array $condition the conditions that will be put in the WHERE part of the UPDATE SQL.
     * Please refer to [[Query::where()]] on how to specify this parameter.
     * @param array $params the parameters (name => value) to be bound to the query.
     * @return integer the number of rows updated
     */
    public static function updateAll($attributes, $condition = '', $params = [])
    {
        $command = static::getDb()->createCommand();
        $command->update(static::tableName(), $attributes, $condition, $params);

        return $command->execute();
    }

    /**
     * Updates the whole table using the provided counter changes and conditions.
     * For example, to increment all customers' age by 1,
     *
     * ~~~
     * Customer::updateAllCounters(['age' => 1]);
     * ~~~
     *
     * @param array $counters the counters to be updated (attribute name => increment value).
     * Use negative values if you want to decrement the counters.
     * @param string|array $condition the conditions that will be put in the WHERE part of the UPDATE SQL.
     * Please refer to [[Query::where()]] on how to specify this parameter.
     * @param array $params the parameters (name => value) to be bound to the query.
     * Do not name the parameters as `:bp0`, `:bp1`, etc., because they are used internally by this method.
     * @return integer the number of rows updated
     */
    public static function updateAllCounters($counters, $condition = '', $params = [])
    {
        $n = 0;
        foreach ($counters as $name => $value) {
            $counters[$name] = new Expression("[[$name]]+:bp{$n}", [":bp{$n}" => $value]);
            $n++;
        }
        $command = static::getDb()->createCommand();
        $command->update(static::tableName(), $counters, $condition, $params);

        return $command->execute();
    }

    /**
     * Deletes rows in the table using the provided conditions.
     * WARNING: If you do not specify any condition, this method will delete ALL rows in the table.
     *
     * For example, to delete all customers whose status is 3:
     *
     * ~~~
     * Customer::deleteAll('status = 3');
     * ~~~
     *
     * @param string|array $condition the conditions that will be put in the WHERE part of the DELETE SQL.
     * Please refer to [[Query::where()]] on how to specify this parameter.
     * @param array $params the parameters (name => value) to be bound to the query.
     * @return integer the number of rows deleted
     */
    public static function deleteAll($condition = '', $params = [])
    {
        $command = static::getDb()->createCommand();
        $command->delete(static::tableName(), $condition, $params);

        return $command->execute();
    }

    /**
     * @inheritdoc
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        return Yii::createObject(ActiveQuery::className(), [get_called_class()]);
    }

    /**
     * Declares the name of the database table associated with this AR class.
     * By default this method returns the class name as the table name by calling [[Inflector::camel2id()]]
     * with prefix [[Connection::tablePrefix]]. For example if [[Connection::tablePrefix]] is 'tbl_',
     * 'Customer' becomes 'tbl_customer', and 'OrderItem' becomes 'tbl_order_item'. You may override this method
     * if the table is not named after this convention.
     * @return string the table name
     */
    public static function tableName()
    {
        return '{{%' . Inflector::camel2id(StringHelper::basename(get_called_class()), '_') . '}}';
    }

    /**
     * Returns the schema information of the DB table associated with this AR class.
     * @return TableSchema the schema information of the DB table associated with this AR class.
     * @throws InvalidConfigException if the table for the AR class does not exist.
     */
    public static function getTableSchema()
    {
        $schema = static::getDb()->getSchema()->getTableSchema(static::tableName());
        if ($schema !== null) {
            return $schema;
        } else {
            throw new InvalidConfigException("The table does not exist: " . static::tableName());
        }
    }

    /**
     * Returns the primary key name(s) for this AR class.
     * The default implementation will return the primary key(s) as declared
     * in the DB table that is associated with this AR class.
     *
     * If the DB table does not declare any primary key, you should override
     * this method to return the attributes that you want to use as primary keys
     * for this AR class.
     *
     * Note that an array should be returned even for a table with single primary key.
     *
     * @return string[] the primary keys of the associated database table.
     */
    public static function primaryKey()
    {
        return static::getTableSchema()->primaryKey;
    }

    /**
     * Returns the list of all attribute names of the model.
     * The default implementation will return all column names of the table associated with this AR class.
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return array_keys(static::getTableSchema()->columns);
    }

    /**
     * Declares which DB operations should be performed within a transaction in different scenarios.
     * The supported DB operations are: [[OP_INSERT]], [[OP_UPDATE]] and [[OP_DELETE]],
     * which correspond to the [[insert()]], [[update()]] and [[delete()]] methods, respectively.
     * By default, these methods are NOT enclosed in a DB transaction.
     *
     * In some scenarios, to ensure data consistency, you may want to enclose some or all of them
     * in transactions. You can do so by overriding this method and returning the operations
     * that need to be transactional. For example,
     *
     * ~~~
     * return [
     *     'admin' => self::OP_INSERT,
     *     'api' => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE,
     *     // the above is equivalent to the following:
     *     // 'api' => self::OP_ALL,
     *
     * ];
     * ~~~
     *
     * The above declaration specifies that in the "admin" scenario, the insert operation ([[insert()]])
     * should be done in a transaction; and in the "api" scenario, all the operations should be done
     * in a transaction.
     *
     * @return array the declarations of transactional operations. The array keys are scenarios names,
     * and the array values are the corresponding transaction operations.
     */
    public function transactions()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function populateRecord($record, $row)
    {
        $columns = static::getTableSchema()->columns;
        foreach ($row as $name => $value) {
            if (isset($columns[$name])) {
                $row[$name] = $columns[$name]->phpTypecast($value);
            }
        }
        parent::populateRecord($record, $row);
    }

    /**
     * Inserts a row into the associated database table using the attribute values of this record.
     *
     * This method performs the following steps in order:
     *
     * 1. call [[beforeValidate()]] when `$runValidation` is true. If validation
     *    fails, it will skip the rest of the steps;
     * 2. call [[afterValidate()]] when `$runValidation` is true.
     * 3. call [[beforeSave()]]. If the method returns false, it will skip the
     *    rest of the steps;
     * 4. insert the record into database. If this fails, it will skip the rest of the steps;
     * 5. call [[afterSave()]];
     *
     * In the above step 1, 2, 3 and 5, events [[EVENT_BEFORE_VALIDATE]],
     * [[EVENT_BEFORE_INSERT]], [[EVENT_AFTER_INSERT]] and [[EVENT_AFTER_VALIDATE]]
     * will be raised by the corresponding methods.
     *
     * Only the [[dirtyAttributes|changed attribute values]] will be inserted into database.
     *
     * If the table's primary key is auto-incremental and is null during insertion,
     * it will be populated with the actual value after insertion.
     *
     * For example, to insert a customer record:
     *
     * ~~~
     * $customer = new Customer;
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->insert();
     * ~~~
     *
     * @param boolean $runValidation whether to perform validation before saving the record.
     * If the validation fails, the record will not be inserted into the database.
     * @param array $attributes list of attributes that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     * @return boolean whether the attributes are valid and the record is inserted successfully.
     * @throws \Exception in case insert failed.
     */
    public function insert($runValidation = true, $attributes = null)
    {
        if ($runValidation && !$this->validate($attributes)) {
            Yii::info('Model not inserted due to validation error.', __METHOD__);
            return false;
        }

        if (!$this->isTransactional(self::OP_INSERT)) {
            return $this->insertInternal($attributes);
        }

        $transaction = static::getDb()->beginTransaction();
        try {
            $result = $this->insertInternal($attributes);
            if ($result === false) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Inserts an ActiveRecord into DB without considering transaction.
     * @param array $attributes list of attributes that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     * @return boolean whether the record is inserted successfully.
     */
    protected function insertInternal($attributes = null)
    {
        if (!$this->beforeSave(true)) {
            return false;
        }
        $values = $this->getDirtyAttributes($attributes);
        if (empty($values)) {
            foreach ($this->getPrimaryKey(true) as $key => $value) {
                $values[$key] = $value;
            }
        }
        if (($primaryKeys = static::getDb()->schema->insert($this->tableName(), $values)) === false) {
            return false;
        }
        foreach ($primaryKeys as $name => $value) {
            $id = $this->getTableSchema()->columns[$name]->phpTypecast($value);
            $this->setAttribute($name, $id);
            $values[$name] = $id;
        }

        $changedAttributes = array_fill_keys(array_keys($values), null);
        $this->setOldAttributes($values);
        $this->afterSave(true, $changedAttributes);

        return true;
    }

    /**
     * Saves the changes to this active record into the associated database table.
     *
     * This method performs the following steps in order:
     *
     * 1. call [[beforeValidate()]] when `$runValidation` is true. If validation
     *    fails, it will skip the rest of the steps;
     * 2. call [[afterValidate()]] when `$runValidation` is true.
     * 3. call [[beforeSave()]]. If the method returns false, it will skip the
     *    rest of the steps;
     * 4. save the record into database. If this fails, it will skip the rest of the steps;
     * 5. call [[afterSave()]];
     *
     * In the above step 1, 2, 3 and 5, events [[EVENT_BEFORE_VALIDATE]],
     * [[EVENT_BEFORE_UPDATE]], [[EVENT_AFTER_UPDATE]] and [[EVENT_AFTER_VALIDATE]]
     * will be raised by the corresponding methods.
     *
     * Only the [[dirtyAttributes|changed attribute values]] will be saved into database.
     *
     * For example, to update a customer record:
     *
     * ~~~
     * $customer = Customer::findOne($id);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->update();
     * ~~~
     *
     * Note that it is possible the update does not affect any row in the table.
     * In this case, this method will return 0. For this reason, you should use the following
     * code to check if update() is successful or not:
     *
     * ~~~
     * if ($this->update() !== false) {
     *     // update successful
     * } else {
     *     // update failed
     * }
     * ~~~
     *
     * @param boolean $runValidation whether to perform validation before saving the record.
     * If the validation fails, the record will not be inserted into the database.
     * @param array $attributeNames list of attributes that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     * @return integer|boolean the number of rows affected, or false if validation fails
     * or [[beforeSave()]] stops the updating process.
     * @throws StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being updated is outdated.
     * @throws \Exception in case update failed.
     */
    public function update($runValidation = true, $attributeNames = null)
    {
        if ($runValidation && !$this->validate($attributeNames)) {
            Yii::info('Model not updated due to validation error.', __METHOD__);
            return false;
        }

        if (!$this->isTransactional(self::OP_UPDATE)) {
            return $this->updateInternal($attributeNames);
        }

        $transaction = static::getDb()->beginTransaction();
        try {
            $result = $this->updateInternal($attributeNames);
            if ($result === false) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Deletes the table row corresponding to this active record.
     *
     * This method performs the following steps in order:
     *
     * 1. call [[beforeDelete()]]. If the method returns false, it will skip the
     *    rest of the steps;
     * 2. delete the record from the database;
     * 3. call [[afterDelete()]].
     *
     * In the above step 1 and 3, events named [[EVENT_BEFORE_DELETE]] and [[EVENT_AFTER_DELETE]]
     * will be raised by the corresponding methods.
     *
     * @return integer|false the number of rows deleted, or false if the deletion is unsuccessful for some reason.
     * Note that it is possible the number of rows deleted is 0, even though the deletion execution is successful.
     * @throws StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being deleted is outdated.
     * @throws \Exception in case delete failed.
     */
    public function delete()
    {
        if (!$this->isTransactional(self::OP_DELETE)) {
            return $this->deleteInternal();
        }

        $transaction = static::getDb()->beginTransaction();
        try {
            $result = $this->deleteInternal();
            if ($result === false) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Deletes an ActiveRecord without considering transaction.
     * @return integer|false the number of rows deleted, or false if the deletion is unsuccessful for some reason.
     * Note that it is possible the number of rows deleted is 0, even though the deletion execution is successful.
     * @throws StaleObjectException
     */
    protected function deleteInternal()
    {
        if (!$this->beforeDelete()) {
            return false;
        }

        // we do not check the return value of deleteAll() because it's possible
        // the record is already deleted in the database and thus the method will return 0
        $condition = $this->getOldPrimaryKey(true);
        $lock = $this->optimisticLock();
        if ($lock !== null) {
            $condition[$lock] = $this->$lock;
        }
        $result = $this->deleteAll($condition);
        if ($lock !== null && !$result) {
            throw new StaleObjectException('The object being deleted is outdated.');
        }
        $this->setOldAttributes(null);
        $this->afterDelete();

        return $result;
    }

    /**
     * Returns a value indicating whether the given active record is the same as the current one.
     * The comparison is made by comparing the table names and the primary key values of the two active records.
     * If one of the records [[isNewRecord|is new]] they are also considered not equal.
     * @param ActiveRecord $record record to compare to
     * @return boolean whether the two active records refer to the same row in the same database table.
     */
    public function equals($record)
    {
        if ($this->isNewRecord || $record->isNewRecord) {
            return false;
        }

        return $this->tableName() === $record->tableName() && $this->getPrimaryKey() === $record->getPrimaryKey();
    }

    /**
     * Returns a value indicating whether the specified operation is transactional in the current [[scenario]].
     * @param integer $operation the operation to check. Possible values are [[OP_INSERT]], [[OP_UPDATE]] and [[OP_DELETE]].
     * @return boolean whether the specified operation is transactional in the current [[scenario]].
     */
    public function isTransactional($operation)
    {
        $scenario = $this->getScenario();
        $transactions = $this->transactions();

        return isset($transactions[$scenario]) && ($transactions[$scenario] & $operation);
    }
}












class Mcuser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbl_mcuser}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userlogin', 'userpassword'], 'required'],
            [['states', 'signin_time', 'update_time', 'create_time'], 'integer'],
            [['userlogin', 'userpassword', 'group', 'username', 'usertel', 'weixin', 'weibo'], 'string', 'max' => 32],
            [['QQ'], 'string', 'max' => 16],
            [['ip'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userlogin' => 'Userlogin',
            'userpassword' => 'Userpassword',
            'group' => 'Group',
            'username' => 'Username',
            'usertel' => 'Usertel',
            'QQ' => 'Qq',
            'weixin' => 'Weixin',
            'weibo' => 'Weibo',
            'ip' => 'Ip',
            'states' => 'States',
            'signin_time' => 'Signin Time',
            'update_time' => 'Update Time',
            'create_time' => 'Create Time',
        ];
    }
}
