<?php
namespace Chamilo\Core\Repository\ContentObject\Link\Integration\Chamilo\Core\Home\Type;

use Chamilo\Libraries\Platform\Translation;

class Linker extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block
{

    public function __construct($renderer, $block)
    {
        parent :: __construct($renderer, $block, Translation :: get('Linker'));
    }

    public function isVisible()
    {
        return true; // i.e.display on homepage when anonymous
    }

    public function displayContent()
    {
        $content_object = $this->getObject();
        $url = htmlentities($content_object->get_url());

        $html = array();
        $html[] = $content_object->get_description();
        $html[] = '<div class="link_url" style="margin-top: 1em;"><a href="' . $url . '" target="_blank" >' . $url .
             '</a></div>';

        return implode(PHP_EOL, $html);
    }

    public function isConfigurable()
    {
        return true;
    }
}
