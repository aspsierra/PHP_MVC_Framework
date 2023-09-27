<?php

namespace aspsierra\phpBasicFw\core;

abstract class Model
{
    //Validation rules created as example
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';

    //Array with the posible errors for each attribute
    public array $errors = [];

    /**
     * Labels for the diferent attribute
     * @return  array 
     */
    public function labels(): array
    {
        return [];
    }

    public function getLabel($attribute){
        return $this->labels()[$attribute] ?? $attribute;
    }

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key))
                $this->$key = $value;
        }
    }

    /**
     * Especify the validation rules to apply to each attribute
     * @return  array 
     */
    abstract public function rules(): array;

    /**
     * Validation for each rule defined as constant
     * @return
     */
    public function validate()
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->$attribute;
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($ruleName)) {
                    $ruleName = $rule[0];
                }

                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addErrorForRue($attribute, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addErrorForRue($attribute, self::RULE_EMAIL);
                }
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addErrorForRue($attribute, self::RULE_MIN, $rule);
                }
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addErrorForRue($attribute, self::RULE_MAX, $rule);
                }
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $rule['match'] = $this->getLabel($rule['match']);
                    $this->addErrorForRue($attribute, self::RULE_MATCH, $rule);
                }
                if ($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttr = $attribute = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr");
                    $statement->bindValue(':attr', $value);
                    $statement->execute();
                    if ($statement->fetchObject()) {
                        $this->addErrorForRue($attribute, self::RULE_UNIQUE, ['field' => $this->getLabel($attribute)]);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Add an error to the array for a specific rule with an specific message for each one
     * @param   string  $attribute  attribute associated to the error
     * @param   string  $rule       rule applied
     * @param   array   $params     parameters to assign to the message
     */
    private function addErrorForRue(string $attribute, string $rule, $params = [])
    {
        $message = $this->errorMessages()[$rule] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    /**
     * Add a not rule assigned error
     *
     * @param   string  $attribute  attribute associated to the error
     * @param   string  $message    Message to display
     */
    public function addError(string $attribute, string $message){
        $this->errors[$attribute][] = $message;
    }

    /**
     * Message for each ruled error
     * @return  array 
     */
    public function errorMessages()
    {
        return [
            self::RULE_REQUIRED => 'Field required',
            self::RULE_EMAIL => 'This field must be a valid email address',
            self::RULE_MIN => 'Minimum length is {min}',
            self::RULE_MAX => 'Max length id {max}',
            self::RULE_MATCH => 'Field must match {match}',
            self::RULE_UNIQUE => 'This {field} already exists'
        ];
    }

    /**
     * Check if exists an error to a specific attribute
     * @param   string  $attribute  
     * @return  mixed              
     */
    public function hasError($attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    /**
     * Get the first error for each attribute
     * @param   string  $attribute  
     * @return  mixed              
     */
    public function getFirstError($attribute)
    {
        return $this->errors[$attribute][0] ?? false;
    }
}
