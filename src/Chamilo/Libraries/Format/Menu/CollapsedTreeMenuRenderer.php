<?php
namespace Chamilo\Libraries\Format\Menu;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

/**
 * Tree renderer with items collapsed by default.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 * @package Chamilo\Libraries\Format\Menu
 */
class CollapsedTreeMenuRenderer extends TreeMenuRenderer
{

    /**
     *
     * @return string
     */
    protected function get_javascript()
    {
        return ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) .
                 'Jquery/jquery.simple_tree_menu.js');
    }
}
