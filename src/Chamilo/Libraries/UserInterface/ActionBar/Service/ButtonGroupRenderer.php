<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\ButtonGroup;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonGroupRenderer
{

    public function render(ButtonGroup $button): string
    {
        $html = [];

        $html[] = '<div class="' . implode(' ', $this->determineClasses($button)) . '">';

        foreach ($button->getGroupButtons() as $button)
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
    protected function determineClasses(ButtonGroup $buttonGroup): array
    {
        return array_merge($buttonGroup->getClasses(), ['action-bar', 'btn-group']);
    }

    public function getButtonClass(): string
    {
        return ButtonGroup::class;
    }
}