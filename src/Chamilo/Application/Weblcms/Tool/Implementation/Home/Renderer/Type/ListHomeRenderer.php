<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\Type;

use Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\HomeRenderer;
use Chamilo\Application\Weblcms\Renderer\ToolList\ToolListRenderer;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ListHomeRenderer extends HomeRenderer
{

    /**
     *
     * @see \Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\HomeRenderer::render()
     */
    public function render()
    {
        $renderer = ToolListRenderer::factory(
            ToolListRenderer::TYPE_FIXED, 
            $this->getHomeTool(), 
            $this->getCourseTools());
        
        $html = [];
        
        $html[] = $this->getHomeTool()->renderHomeActions();
        
        $html[] = '<div class="clearfix"></div>';
        
        if ($this->getIntroductionAllowed())
        {
            $html[] = $this->getHomeTool()->display_introduction_text($this->getIntroduction());
        }
        
        $html[] = $renderer->toHtml();
        
        return implode(PHP_EOL, $html);
    }
}