<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonDropDownCollectionInterface;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonRendererDropDownTrait
{
    use ButtonRendererCollectionTrait;

    protected const DROPDOWN_CLASS = 'dropdown-toggle dropdown-toggle-split';
    protected const DROPDOWN_TOGGLE_ATTRIBUTES = ' data-bs-toggle="dropdown" aria-expanded="false" ';

    /**
     * @return string[]
     */
    public function determineDropDownClasses(ButtonDropDownCollectionInterface $dropDownButton): array
    {
        return array_merge(['dropdown-menu'], $dropDownButton->getDropDownClasses());
    }

    public function renderDropDownButtons(ButtonDropDownCollectionInterface $dropDownButton): string
    {
        $html = [];

        $html[] = '<ul class="' . implode(' ', $this->determineDropDownClasses($dropDownButton)) . '">';
        $html[] = $this->renderSubButtons($dropDownButton->getButtons());
        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }
}