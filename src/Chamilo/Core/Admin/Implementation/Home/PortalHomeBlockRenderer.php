<?php
namespace Chamilo\Core\Admin\Implementation\Home;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Admin\Implementation\Home
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PortalHomeBlockRenderer extends BlockRenderer
{
    public const CONTEXT = Manager::CONTEXT;

    public function displayContent(Element $block, ?User $user = null): string
    {
        $html = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'portal_home']);

        return $html ?: $this->getTranslator()->trans('ConfigurePortalHomeFirst', [], Manager::CONTEXT);
    }
}
