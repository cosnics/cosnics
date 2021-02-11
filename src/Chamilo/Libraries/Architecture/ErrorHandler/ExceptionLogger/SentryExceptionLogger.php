<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Libraries\Format\Structure\BaseHeader;
use Chamilo\Libraries\Platform\Session\Session;
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

    /**
     * @var string
     */
    protected $sentryConnectionString;

    /**
     * SentryExceptionLogger constructor.
     *
     * @param string $sentryConnectionString
     *
     * @throws \Exception
     */
    public function __construct($sentryConnectionString = '')
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

        init(
            [
                'dsn' => $sentryConnectionString,
                'traces_sample_rate' => 0.01,
                'before_send' => function (Event $event): ?Event {
                    $userId = SessionUtilities::getUserId();

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
     *
     * @param \Chamilo\Libraries\Format\Structure\BaseHeader $header
     */
    public function addJavascriptExceptionLogger(BaseHeader $header)
    {
        $matches = [];
        preg_match("/https:\/\/(.*)@/", $this->sentryConnectionString, $matches);

        $sentryKey = $matches[1];

        $html = [];

        $html[] = '<script
                src="https://js.sentry-cdn.com/' . $sentryKey . '.min.js"
                crossorigin="anonymous"
            ></script>';

        $profilePage = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'] .
            '?application=Chamilo\\\\Core\\\\User&go=UserDetail&user_id=' . Session::getUserId();

        $html[] = '<script>';

        $html[] = '
        
        Sentry.onLoad(function() {
                Sentry.setContext("user", {
                    id: ' . Session::getUserId() . ',
                    profile_page: "' . $profilePage . '"
                })
            });
            Sentry.forceLoad();';

        $html[] = 'unknownFunction();';

        $html[] = '</script>';

        $header->addHtmlHeader(implode(PHP_EOL, $html));
    }

    /**
     * Logs an exception
     *
     * @param \Exception $exception
     * @param integer $exceptionLevel
     * @param string $file
     * @param integer $line
     */
    public function logException($exception, $exceptionLevel = self::EXCEPTION_LEVEL_ERROR, $file = null, $line = 0)
    {
        if ($exceptionLevel != self::EXCEPTION_LEVEL_FATAL_ERROR)
        {
            return;
        }

        captureException($exception);
    }
}