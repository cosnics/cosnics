<?php
namespace Chamilo\Core\Repository\ContentObject\Announcement\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Platform\Translation;

class Displayer extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block implements ConfigurableInterface,
    StaticBlockTitleInterface
{

    public function __construct($renderer, $block)
    {
        parent :: __construct($renderer, $block, Translation :: get('Displayer'));
    }

    public function isVisible()
    {
        return true; // i.e.display on homepage when anonymous
    }

    public function displayContent()
    {
        $content_object = $this->getObject();

        $display = ContentObjectRenditionImplementation :: factory(
            $content_object,
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_DESCRIPTION,
            $this->getRenderer());
        return $display->render();
    }
}
