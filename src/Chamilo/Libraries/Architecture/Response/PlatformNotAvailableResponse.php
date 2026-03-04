<?php
namespace Chamilo\Libraries\Architecture\Response;

use Chamilo\Libraries\DependencyInjection\Architecture\Trait\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultHeaderRenderer;
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
        $translator = $this->getTranslator();

        $html = [];

        $html[] = $this->getHeaderRenderer()->render();

        $html[] = '<div class="card text-bg-danger mt-3 w-50 mx-auto">';
        $html[] = '<div class="card-header">';
        $html[] = $translator->trans('PlatformNotAvailableTitle', [], StringUtilities::LIBRARIES);
        $html[] = '</div>';
        $html[] = '<div class="card-body">';
        $html[] = '<p class="card-text">';
        $html[] = $translator->trans('PlatformNotAvailableMessage', [], StringUtilities::LIBRARIES);
        $html[] = '</p>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->getFooterRenderer()->render();

        parent::__construct(implode(PHP_EOL, $html));
    }

    protected function getFooterRenderer(): DefaultFooterRenderer
    {
        return $this->getService(DefaultFooterRenderer::class);
    }

    protected function getHeaderRenderer(): DefaultHeaderRenderer
    {
        return $this->getService(DefaultHeaderRenderer::class);
    }
}