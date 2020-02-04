<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar\Renderer;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SubButtonDividerRenderer extends AbstractButtonToolbarItemRenderer
{

    /**
     *
     * @see \Chamilo\Libraries\Format\Structure\ActionBar\Renderer\AbstractButtonToolbarItemRenderer::render()
     */
    public function render()
    {
        return '<div ' . $this->renderClasses() . '></div>';
    }

    /**
     *
     * @return string
     */
    public function renderClasses()
    {
        return 'class="' . implode(' ', $this->determineClasses()) . '"';
    }

    /**
     *
     * @return string[]
     */
    public function determineClasses()
    {
        $classes = array();

        $classes[] = 'dropdown-divider';
        $classes[] = $this->getButton()->getClasses();

        return $classes;
    }
}