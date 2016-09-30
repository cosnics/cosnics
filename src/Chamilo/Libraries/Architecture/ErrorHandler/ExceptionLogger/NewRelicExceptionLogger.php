<?php

namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Libraries\Platform\Session\Session;

/**
 * Logs errors to New Relic
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NewRelicExceptionLogger implements ExceptionLoggerInterface
{
    /**
     * NewRelicExceptionLogger constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        if (!extension_loaded('newrelic'))
        {
            throw new \Exception('Can not use the NewRelicExceptionLogger when the newrelic extension is not loaded');
        }

        $this->configureChamiloParameters();
    }

    /**
     * Logs an exception
     *
     * @param \Exception $exception
     * @param string $file
     * @param int $line
     */
    public function logException($exception, $file = null, $line = 0)
    {
        newrelic_notice_error('chamilo_exception', $exception);
    }

    /**
     * Configures additional chamilo parameters in New Relic
     */
    protected function configureChamiloParameters()
    {
        $prefix = 'chamilo_';

        newrelic_add_custom_parameter($prefix . 'url', $_SERVER['REQUEST_URI']);
        newrelic_add_custom_parameter($prefix . 'http_method', $_SERVER['REQUEST_METHOD']);

        $user_id = Session::get_user_id();
        if (!empty($user_id))
        {
            newrelic_add_custom_parameter($prefix . 'user_id', Session::get_user_id());
        }
    }
}