<?php
namespace Chamilo\Libraries\Format\Response;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Display;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Format\Response
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ExceptionResponse extends Response
{
    use DependencyInjectionContainerTrait;

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function __construct(Exception $exception, ?Application $application = null)
    {
        $this->initializeContainer();

        $this->getPageConfiguration()->setApplication($application);

        $html = [];
        $html[] = $this->getHeaderRenderer()->render();
        $html[] = Display::error_message($exception->getMessage());
        $html[] = $this->getFooterRenderer()->render();

        parent::__construct(implode(PHP_EOL, $html));
    }
}