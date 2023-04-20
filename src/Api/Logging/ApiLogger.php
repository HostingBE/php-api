<?php

/**
 * @author Constan van Suchtelen van de Haere <constan@hostingbe.com>
 * @copyright 2023 HostingBE
 */

namespace HostingBE\phpapi\Api\Logging;

use DateTime;
use DateTimeZone;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class ApiLogger {

    public function create(string $channel = 'testing', string $fileNameHandle = 'app_log') {
        $logger = new Logger($channel);
        $dateFormat = "n/j/Y, g:i a";
        $formatter = new LineFormatter(null, $dateFormat, false, true);
        $now = (new DateTime("now"))->format('m_d_Y');
        $handler = new StreamHandler(__DIR__ . "/../../../logs/{$fileNameHandle}_{$now}.log", Logger::INFO);
        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);
        $logger->setTimezone(new DateTimeZone('Europe/Amsterdam'));
        return $logger;
        }
}

?>