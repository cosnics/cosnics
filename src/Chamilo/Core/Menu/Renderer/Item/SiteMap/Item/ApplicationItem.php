<?php
namespace Chamilo\Core\Menu\Renderer\Item\SiteMap\Item;

use Chamilo\Core\Menu\Renderer\Item\SiteMap\SiteMap;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\SiteMap\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ApplicationItem extends SiteMap
{

    public function get_item_url()
    {
        $application = $this->getItem()->get_application();
        
        if (Application::is_active($application))
        {
            return;
        }
        
        if ($application == 'root')
        {
            $url = 'index.php';
        }
        else
        {
            $url = 'index.php?application=' . $this->getItem()->get_application();
        }
        
        $html = array();
        if ($this->getItem()->get_use_translation())
        {
            $title = Translation::get('TypeName', null, $this->getItem()->get_application());
        }
        else
        {
            $title = $this->getItem()->get_titles()->get_translation(Translation::getInstance()->getLanguageIsocode());
        }
        
        $html[] = '<a href="' . $url . '">' . $title;
        
        $html[] = '</a>';
        
        return implode(PHP_EOL, $html);
    }
}
