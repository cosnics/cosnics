<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Service;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SplitDropdownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererActionInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererDisplayInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererDropDownInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererActionTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererClassesTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererDisplayTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererDropDownTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererLinkTrait;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SplitDropdownButtonRenderer extends AbstractButtonCollectionButtonRenderer
    implements ButtonRendererInterface, ButtonRendererDisplayInterface, ButtonRendererDropDownInterface,
    ButtonRendererActionInterface
{
    use ButtonRendererDisplayTrait;
    use ButtonRendererDropDownTrait;
    use ButtonRendererActionTrait;
    use ButtonRendererLinkTrait;
    use ButtonRendererClassesTrait;

    public function render(SplitDropdownButtonCollection $splitDropDownButton): string
    {
        $html = [];

        $html[] = '<div class="btn-group">';
        $html[] = $this->renderLink($splitDropDownButton, $this->getDefaultButtonClasses());
        $html[] = $this->renderDropdown($splitDropDownButton);
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getButtonClassName(): string
    {
        return SplitDropdownButtonCollection::class;
    }

    public function renderDropdown(SplitDropdownButtonCollection $splitDropDownButton): string
    {
        $html = [];

        $html[] = '<a';
        $html[] = 'class="' .
            $this->renderClasses($splitDropDownButton, $this->getDefaultButtonClasses([static::DROPDOWN_CLASS])) . '"';
        $html[] = static::DROPDOWN_TOGGLE_ATTRIBUTES;
        $html[] = '>';
        $html[] = $this->renderCaret();
        $html[] = '<span class="sr-only"></span>';
        $html[] = '</a>';

        $html[] = $this->renderDropDownButtons($splitDropDownButton);

        return implode(PHP_EOL, $html);
    }
}