<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class DatabaseLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        $channel = $config['name'] ?? 'laravel';
        $monolog = new Logger($channel);

        $monolog->pushHandler(new StreamHandler(storage_path('logs/laravel.log'), Logger::DEBUG));
        $monolog->pushHandler(new DatabaseHandler(Logger::DEBUG));

        return $monolog;
    }
}