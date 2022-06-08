<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Libraries\Format\Structure\BaseHeader;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Exception;
use Sentry\Event;
use function Sentry\captureException;
use function Sentry\init;

/**
 * Logs Exceptions to Sentry (sentry.io)
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SentryExceptionLogger implements ExceptionLoggerInterface
{

    protected string $sentryConnectionString;

    protected SessionUtilities $sessionUtilities;

    /**
     * @throws \Exception
     */
    public function __construct(SessionUtilities $sessionUtilities, string $sentryConnectionString)
    {
        if (!class_exists('\Sentry\SentrySdk'))
        {
            throw new Exception('Can not use the SentryExceptionLogger when sentry is not included');
        }

        if (empty($sentryConnectionString))
        {
            throw new Exception('The given connection string for sentry can not be empty');
        }

        $this->sentryConnectionString = $sentryConnectionString;
        $this->sessionUtilities = $sessionUtilities;

        init(
            [
                'dsn' => $sentryConnectionString,
                'traces_sample_rate' => 0.01,
                'before_send' => function (Event $event) use ($sessionUtilities): ?Event {
                    $userId = $sessionUtilities->getUserId();

                    $profilePage =
                        $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'] .
                        '?application=Chamilo\\\\Core\\\\User&go=UserDetail&user_id=' . $userId;

                    $event->setContext('user', ['id' => $userId, 'profile_page' => $profilePage]);

                    return $event;
                }
            ]
        );
    }

    /**
     * Adds an exception logger for javascript to the header
     */
    public function addJavascriptExceptionLogger(BaseHeader $header)
    {
        $matches = [];
        preg_match("/https:\/\/(.*)@/", $this->getSentryConnectionString(), $matches);

        $sentryKey = $matches[1];

        $html = [];

        $html[] = '<script
                src="https://js.sentry-cdn.com/' . $sentryKey . '.min.js"
                crossorigin="anonymous"
            ></script>';

        $userId = $this->getSessionUtilities()->getUserId();

        $profilePage = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'] .
            '?application=Chamilo\\\\Core\\\\User&go=UserDetail&user_id=' . $userId;

        $html[] = '<script>';

        $html[] = '
        
        Sentry.onLoad(function() {
                Sentry.setContext("user", {
                    id: ' . $userId . ',
                    profile_page: "' . $profilePage . '"
                })
            });
            Sentry.forceLoad();';

        $html[] = 'unknownFunction();';

        $html[] = '</script>';

        $header->addHtmlHeader(implode(PHP_EOL, $html));
    }

    public function getSentryConnectionString(): string
    {
        return $this->sentryConnectionString;
    }

    public function getSessionUtilities(): SessionUtilities
    {
        return $this->sessionUtilities;
    }

    public function logException(
        Exception $exception, int $exceptionLevel = self::EXCEPTION_LEVEL_ERROR, ?string $file = null, int $line = 0
    )
    {
        if ($exceptionLevel != self::EXCEPTION_LEVEL_FATAL_ERROR)
        {
            return;
        }

        captureException($exception);
    }
}