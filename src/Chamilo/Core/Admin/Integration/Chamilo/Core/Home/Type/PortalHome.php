<?php
namespace Chamilo\Core\Admin\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;

class PortalHome extends \Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer
{

    public function displayContent()
    {
        $html = PlatformSetting :: get('portal_home');
        $html = $html ? $html : Translation :: get('ConfigurePortalHomeFirst');

        $renderer = new ContentObjectResourceRenderer($this, $html);

        return $renderer->run();
    }

    public function isVisible()
    {
        return true; // i.e.display on homepage when anonymous
    }
}
