<?php
require_once '../functions.php';
require_once '../cart_functions.php';

logout();
header('Location: login.php');
exit();
?>