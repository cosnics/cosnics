<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Interface for classes that build exception loggers
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
interface ExceptionLoggerBuilderInterface
{

    public function __construct(
        ConfigurationConsulter $configurationConsulter, SessionInterface $session, UrlGenerator $urlGenerator
    );

    public function createExceptionLogger(): ExceptionLoggerInterface;
}