<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

session_unset();
session_destroy();
session_start();
setFlashMessage('success', 'You have been successfully logged out.');
redirect('login.php');
exit();
?>
