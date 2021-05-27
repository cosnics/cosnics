<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar\Renderer;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SubButtonRenderer extends AbstractButtonRenderer
{
    use ActionButtonRendererTrait;

    /**
     * @return string
     */
    public function render()
    {
        $html = [];

        $html[] = '<li' . ($this->getButton()->isActive() ? ' class="active"' : '') . '>';
        $html[] = parent::render();
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string[]
     */
    public function determineClasses()
    {
        $classes = [];

        $classes[] = $this->getButton()->getClasses();

        if (!$this->getButton()->getAction())
        {
            $classes[] = 'disabled';
        }

        return $classes;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton
     */
    public function getButton()
    {
        return parent::getButton();
    }
}