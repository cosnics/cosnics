<?php
use Chamilo\Libraries\Architecture\ErrorHandler\ErrorHandler;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Platform\Translation;

require_once __DIR__ . '/src/Chamilo/Libraries/Architecture/Bootstrap.php';

\Chamilo\Libraries\Architecture\Bootstrap::getInstance()->setup();

if (! \Chamilo\Configuration\Configuration::get('Chamilo\Configuration', 'debug', 'show_errors'))
{
    $errorHandler = new ErrorHandler($this->getExceptionLogger(), Translation::getInstance());
    $errorHandler->registerErrorHandlers();
}

$containerBuilder = new DependencyInjectionContainerBuilder();
$container = $containerBuilder->createContainer();

$console = $container->get('chamilo.libraries.console');
$console->setHelperSet($container->get('chamilo.libraries.console.helper_set'));
$console->run();