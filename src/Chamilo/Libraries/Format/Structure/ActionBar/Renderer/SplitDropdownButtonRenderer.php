<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar\Renderer;

use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SplitDropdownButtonRenderer extends AbstractButtonRenderer
{
    use DropdownButtonRendererTrait;
    use ActionButtonRendererTrait;

    /**
     *
     * @return string[]
     */
    public function determineDropdownActionClasses()
    {
        $classes = [];

        $classes[] = 'btn';
        $classes[] = 'btn-default';
        $classes[] = 'dropdown-toggle';
        $classes[] = $this->getButton()->getClasses();

        return $classes;
    }

    /**
     *
     * @return string
     */
    public function renderDropdown()
    {
        $html = [];

        $linkHtml = [];

        $linkHtml[] = '<a';
        $linkHtml[] = 'class="' . implode(' ', $this->determineDropdownActionClasses()) . '"';
        $linkHtml[] = $this->renderDropdownAttributes();
        $linkHtml[] = '>';

        $html[] = implode(' ', $linkHtml);
        $html[] = $this->renderCaret();
        $html[] = '<span class="sr-only">' . Translation::get('ToggleDropdown') . '</span>';
        $html[] = '</a>';

        $html[] = $this->renderSubButtons();

        return implode(PHP_EOL, $html);
    }
}