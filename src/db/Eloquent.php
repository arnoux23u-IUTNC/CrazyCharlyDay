<?php

namespace custombox\db;

use Exception;
use Illuminate\Database\Capsule\Manager as Capsule;

class Eloquent
{

    public static function start(string $file)
    {
        $capsule = new Capsule;
        if (!file_exists($file)) {
            print_r("Config file not found");
            header('HTTP/1.1 500 Internal Server Error');
            require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . '500.html';
            exit();
        }
        $config = parse_ini_file($file);
        $capsule->addConnection(array('driver' => $config['db_driver'],
                'host' => $config['db_host'],
                'database' => $config['db_name'],
                'username' => $config['db_user'],
                'password' => $config['db_password'],
                'charset' => $config['db_charset'],
                'collation' => $config['db_collation'],
                'prefix' => $config['db_prefix'])
        );
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        try {
            $capsule->getConnection()->getPdo();
        } catch (Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . '500.html';
            exit();
        }
    }
}