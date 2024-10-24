<?php

namespace App\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;

class CsvLogger
{
    private Logger $logger;

    public function __construct(string $name = 'import_products_logger')
    {
        $this->logger = new Logger($name);
        $output = "%level_name% | %datetime% > %message%\n";
        $streamHandler = new StreamHandler(__DIR__ . "/../../var/log/ProductImport.log", Level::Debug);
        $streamHandler->setFormatter(new LineFormatter($output));
        $this->logger->pushHandler($streamHandler);
    }

    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }
}
