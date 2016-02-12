<?php
namespace Chamilo\Core\Menu\Renderer\Item\Bar;

use Chamilo\Core\Menu\Renderer\Item\Renderer;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\Bar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Bar extends Renderer
{

    public function isSelected()
    {
        return $this->isItemSelected() || $this->isParentSelected();
    }

    abstract function isItemSelected();

    public function isParentSelected()
    {
        return $this->getItem()->hasParent() && $this->getParentRenderer()->isSelected();
    }

    public function render()
    {
        $html = array();

        $selected = $this->isItemSelected();

        $html[] = '<li' . ($selected ? ' class="active"' : '') . '>';
        $html[] = $this->getContent();

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }
}
