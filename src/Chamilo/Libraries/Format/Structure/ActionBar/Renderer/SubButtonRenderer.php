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
    use \Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ActionButtonRendererTrait;

    /**
     *
     * @see \Chamilo\Libraries\Format\Structure\ActionBar\Renderer\AbstractButtonRenderer::render()
     */
    public function render()
    {
        $html = array();

        $html[] = '<li>';
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
        $classes = array();

        $classes[] = 'dropdown-item';
        $classes[] = $this->getButton()->getClasses();

        if (! $this->getButton()->getAction())
        {
            $classes[] = 'disabled';
        }

        return $classes;
    }
}