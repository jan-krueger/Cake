<?php
require_once 'Cake.class.php';
require_once 'CakeErrorHandler.class.php';

use Cake\Cake;
use Cake\CakeErrorHandler;
use Cake\MyOwnValidatior;


$array = [
    'username'  =>          ['Yonas', 'required'],
    'password|Password' =>  ['test', 'required|match(repeatPassword, secondRepeat)'],
    'repeatPassword' =>     ['test', ''],
    'secondRepeat' =>       ['tesitt', '']
    ];

$cake = new Cake();
var_dump($cake->validate($array)->valid());
