<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Libraries\Format\Structure\BaseHeader;
use Chamilo\Libraries\Platform\Session\Session;
use Exception;
use Raven_Client;

/**
 * Logs Exceptions to Sentry (sentry.io)
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SentryExceptionLogger implements ExceptionLoggerInterface
{

    /**
     * @var \Raven_Client
     */
    protected $sentryClient;

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
        if (!class_exists('\Raven_Client'))
        {
            throw new Exception('Can not use the SentryExceptionLogger when sentry is not included');
        }

        if (empty($sentryConnectionString))
        {
            throw new Exception('The given connection string for sentry can not be empty');
        }

        $this->sentryConnectionString = $sentryConnectionString;

        $this->sentryClient = new Raven_Client(
            $sentryConnectionString, array('install_default_breadcrumb_handlers' => false)
        );
    }

    /**
     * Adds an exception logger for javascript to the header
     *
     * @param \Chamilo\Libraries\Format\Structure\BaseHeader $header
     */
    public function addJavascriptExceptionLogger(BaseHeader $header)
    {
        $html = [];

        $html[] = '<script>';

        $html[] = 'Raven.config(\'' . $this->sentryConnectionString . '\',{';
        $html[] = '}).install();';

        $html[] = 'Raven.setUserContext({';
        $html[] = 'id: ' . Session::getUserId();
        $html[] = '})';

        $html[] = 'Raven.setExtraContext({';
        $html[] =
            'profile: \'' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'] .
            '?application=Chamilo\\\\Core\\\\User&go=UserDetail&user_id=' . Session::getUserId() . '\'';
        $html[] = '})';

        $html[] = '</script>';

        $header->addJavascriptFile('https://cdn.ravenjs.com/3.26.1/raven.min.js');
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

        $this->sentryClient->captureException($exception);
    }
}