<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererClassesTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererSubButtonsTrait;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ButtonGroupRenderer extends AbstractButtonCollectionButtonRenderer implements ButtonRendererInterface
{
    use ButtonRendererSubButtonsTrait;
    use ButtonRendererClassesTrait;

    public function render(ButtonGroup $buttonGroup): string
    {
        $html = [];

        $html[] = '<div';
        $html[] = 'class="' . $this->renderClasses($buttonGroup, [], ['action-bar', 'btn-group']) . '">';
        $html[] = $this->renderSubButtons($buttonGroup->getGroupButtons());
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getButtonClass(): string
    {
        return ButtonGroup::class;
    }
}