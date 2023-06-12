<?php
namespace Chamilo\Core\Admin\Service\Home;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Home\Architecture\Interfaces\AnonymousBlockInterface;
use Chamilo\Core\Home\Renderer\BlockRenderer;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\User\Storage\DataClass\User;

class PortalHomeBlockRenderer extends BlockRenderer implements AnonymousBlockInterface
{
    public const CONTEXT = Manager::CONTEXT;

    public function displayContent(Element $block, ?User $user = null): string
    {
        $html = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'portal_home']);
        $html = $html ?: $this->getTranslator()->trans('ConfigurePortalHomeFirst', [], Manager::CONTEXT);

        $renderer = new ContentObjectResourceRenderer($html);

        return $renderer->run();
    }
}
