<?php
namespace Chamilo\Libraries\Architecture\Response;

use Chamilo\Libraries\DependencyInjection\Architecture\Trait\DependencyInjectionContainerTrait;
use Chamilo\Libraries\UserInterface\Layout\Service\BaseFooterRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\BaseHeaderRenderer;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Architecture\Response
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PlatformNotAvailableResponse extends Response
{
    use DependencyInjectionContainerTrait;

    public function __construct()
    {
        $html = [];
        $html[] = $this->getHeaderRenderer()->render();
        $html[] = '<br />';
        $html[] = '<div class="alert alert-danger text-center">';
        $html[] = $this->getTranslator()->trans('PlatformNotAvailableMessage');
        $html[] = '</div>';
        $html[] = $this->getFooterRenderer()->render();

        parent::__construct(implode(PHP_EOL, $html));
    }

    protected function getFooterRenderer(): BaseFooterRenderer
    {
        return $this->getService(BaseFooterRenderer::class);
    }

    protected function getHeaderRenderer(): BaseHeaderRenderer
    {
        return $this->getService(BaseHeaderRenderer::class);
    }
}