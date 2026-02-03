<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Service;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererDisplayInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererDropDownInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererClassesTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererDisplayTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererDropDownTrait;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DropDownButtonRenderer extends AbstractButtonCollectionButtonRenderer
    implements ButtonRendererInterface, ButtonRendererDisplayInterface, ButtonRendererDropDownInterface
{
    use ButtonRendererDisplayTrait;
    use ButtonRendererDropDownTrait;
    use ButtonRendererClassesTrait;

    public function render(DropDownButtonCollection $dropDownButton): string
    {
        $html = [];

        $html[] = '<div class="btn-group">';
        $html[] = $this->renderLink($dropDownButton);
        $html[] = $this->renderDropDownButtons($dropDownButton);
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getButtonClassName(): string
    {
        return DropDownButtonCollection::class;
    }

    public function renderLink(DropDownButtonCollection $dropDownButton): string
    {
        $html = [];

        $html[] = '<a';
        $html[] = static::DROPDOWN_TOGGLE_ATTRIBUTES;
        $html[] = 'class="' .
            $this->renderClasses($dropDownButton, $this->getDefaultButtonClasses([static::DROPDOWN_CLASS])) . '"';
        $html[] = 'title="' . $this->getTitle($dropDownButton) . '"';
        $html[] = '>';
        $html[] = $this->renderInlineGlyphAndLabel($dropDownButton);
        $html[] = $this->renderCaret();
        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }
}