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
        return '<li role="separator" ' . $this->renderClasses() . '></li>';
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

        $classes[] = 'divider';
        $classes[] = $this->getButton()->getClasses();

        return $classes;
    }
}