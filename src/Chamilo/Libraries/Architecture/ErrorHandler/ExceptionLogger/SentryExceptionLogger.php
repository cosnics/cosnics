<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\PageConfiguration;
use Exception;
use Sentry\Event;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Throwable;
use function Sentry\captureException;
use function Sentry\init;

/**
 * Logs Exceptions to Sentry (sentry.io)
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class SentryExceptionLogger implements ExceptionLoggerInterface
{

    protected string $sentryConnectionString;

    protected SessionInterface $session;

    protected UrlGenerator $urlGenerator;

    /**
     * @throws \Exception
     */
    public function __construct(
        SessionInterface $session, UrlGenerator $urlGenerator, string $sentryConnectionString
    )
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
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;

        init(
            [
                'dsn' => $sentryConnectionString,
                'traces_sample_rate' => 0.01,
                'before_send' => function (Event $event) use ($session, $urlGenerator): ?Event {
                    $userId = $session->get(Manager::SESSION_USER_IO);

                    if ($userId)
                    {
                        $profilePageUrl = $urlGenerator->fromParameters(
                            [
                                Application::PARAM_CONTEXT => Manager::CONTEXT,
                                Application::PARAM_ACTION => Manager::ACTION_USER_DETAIL,
                                Manager::PARAM_USER_USER_ID => $userId
                            ]
                        );

                        $event->setContext('user', ['id' => $userId, 'profile_page' => $profilePageUrl]);
                    }

                    return $event;
                }
            ]
        );
    }

    /**
     * Adds an exception logger for javascript to the header
     */
    public function addJavascriptExceptionLogger(PageConfiguration $pageConfiguration)
    {
        $matches = [];
        preg_match('/https:\/\/(.*)@/', $this->getSentryConnectionString(), $matches);

        $sentryKey = $matches[1];

        $html = [];

        $html[] = '<script
                src="https://js.sentry-cdn.com/' . $sentryKey . '.min.js"
                crossorigin="anonymous"
            ></script>';

        $userId = $this->getSession()->get(Manager::SESSION_USER_IO);

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

        $pageConfiguration->addHtmlHeader(implode(PHP_EOL, $html));
    }

    public function getSentryConnectionString(): string
    {
        return $this->sentryConnectionString;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function logException(
        Throwable $exception, int $exceptionLevel = self::EXCEPTION_LEVEL_ERROR, ?string $file = null, int $line = 0
    )
    {
        if ($exceptionLevel != self::EXCEPTION_LEVEL_FATAL_ERROR)
        {
            return;
        }

        captureException($exception);
    }
}