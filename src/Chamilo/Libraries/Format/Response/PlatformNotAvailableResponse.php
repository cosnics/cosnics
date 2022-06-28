<?php
namespace Chamilo\Libraries\Format\Response;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Format\Response
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PlatformNotAvailableResponse extends Response
{
    use DependencyInjectionContainerTrait;

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function __construct(string $message, ?Application $application = null)
    {
        $this->initializeContainer();

        $this->getPageConfiguration()->setApplication($application);

        $html = [];
        $html[] = $this->getHeaderRenderer()->render();
        $html[] = '<br />';
        $html[] = '<div class="alert alert-danger text-center">';
        $html[] = $message;
        $html[] = '</div>';
        $html[] = $this->getFooterRenderer()->render();

        parent::__construct(implode(PHP_EOL, $html));
    }
}