<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar\Renderer;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SubButtonHeaderRenderer extends AbstractButtonToolbarItemRenderer
{

    /**
     *
     * @see \Chamilo\Libraries\Format\Structure\ActionBar\Renderer\AbstractButtonRenderer::render()
     */
    public function render()
    {
        $html = array();

        $html[] = '<li ' . $this->renderClasses() . '>';
        $html[] = $this->getButton()->getLabel();
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string[]
     */
    public function determineClasses()
    {
        $classes = array();

        $classes[] = 'dropdown-header';
        $classes[] = $this->getButton()->getClasses();

        return $classes;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButtonHeader
     */
    public function getButton()
    {
        return parent::getButton();
    }

    /**
     *
     * @return string
     */
    public function renderClasses()
    {
        return 'class="' . implode(' ', $this->determineClasses()) . '"';
    }
}