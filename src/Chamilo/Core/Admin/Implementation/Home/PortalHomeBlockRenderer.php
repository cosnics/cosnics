<?php
namespace Chamilo\Core\Admin\Implementation\Home;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Home\Architecture\Interface\AnonymousBlockInterface;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer;
use Chamilo\Core\User\Storage\DataClass\User;

class PortalHomeBlockRenderer extends BlockRenderer implements AnonymousBlockInterface
{
    public const CONTEXT = Manager::CONTEXT;

    public function displayContent(Element $block, ?User $user = null): string
    {
        $html = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'portal_home']);

        return $html ?: $this->getTranslator()->trans('ConfigurePortalHomeFirst', [], Manager::CONTEXT);
    }
}
