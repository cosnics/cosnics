<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar\Renderer;

use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
trait DropdownButtonRendererTrait
{

    public function render(): string
    {
        $html = [];

        $html[] = '<div class="btn-group">';
        $html[] = parent::render();
        $html[] = $this->renderDropdown();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    public function determineDropdownClasses(): array
    {
        return array_merge(['dropdown-menu'], $this->getButton()->getDropdownClasses());
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton
     */
    abstract public function getButton();

    public function renderCaret(): string
    {
        return '<span class="caret"></span>';
    }

    abstract public function renderDropdown(): string;

    public function renderDropdownAttributes(): string
    {
        return 'data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button"';
    }

    /**
     * @throws \ReflectionException
     */
    public function renderSubButtons(): string
    {
        $html = [];

        $html[] = '<ul class="' . implode(' ', $this->determineDropdownClasses()) . '">';

        foreach ($this->getButton()->getSubButtons() as $subButton)
        {
            $rendererClassName =
                __NAMESPACE__ . '\\' . ClassnameUtilities::getInstance()->getClassnameFromObject($subButton) .
                'Renderer';
            $renderer = new $rendererClassName($subButton);
            $html[] = $renderer->render($subButton);
        }

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }
}
