<?php

namespace Cake;

class CakeErrorHandler
{

    private $errors = [];
    private $messages = [];

    private $log = [];

    public function __construct()
    {

        $this->setRuleMessages([
            'alphanumeric'  => 'The {field} field must contain only numbers and letters.',
            'collection'    => 'The {field} field must be {arguments}.',
            'date'          => 'The {field} field must match the {arguments} format.',
            'email'         => 'The {field} field is not a valid email address.',
            'hexcolor'      => 'The {field} field must be a valid hex color.',
            'ip'            => 'The {field} field must be a valid I.P. address.',
            'match'         => 'The {field} field must match to the {arguments} field.',
            'maxlength'     => 'The {field} field must be a maximum of {arguments} length.',
            'minlength'     => 'The {field} field must be a minimum of {arguments} length.',
            'required'      => 'The {field} field is required.',
            'url'           => 'The {field} field must be a valid URL.'
        ]);

    }

    public function setRuleMessages($messages)
    {

        foreach($messages as $rule => $message) {
            $this->setRuleMessage($rule, $message);
        }

    }

    public function setRuleMessage($rule, $message)
    {
        $this->messages[$rule] = $message;
    }

    public function log($field, $message)
    {
        $this->log[$field][] = $message;
    }

    public function addError(Cake $cake, $rule, $item, $arguments)
    {

        if(!(array_key_exists($rule['name'], $this->messages))) {
            return;
        }

        //get values for the rule replacement
        $ruleName = $rule['name'];
        $field = (is_null($item['alias']) ? $item['name'] : $item['alias']);
        $value = $item['value'];
        $arguments = (count($arguments) > 1 ? join(', ', $arguments) : $arguments);

        if(!(is_array($arguments)) && array_key_exists($arguments, $cake->getItems())) {
            $arguments = $cake->getItems()[$arguments]['alias'];
        }

        $message = $this->messages[$rule['name']];
        $message = str_replace(
            [
                '{rule}',
                '{field}',
                '{value}',
                '{arguments}'
            ],
            [
                $ruleName,
                $field,
                $item['value'],
                (count($arguments) > 1 ? join(', ', $arguments) : $arguments)
            ],
            $message
        );

        $this->errors[$item['name']][] = $message;

    }

    public function getErrors($field = null)
    {

        if(is_null($field)) {
            return $this->errors;
        }

        if(array_key_exists($field, $this->errors)) {
            return $this->errors[$field];
        }

        return null;
    }

    public function getLog()
    {
        return $this->log;
    }

    public function passed()
    {
        return (count($this->getErrors()) === 0);
    }

    public function failed()
    {
        return (count($this->getErrors()) >= 1);
    }

}
