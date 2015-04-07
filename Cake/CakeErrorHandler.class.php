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

    public function addError($rule, $field, $alias, $value, $arguments)
    {

        if(!(array_key_exists($rule['name'], $this->messages))) {
            return;
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
                $rule['name'],
                ($alias == null ? $field : $alias),
                $value,
                (count($arguments) > 1 ? join(', ', $arguments) : $arguments)
            ],
            $message
        );

        $this->errors[$field][$rule['name']] = $message;

    }

    public function getErrors($field = null)
    {
        return (is_null($field) ? $this->errors : $this->errors[$field]);
    }

    public function getLog()
    {
        return $this->log;
    }

    public function valid()
    {
        return (count($this->getErrors()) === 0);
    }

}
