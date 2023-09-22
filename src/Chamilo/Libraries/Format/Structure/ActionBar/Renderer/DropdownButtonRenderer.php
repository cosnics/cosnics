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
    use DropdownButtonRendererTrait;

    /**
     * @return string[]
     */
    public function determineClasses(): array
    {
        $classes = parent::determineClasses();

        $classes[] = 'dropdown-toggle';

        return $classes;
    }

    public function renderDropdown(): string
    {
        return $this->renderSubButtons();
    }

    public function renderLinkContent(): string
    {
        $html = [];

        $html[] = parent::renderLinkContent();
        $html[] = $this->renderCaret();

        return implode('', $html);
    }

    public function renderLinkOpeningTag(): string
    {
        $html = [];

        $html[] = '<a';
        $html[] = $this->renderDropdownAttributes();
        $html[] = $this->renderClasses();
        $html[] = $this->renderTitle();
        $html[] = '>';

        return implode(' ', $html);
    }
}