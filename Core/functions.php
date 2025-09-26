<?php

use Core\Response;

function adminAuth()
{
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: /admin/login');
        exit();
    }
}

function dd($value)
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";

    die();
}

function abort($code = Response::NOT_FOUND)
{
    http_response_code($code);

    require base_path("/views/{$code}.php");

    die();
}

function urlIs($value)
{
    return $_SERVER['REQUEST_URI'] === $value;
}

function authorize($condition, $status = Response::FORBIDDEN)
{
    if (! $condition) {
        abort($status);
    }

    return true;
}

function base_path($path)
{
    return BASE_PATH . $path;
} 

function view($path, $attributes = [])
{
    extract($attributes);

    require base_path('views/' . $path);
}