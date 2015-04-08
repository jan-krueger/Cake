<?php
require_once 'autoloader.cake.php';

use Cake\Cake;
use Cake\CakeErrorHandler;
use Cake\CakeUtils;

if(isset($_POST['submitButton'])) {

    $errorHandler = new CakeErrorHandler;
    $validator = new Cake($errorHandler);

    $validator->validate([
        'username|Username' => [$_POST['username'], 'required|minlength(4)|maxlength(20)'],
        'email|E-Mail'      => [$_POST['email'], 'required|email'],
        'password|Password' => [$_POST['password'], 'required|minlength(6)|match(password_confirm)'],
        'password_confirm' => [$_POST['password_confirm'], 'required']
    ]);

    if($errorHandler->passed()) {
        echo "You're welcome to join our exclusive club!";
    }

    if($errorHandler->failed()) {
        echo "<ul>";
        foreach($errorHandler->getErrors() as $field => $messages) {

            echo "<li>{$field}</li>";
            echo "<ul>";
            foreach($messages as $message) {
                echo "<li>{$message}</li>";
            }
            echo "</ul>";


        }
        echo "</ul>";
    }

}

?>

<html>
    <head>
        <title>Cake - the simple way!</title>
    </head>
    <body>

        <form method="post">
            <div>
                <p>Username: <input type="text" name="username"/></p>
            </div>
            <div>
                <p>E-Mail  : <input type="email" name="email"/></p>
            </div>
            <div>
                <p>Password: <input type="password" name="password"/></p>
            </div>
            <div>
                <p>Password confirmation: <input type="password" name="password_confirm"></p>
            </div>
            <div>
                <input type="submit" name="submitButton" value="Submit">
            </div>
        </form>

    </body>
</html>
