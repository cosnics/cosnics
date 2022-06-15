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

    public function render(): string
    {
        $html = [];

        $html[] = '<li ' . $this->renderClasses() . '>';
        $html[] = $this->getButton()->getLabel();
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    public function determineClasses(): array
    {
        return array_merge(['dropdown-header'], $this->getButton()->getClasses());
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButtonHeader
     */
    public function getButton()
    {
        return parent::getButton();
    }

    public function renderClasses(): string
    {
        return 'class="' . implode(' ', $this->determineClasses()) . '"';
    }
}