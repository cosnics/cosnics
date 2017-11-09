<?php
namespace Chamilo\Core\Menu;

use Chamilo\Core\Menu\Renderer\Menu\Renderer;
use Chamilo\Libraries\Translation\Translation;

/**
 * Settings connector for menu settings
 */
class SettingsConnector
{

    public function get_renderers()
    {
        $translator = Translation::getInstance();
        
        return array(
            Renderer::TYPE_BAR => $translator->getTranslation('MenuBootstrapBar', null, Manager::context()));
    }
}
