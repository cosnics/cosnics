<?php
namespace Chamilo\Core\Repository\Preview\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Preview\Manager;

class RenditionComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        $display = ContentObjectRenditionImplementation::factory(
            $this->get_content_object(), 
            $this->getCurrentFormat(), 
            $this->getCurrentView(), 
            $this);
        
        $html = [];
        
        $html[] = $this->render_header();
        $html[] = $display->render();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \core\repository\ContentObject
     */
    public function get_root_content_object()
    {
        return $this->get_content_object();
    }

    public function get_content_object_display_attachment_url($attachment)
    {
        $parameters = $this->get_parameters();
        $parameters[self::PARAM_CONTENT_OBJECT_ID] = $attachment->get_id();
        
        return $this->get_url($parameters);
    }
}
