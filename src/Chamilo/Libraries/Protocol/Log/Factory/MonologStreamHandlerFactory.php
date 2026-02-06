<?php
namespace Chamilo\Libraries\Protocol\Log\Factory;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

/**
 * @package Chamilo\Libraries\Protocol\Error\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MonologStreamHandlerFactory
{
    public static function createStreamHandler(string $stream): StreamHandler
    {
        $lineFormatter = new LineFormatter(
            '[%datetime%] [%level_name%] %message% %context% %extra%' . PHP_EOL, 'd/m/Y - H:i:s', false, true
        );

        $streamHandler = new StreamHandler($stream);
        $streamHandler->setFormatter($lineFormatter);

        return $streamHandler;
    }
}