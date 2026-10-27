<?php
declare(strict_types=1);

session_start();

$page_title = 'نظام الجمارك - تسجيل الدخول';

$error = $_SESSION['login_error'] ?? '';

unset($_SESSION['login_error']);

require __DIR__ . '/resources/views/auth/login.php';