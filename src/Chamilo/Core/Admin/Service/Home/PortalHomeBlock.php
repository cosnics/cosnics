<?php
namespace Chamilo\Core\Admin\Service\Home;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Home\Architecture\Interfaces\AnonymousBlockInterface;
use Chamilo\Core\Home\Renderer\BlockRenderer;
use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Libraries\Translation\Translation;

class PortalHomeBlock extends BlockRenderer implements AnonymousBlockInterface
{

    public function displayContent()
    {
        $html = Configuration::getInstance()->get_setting(['Chamilo\Core\Admin', 'portal_home']);
        $html = $html ?: Translation::get('ConfigurePortalHomeFirst');

        $renderer = new ContentObjectResourceRenderer($html);

        return $renderer->run();
    }
}
