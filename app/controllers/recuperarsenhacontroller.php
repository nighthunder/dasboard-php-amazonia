<?php

require_once '../model/M_User.php';
use app\models\M_User;
require_once '../../config/config.php';
require_once '../functions/functions.php';

$user = new M_User;

// echo "<pre>";
// var_dump($_GET);
// echo "</pre>";

echo $user->recuperarSenha($_POST["email"],$_POST["code"],$_POST["senha"]);

?>