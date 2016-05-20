<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar\Renderer;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DropdownButtonRenderer extends AbstractButtonRenderer
{
    use \Chamilo\Libraries\Format\Structure\ActionBar\Renderer\DropdownButtonRendererTrait;

    /**
     *
     * @return string
     */
    public function renderDropdown()
    {
        return $this->renderSubButtons();
    }

    /**
     *
     * @return string
     */
    public function renderLinkOpeningTag()
    {
        $html = array();

        $html[] = '<a';
        $html[] = $this->renderDropdownAttributes();
        $html[] = $this->renderClasses();
        $html[] = $this->renderTitle();
        $html[] = '>';

        return implode(' ', $html);
    }

    /**
     *
     * @return string[]
     */
    public function determineClasses()
    {
        $classes = parent :: determineClasses();

        $classes[] = 'dropdown-toggle';

        return $classes;
    }

    /**
     *
     * @return string
     */
    public function renderLinkContent()
    {
        $html = array();

        $html[] = parent :: renderLinkContent();
        $html[] = $this->renderCaret();

        return implode('', $html);
    }
}