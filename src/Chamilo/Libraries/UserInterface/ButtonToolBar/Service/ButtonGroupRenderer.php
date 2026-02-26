<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Service;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererClassesTrait;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ButtonGroupRenderer extends AbstractButtonCollectionButtonRenderer implements ButtonRendererInterface
{
    use ButtonRendererClassesTrait;

    public function render(ButtonGroup $buttonGroup): string
    {
        $html = [];

        $html[] = '<div';
        $html[] = 'class="' . $this->renderClasses($buttonGroup, [], ['btn-group', 'me-2']) . '">';
        $html[] = $this->renderSubButtons($buttonGroup->getButtons());
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getButtonClassName(): string
    {
        return ButtonGroup::class;
    }
}