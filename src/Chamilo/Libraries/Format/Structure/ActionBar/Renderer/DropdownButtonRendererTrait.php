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

    /**
     *
     * @return string
     */
    abstract public function getButton();

    /**
     *
     * @return string
     */
    abstract public function renderDropdown();

    /**
     *
     * @return string
     */
    public function render()
    {
        $html = array();

        $html[] = '<div class="btn-group">';
        $html[] = parent::render();
        $html[] = $this->renderDropdown();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderSubButtons()
    {
        $html = array();

        $html[] = '<ul class="' . implode(' ', $this->determineDropdownClasses()) . '">';

        foreach ($this->getButton()->getSubButtons() as $subButton)
        {
            $rendererClassName = __NAMESPACE__ . '\\' .
                 ClassnameUtilities::getInstance()->getClassnameFromObject($subButton) . 'Renderer';
            $renderer = new $rendererClassName($subButton);
            $html[] = $renderer->render($subButton);
        }

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string[]
     */
    public function determineDropdownClasses()
    {
        $classes = array();

        $classes[] = 'dropdown-menu';

        $dropdownClasses = $this->getButton()->getDropdownClasses();

        if (! empty($dropdownClasses))
        {
            $classes[] = $dropdownClasses;
        }

        return $classes;
    }

    /**
     *
     * @return string
     */
    public function renderCaret()
    {
        return '<span class="caret"></span>';
    }

    /**
     *
     * @return string
     */
    public function renderDropdownAttributes()
    {
        return 'data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button"';
    }
}
