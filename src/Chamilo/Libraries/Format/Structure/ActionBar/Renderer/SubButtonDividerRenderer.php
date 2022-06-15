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

    public function render(): string
    {
        return '<li role="separator" ' . $this->renderClasses() . '></li>';
    }

    /**
     * @return string[]
     */
    public function determineClasses(): array
    {
        return array_merge(['divider'], $this->getButton()->getClasses());
    }

    /**
     *
     * @return string
     */
    public function renderClasses(): string
    {
        return 'class="' . implode(' ', $this->determineClasses()) . '"';
    }
}