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

    protected const DROPDOWN_CLASS = 'dropdown-toggle';
    protected const DROPDOWN_TOGGLE_ATTRIBUTES = 'data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button"';

    /**
     * @return string[]
     */
    public function determineDropDownClasses(ButtonDropDownCollectionInterface $dropDownButton): array
    {
        return array_merge(['dropdown-menu'], $dropDownButton->getDropDownClasses());
    }

    protected function renderCaret(): string
    {
        return '<span class="caret"></span>';
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