<?php
namespace Chamilo\Core\Menu;

use Chamilo\Core\Menu\Renderer\Item;
use Chamilo\Libraries\Translation\Translation;

/**
 * Settings connector for menu settings
 */
class SettingsConnector
{

    public function get_renderers()
    {
        return array(
            ItemRenderer::class => Translation::getInstance()->getTranslation(
                'MenuBootstrapBar', null, Manager::context()
            )
        );
    }
}
