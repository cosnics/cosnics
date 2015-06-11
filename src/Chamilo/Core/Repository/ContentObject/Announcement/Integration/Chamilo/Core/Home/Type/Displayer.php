<?php
namespace Chamilo\Core\Repository\ContentObject\Announcement\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Platform\Translation;

class Displayer extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block
{

    public function __construct($parent, $block_info, $configuration)
    {
        parent :: __construct($parent, $block_info, $configuration);
        $this->default_title = Translation :: get('Displayer');
    }

    public function is_visible()
    {
        return true; // i.e.display on homepage when anonymous
    }

    public function display_content()
    {
        $content_object = $this->get_object();
        
        $display = ContentObjectRenditionImplementation :: factory(
            $content_object, 
            ContentObjectRendition :: FORMAT_HTML, 
            ContentObjectRendition :: VIEW_DESCRIPTION, 
            $this->get_parent());
        return $display->render();
    }
}
