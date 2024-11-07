<?php
session_start();
require_once '../Models/Manager.php';

$error = '';
$datas = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['username'] === 'admin' && $_POST['password'] === 'Admin*123') {
        $_SESSION['admin'] = true;
        header('Location: database_manager.php');
        exit();
    } else {
        $error = 'Identifiants incorrects';
    }
}

$datas['error'] = $error;
$pageTitle = 'Connexion Administration';

ob_start();
require_once '../Views/admin/login_content.php';
$content = ob_get_clean();

require_once '../Views/base.html.php';
