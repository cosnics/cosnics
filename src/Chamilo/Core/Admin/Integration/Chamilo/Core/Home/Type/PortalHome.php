<?php
namespace Chamilo\Core\Admin\Integration\Chamilo\Core\Home\Type;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Home\Renderer\BlockRenderer;
use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Libraries\Translation\Translation;

class PortalHome extends BlockRenderer
{

    public function displayContent()
    {
        $html = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'portal_home'));
        $html = $html ?: Translation::get('ConfigurePortalHomeFirst');
        
        $renderer = new ContentObjectResourceRenderer($html);
        
        return $renderer->run();
    }

    public function isVisible()
    {
        return true; // i.e.display on homepage when anonymous
    }
}
