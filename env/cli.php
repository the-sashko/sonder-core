<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$_SERVER = [
    'HTTP_COOKIE'          => '',
    'HTTP_ACCEPT_LANGUAGE' => 'en-US,en',
    'HTTP_ACCEPT'          => 'text/html,application/xml',
    'HTTP_USER_AGENT'      => 'test',
    'HTTP_ACCEPT_ENCODING' => '',
    'HTTP_HOST'            => 'localhost',
    'SERVER_NAME'          => 'localhost',
    'SERVER_PORT'          => '80',
    'SERVER_ADDR'          => '127.0.0.1',
    'REMOTE_PORT'          => '80',
    'REMOTE_ADDR'          => '127.0.0.1',
    'REQUEST_SCHEME'       => 'https',
    'REQUEST_TIME'         => time(),
    'SERVER_PROTOCOL'      => 'HTTP/1.1',
    'DOCUMENT_ROOT'        => __DIR__.'/../../../public',
    'DOCUMENT_URI'         => '/index.php',
    'REQUEST_URI'          => '/',
    'SCRIPT_NAME'          => '/index.php',
    'QUERY_STRING'         => '',
    'SCRIPT_FILENAME'      => __DIR__.'/../../../public/index.php',
    'PHP_SELF'             => '/index.php'
];

$_POST    = [];
$_GET     = [];
$_REQUEST = [];

trait Router
{
    //Mock Router
}
