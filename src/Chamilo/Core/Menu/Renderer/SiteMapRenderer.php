<?php
namespace Chamilo\Core\Menu\Renderer;

/**
 * @package Chamilo\Core\Menu\Renderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SiteMapRenderer extends MenuRenderer
{

    /**
     * @param string $containerMode
     * @param integer $numberOfItems
     *
     * @return string
     */
    public function renderHeader(string $containerMode, int $numberOfItems = 0)
    {
        return '';
    }

    /**
     *
     * @return string
     */
    public function renderFooter()
    {
        return '';
    }
}