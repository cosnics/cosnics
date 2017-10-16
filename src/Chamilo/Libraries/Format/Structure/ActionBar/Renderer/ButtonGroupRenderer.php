<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar\Renderer;

use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

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
     *
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup
     */
    private $buttonGroup;

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup $buttonGroup
     */
    public function __construct(ButtonGroup $buttonGroup)
    {
        $this->buttonGroup = $buttonGroup;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup
     */
    public function getButtonGroup()
    {
        return $this->buttonGroup;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup $buttonGroup
     */
    public function setButtonGroup(ButtonGroup $buttonGroup)
    {
        $this->buttonGroup = $buttonGroup;
    }

    /**
     *
     * @return string
     */
    protected function getClasses()
    {
        $classes = $this->getButtonGroup()->getClasses();
        $classes[] = 'action-bar';
        $classes[] = 'btn-group';

        return implode(' ', $classes);
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $html = array();

        $html[] = '<div class="' . $this->getClasses() . '">';

        foreach ($this->getButtonGroup()->getButtons() as $button)
        {
            $rendererClassName = __NAMESPACE__ . '\\' .
                 ClassnameUtilities::getInstance()->getClassnameFromObject($button) . 'Renderer';
            $renderer = new $rendererClassName($button);

            $html[] = $renderer->render($button);
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}