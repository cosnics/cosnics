<?php
namespace Chamilo\Libraries\Protocol\Error\Architecture\Interface;

use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Interface for classes that build exception loggers
 *
 * @package Chamilo\Libraries\Protocol\Error\Architecture\Interface
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
interface ExceptionLoggerBuilderInterface
{
    public function __construct(SessionInterface $session, UrlGenerator $urlGenerator, array $configuration = []);

    public function createExceptionLogger(): ExceptionLoggerInterface;
}