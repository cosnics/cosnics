<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar\Renderer;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonGroupRenderer extends AbstractButtonToolbarItemRenderer
{

    /**
     * @throws \ReflectionException
     */
    public function render(): string
    {
        $html = [];

        $html[] = '<div class="' . implode(' ', $this->determineClasses()) . '">';

        foreach ($this->getButtonGroup()->getButtons() as $button)
        {
            $rendererClassName =
                __NAMESPACE__ . '\\' . ClassnameUtilities::getInstance()->getClassnameFromObject($button) . 'Renderer';
            $renderer = new $rendererClassName($button);

            $html[] = $renderer->render($button);
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    protected function determineClasses(): array
    {
        return array_merge($this->getButtonGroup()->getClasses(), ['action-bar', 'btn-group']);
    }

    public function getButtonGroup(): ButtonGroup
    {
        return $this->getButton();
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup $buttonGroup
     */
    public function setButtonGroup(ButtonGroup $buttonGroup)
    {
        $this->setButton($buttonGroup);
    }
}