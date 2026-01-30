<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonDropDownInterface;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonRendererDropDownTrait
{
    use ButtonRendererSubButtonsTrait;

    /**
     * @return string[]
     */
    public function determineDropDownClasses(ButtonDropDownInterface $dropDownButton): array
    {
        return array_merge(['dropdown-menu'], $dropDownButton->getDropDownClasses());
    }

    protected function getDropdownToggleAttributes(): string
    {
        return 'data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button"';
    }

    public function renderDropDownButtons(ButtonDropDownInterface $dropDownButton): string
    {
        $html = [];

        $html[] = '<ul class="' . implode(' ', $this->determineDropDownClasses($dropDownButton)) . '">';
        $html[] = $this->renderSubButtons($dropDownButton->getDropDownButtons());
        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }
}