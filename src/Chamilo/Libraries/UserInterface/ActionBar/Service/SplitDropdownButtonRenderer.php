<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SplitDropdownButton;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonRendererActionInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonRendererDisplayInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonRendererDropDownInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererActionTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererClassesTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererDisplayTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererDropDownTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererLinkTrait;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Service
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

    public function render(SplitDropdownButton $splitDropDownButton): string
    {
        $html = [];

        $html[] = '<div class="btn-group">';
        $html[] = $this->renderLink($splitDropDownButton, ['btn', 'btn-default']);
        $html[] = $this->renderDropdown($splitDropDownButton);
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getButtonClass(): string
    {
        return SplitDropdownButton::class;
    }

    public function renderDropdown(SplitDropdownButton $splitDropDownButton): string
    {
        $html = [];

        $html[] = '<a';
        $html[] =
            'class="' . $this->renderClasses($splitDropDownButton, ['btn', 'btn-default', 'dropdown-toggle']) . '"';
        $html[] = $this->getDropdownToggleAttributes();
        $html[] = '>';
        $html[] = '<span class="caret"></span>';
        $html[] = '<span class="sr-only"></span>';
        $html[] = '</a>';

        $html[] = $this->renderDropDownButtons($splitDropDownButton);

        return implode(PHP_EOL, $html);
    }
}