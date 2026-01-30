<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\DropDownButton;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonRendererDisplayInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonRendererDropDownInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererClassesTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererDisplayTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererDropDownTrait;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DropDownButtonRenderer extends AbstractButtonCollectionButtonRenderer
    implements ButtonRendererInterface, ButtonRendererDisplayInterface, ButtonRendererDropDownInterface
{
    use ButtonRendererDisplayTrait;
    use ButtonRendererDropDownTrait;
    use ButtonRendererClassesTrait;

    public function render(DropDownButton $dropDownButton): string
    {
        $html = [];

        $html[] = '<div class="btn-group">';
        $html[] = $this->renderLink($dropDownButton);
        $html[] = $this->renderDropDownButtons($dropDownButton);
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getButtonClass(): string
    {
        return DropDownButton::class;
    }

    public function renderLink(DropDownButton $dropDownButton): string
    {
        $html = [];

        $html[] = '<a';
        $html[] = $this->getDropdownToggleAttributes();
        $html[] = 'class="' . $this->renderClasses($dropDownButton, ['btn', 'btn-default'], ['dropdown-toggle']) . '"';
        $html[] = 'title="' . $this->getTitle($dropDownButton) . '"';
        $html[] = '>';
        $html[] = $this->renderInlineGlyphAndLabel($dropDownButton);
        $html[] = '<span class="caret"></span>';
        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }
}