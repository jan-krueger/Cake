<?php
namespace Cake;

class Cake
{


    private $rules = [];

    public function __construct()
    {
        $this->loadRules();
    }


    public function validate($items)
    {

        $errorHandler = new CakeErrorHandler;

        foreach($items as $field => $arguments) {

            $fieldAlias = explode('|', $field);
            $field = $fieldAlias[0];

            if(count($arguments) < 2) {
                $errorHandler->log($field, "To less arguments. Please check the array for the field. The method expects two or more but only ". count($arguments) ." is given.");
                continue; //the given item has too less arguments to pass in
            }

            $rules = explode('|', $arguments[1]);

            foreach($rules as $ruleName) {

                preg_match("/\((.*?)\)/", $ruleName, $ruleArguments);

                $rule = $this->getRuleByName(
                    (strpos($ruleName, '(') ? mb_substr($ruleName, 0, strpos($ruleName, "(")) : $ruleName)
                );

                if($rule === null) { //the given rule doesnt exist
                    if(!empty($ruleName))
                        $errorHandler->log($field, "The given rule \"{$ruleName}\" doesn't exist.");

                    continue;
                }

                $ruleArguments = (count($ruleArguments) > 1 ? $ruleArguments[1] : null); //gives the right argument
                $ruleArguments = (strpos($ruleArguments, ',') ? explode(',', preg_replace('/\s+/', '', $ruleArguments)) : $ruleArguments); //gives an array of arguments if there is more than one

                //call rule to validate the input
                $result = $rule['class']->invoke(
                    $this,
                    $field,
                    $arguments[0],
                    $ruleArguments,
                    $items
                );


                //when the validation fails then we add a message to the CakeErrorHandler
                if(!($result)) {
                    $errorHandler->addError(
                        $rule,
                        $field,
                        (count($fieldAlias) == 2 ? $fieldAlias[1] : null),
                        $arguments[0],
                        $ruleArguments
                    );
                }

            }

        }

        return $errorHandler;

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

    private function loadRules()
    {

        foreach(get_class_methods($this) as $rule) {

            if(mb_substr($rule, 0, 6) === 'rule__') {

                $name = mb_substr($rule, 6);
                $method = new \ReflectionMethod($this, $rule);

                if(!($method->getNumberOfParameters()) === 4) { //the method expects too much/less arguments
                    continue;
                }

                $this->rules[] = [
                    'name'  => $name,
                    'class' => $method
                ];

            }

        }

    }

    /*
    * This is the rules area.
    * To add your own rule just create a new method that follows the following pattern:
    * 1) Method Name: rule__<your-rule-name> | Replace the <your-rule-name> with your rule name
    * 2) Method Arguments: rule__<your-rule-name>($field, $value, $arguments, $items) | Your rule method needs three arguments.
    *       2.1) $field => the field name
    *       2.1.2) $value => the given value from the field
    *       2.1.3) $arguments => the given arguments that are passed into the method
    *       2.2) The names of the arguments doesn't matter
    * 3) The rule must be "public"
    * 4) Done! - Now you can use your rule to validate fields.
    */

    public function rule__alphanumeric($field, $value, $arguments, $items)
    {
        return ctype_alnum($value);
    }

    public function rule__collection($field, $value, $arguments, $items)
    {
        return in_array($value, $arguments);

    }

    public function rule__date($field, $value, $arguments, $items)
    {

        $date = \DateTime::createFromFormat($arguments, $value);
        var_dump($value);

        return ($date && ($date->format($arguments) == $value));
    }

    public function rule__email($field, $value, $arguments, $items)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public function rule__hexcolor($field, $value, $arguments, $items)
    {
        return preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/i', $value);
    }

    public function rule__ip($field, $value, $arguments, $items)
    {
        return filter_var($value, FILTER_VALIDATE_IP);
    }

    public function rule__match($field, $value, $arguments, $items)
    {

        if(is_array($arguments)) {

            foreach($arguments as $argument) {

                if(!(array_key_exists($argument, $items))) {
                    return false;
                }

                if(!($value === $items[$argument][0])) {
                    return false;
                }

            }

            return true;

        }

        //non array
        if(!(array_key_exists($arguments, $items))) {
            return false;
        }

        return ($value === $items[$arguments][0]);
    }

    public function rule__maxlength($field, $value, $arguments, $items)
    {
        return (mb_strlen($value) <= (int) $arguments);
    }

    public function rule__minlength($field, $value, $arguments, $items)
    {
        return (mb_strlen($value) >= (int) $arguments);
    }

    public function rule__nocatpcha($field, $value, $arguments, $items)
    {
        return static::getCurlResponse(
            sprintf(
                'https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s&remoteip=%s',
                $arguments[0],
                $value,
                $arguments[1]
            )
        )['success'];
    }

    public function rule__required($field, $value, $arguments, $items)
    {
        return (!(empty($value)));
    }

    public function rule__url($field, $value, $arguments, $items)
    {
        return filter_var($value, FILTER_VALIDATE_URL);
    }


    private static function getCurlResponse($url)
    {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true
        ]);

        $result = curl_exec($curl);
        return json_decode($result, true);

    }

}
