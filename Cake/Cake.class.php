<?php
namespace Cake;

class Cake
{


    private $errorHandler;

    private $rules = [];

    private $items = [];

    public function __construct(CakeErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
        $this->loadRules();
    }

    private function __autoload($class)
    {
        var_dump($class);
    }

    public function validate($items)
    {

        $this->items = $items;

        foreach($items as $field => $arguments) {

            $fieldAlias = explode('|', $field);
            $field = $fieldAlias[0];

            if(count($arguments) < 2) {
                $this->errorHandler->log($field, "To less arguments. Please check the array for the field. The method expects two or more but only ". count($arguments) ." is given.");
                continue; //the given item has too less arguments to pass in
            }

            $rules = explode('|', $arguments[1]);

            foreach($rules as $ruleName) {

                preg_match("/\((.*?)\)/", $ruleName, $ruleArguments);

                $rule = $this->getRuleByName(
                    (strpos($ruleName, '(') ? mb_substr($ruleName, 0, strpos($ruleName, "(")) : $ruleName)
                );

                if($rule === null) { //the given rule doesnt exist
                    if(!(empty($ruleName))) {
                        $this->errorHandler->log($field, "The given rule \"{$ruleName}\" doesn't exist.");
                    }
                    continue;
                }

                $ruleArguments = (count($ruleArguments) > 1 ? $ruleArguments[1] : null); //gives the right argument
                $ruleArguments = (strpos($ruleArguments, ',') ? explode(',', preg_replace('/\s+/', '', $ruleArguments)) : $ruleArguments); //gives an array of arguments if there is more than one

                //call rule to validate the input
                $result = $rule['class']->invoke(
                    $this,
                    $field,
                    $arguments[0],
                    $ruleArguments
                );


                //when the validation fails then we add a message to the CakeErrorHandler
                if(!($result)) {
                    $this->errorHandler->addError(
                        $rule,
                        $field,
                        (count($fieldAlias) == 2 ? $fieldAlias[1] : null),
                        $arguments[0],
                        $ruleArguments
                    );
                }

            }

        }

        return $this->errorHandler;

    }

    private function getRuleByName($name)
    {

        foreach($this->rules as $rule) {

            if($rule['name'] == $name) {
                return $rule;
            }

        }

        return null;

    }

    /**
    * Call this method to load all rules
    */
    public function loadRules()
    {

        $this->rules = [];

        foreach(get_class_methods($this) as $rule) {

            if(mb_substr($rule, 0, 6) === 'rule__') {

                $name = mb_substr($rule, 6);
                $method = new \ReflectionMethod($this, $rule);

                if(!($method->getNumberOfParameters()) === 3) { //the method expects too much/less arguments
                    $this->errorHandler->log('methods', "The method \"{$name}\" expects ". $method->getNumberOfParameters() ." but it should expect 3 arguments.");
                    continue;
                }

                $this->rules[] = [
                    'name'  => $name,
                    'class' => $method
                ];

            }

        }

    }

    public function rule__testit($field)
    {
        return false;
    }

    public function rule__alphanumeric($field, $value, $arguments)
    {
        return ctype_alnum($value);
    }

    public function rule__collection($field, $value, $arguments)
    {
        return in_array($value, $arguments);

    }

    public function rule__date($field, $value, $arguments)
    {

        $date = \DateTime::createFromFormat($arguments, $value);
        var_dump($value);

        return ($date && ($date->format($arguments) == $value));
    }

    public function rule__email($field, $value, $arguments)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public function rule__hexcolor($field, $value, $arguments)
    {
        return preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/i', $value);
    }

    public function rule__ip($field, $value, $arguments)
    {
        return filter_var($value, FILTER_VALIDATE_IP);
    }

    public function rule__match($field, $value, $arguments)
    {

        if(is_array($arguments)) {

            foreach($arguments as $argument) {

                if(!(array_key_exists($argument, $this->items))) {
                    return false;
                }

                if(!($value === $this->items[$argument][0])) {
                    return false;
                }

            }

            return true;

        }

        //non array
        if(!(array_key_exists($arguments, $this->items))) {
            return false;
        }

        return ($value === $this->items[$arguments][0]);
    }

    public function rule__maxlength($field, $value, $arguments)
    {
        return (mb_strlen($value) <= (int) $arguments);
    }

    public function rule__minlength($field, $value, $arguments)
    {
        return (mb_strlen($value) >= (int) $arguments);
    }

    public function rule__nocatpcha($field, $value, $arguments)
    {
        return CakeUtils::getCurlResponse(
            sprintf(
                'https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s&remoteip=%s',
                $arguments[0],
                $value,
                $arguments[1]
            )
        )['success'];
    }

    public function rule__required($field, $value, $arguments)
    {
        return (!(empty($value)));
    }

    public function rule__url($field, $value, $arguments)
    {
        return filter_var($value, FILTER_VALIDATE_URL);
    }

}
