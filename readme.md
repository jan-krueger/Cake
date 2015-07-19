# Cake - the simple way to validate values
Cake is the latest validation project. Cake is based on "Validatio" and at all Validatio is a nice script, but the main reason to change to Cake is that Cake is 10 times faster than Validation and this is a massive increase in performance.

---

---

#Installation
Just use Composer
```
composer require sweetcode/cake
```

# Bugs
1. When you use aliases with the match rule it's doesn't work in the right way. #00001


# How can I use Cake?
It's really simple to use.

1 - First of all create a new instance that you can use to validate your fields
```
$cake = new Cake;
```

2 - You need a array to validate.
   The array should follow the following pattern:
```
   key: <field-name>(|<field-name-alias>)
   value: array(
            key: <field-value>
            value: <field-rule>(|<field-rule>|<field-rule>(arguments)...)
        )
```
```
$fields = [
    'username'  => ['Yonas' => 'required|minlength(3)|maxlength(12)|alphanumeric'],
    'password'  => ['test' => 'required|match(repeatPassword)|minlength(4)'],
    'passwordRepeat' => ['testit' => 'required'],
    'email'     => ['test@test.com' => 'required|email']
];

```

3 - Now we validate the given fields.
```
$errorHandler = $cake->validate($fields);
```

4 - The Cake#validate() method returns a CakeErrorHandler instance. You can use this instance to echo out the errors.
```
if($errorHandler->valid()) {
    echo "No errors found.";
} else {
    var_dump($errorHandler->getErrors());
}
```
---
# How can I add my own rules?

1. Method Name: `rule__<your-rule-name>` | Replace the `<your-rule-name>` with your rule name
2. Method Arguments: `rule__<your-rule-name>($field, $value, $arguments)` | Your rule method needs three arguments.
```
$field       => the field name
$value       => the given value from the field
$arguments   => the given arguments that are passed into the method
```
2.1) The names of the arguments doesn't matter
3. The rule method must be "public"
4. Done! - Now you can use your rule to validate fields.

---

---
# List of all rules
| Name          | Arguments                                 | Example                                                                                       | Description
|:--------------|:------------------------------------------| :---------------------------------------------------------------------------------------------| :--------------------
| alphanumeric  | `none`                                    | ['username' => ['Yonas', 'alphanumeric']                                                      | Check for alphanumeric character(s).
| collection    | `list of values` (string)                 | ['service' => ['Google', 'collection(Google, Facebook, Twitter)']]                            | Checks if a value exists in the given collection.
| date          | `date format` (string)                    | ['birthdate' => ['1.1.1990', 'date(d-m-Y)']]                                                  | Checks if the value is a valid date - based on the given date format. The method accepts every format that the DateTime#createFromFormat method accepts too.
| email         | `none`                                    | ['emailadress' => ['test@example.com', 'email']]                                              | Checks if the value is a valid email address.
| hexcolor      | `none`                                    | ['favouriteColor' => ['#FFFFFF', 'date(d-m-Y)']]                                              | Checks if the value is a valid hex color.
| match         | `list of fields` (string)                 | ['password' => ['test', 'match(repeatPassword)'], 'repeatPassword' => ['test', '']]           | Checks if the value matches to the other given fields.
| ip            | `none`                                    | ['yourServer' => ['127.0.0.1', 'ip']]                                                         | Checks if the value is a valid ip address.
| maxlength     | `maximum length` (integer)                | ['username' => ['Yonas', 'maxlength(12)']]                                                    | Checks the maximum length of the value.
| minlength     | `minimum length` (integer)                | ['username' => ['Yonas', 'minlength(4)']]                                                     | Checks the minimum length of the value
| required      | `none`                                    | ['username' => ['Yonas', 'required']]                                                         | Checks if a value is given.
| url           | `none`                                    | ['homepage' => ['http://example.com', 'url']]                                                 | Checks if the value is a valid url.
| nocaptcha     | `secret` (string), `sendIp` (boolean)     | ['captcha' => [$_POST['g-recaptcha-response'], 'nocaptcha(mysecrettoke, false)']]             | Checks if the captcha is answered in the right way
