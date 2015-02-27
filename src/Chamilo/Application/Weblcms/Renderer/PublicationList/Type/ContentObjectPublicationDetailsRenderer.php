<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList\Type;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;

/**
 * $Id: content_object_publication_details_renderer.class.php 216 2009-11-13
 * 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.browser.list_renderer
 */
/**
 * Renderer to display all details of learning object publication
 */
class ContentObjectPublicationDetailsRenderer extends ContentObjectPublicationListRenderer
{

    /**
     * Returns the HTML output of this renderer.
     * 
     * @return string The HTML output
     */
    public function as_html()
    {
        $publication_id = $this->get_tool_browser()->get_publication_id();
        
        $publication = DataManager :: retrieve_content_object_publication_with_content_object($publication_id);
        
        $this->get_tool_browser()->get_parent()->set_parameter(
            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID, 
            $publication_id);
        
        $html[] = $this->render_publication($publication);
        $html[] = '<br />';
        return implode(PHP_EOL, $html);
    }

    /**
     * Renders a single publication.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @return string The rendered HTML.
     */
    public function render_publication($publication, $first = false, $last = false)
    {
        $html = array();
        $last_visit_date = $this->get_tool_browser()->get_last_visit_date();
        $icon_suffix = '';
        if ($publication[ContentObjectPublication :: PROPERTY_HIDDEN])
        {
            $icon_suffix = '_na';
        }
        elseif ($publication[ContentObjectPublication :: PROPERTY_PUBLICATION_DATE] >= $last_visit_date)
        {
            $icon_suffix = '_new';
        }
        
        $content_object = $this->get_content_object_from_publication($publication);
        
        if ($content_object instanceof ComplexContentObjectSupport)
        {
            $title_url = $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID], 
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT));
        }
        
        $html[] = '<div class="announcements level_1" style="background-image: url(' .
             str_replace('.png', $icon_suffix . '.png', $content_object->get_icon_path()) . ');">';
        
        if ($title_url)
        {
            $html[] = '<a href="' . $title_url . '">';
        }
        
        $html[] = '<div class="title' . ($this->is_visible_for_target_users($publication) ? '' : ' invisible') . '">';
        $html[] = $this->render_title($publication);
        $html[] = '</div>';
        
        if ($title_url)
        {
            $html[] = '</a>';
        }
        
        $html[] = '<div style="padding-top: 1px;" class="description' .
             ($this->is_visible_for_target_users($publication) ? '' : ' invisible') . '">';
        $html[] = $this->render_description($publication);
        // $html[] = $this->render_attachments($publication);
        $html[] = '</div>';
        $html[] = '<div class="publication_info' . ($this->is_visible_for_target_users($publication) ? '' : ' invisible') .
             '">';
        $html[] = $this->render_publication_information($publication);
        $html[] = '</div>';
        $html[] = '<div class="publication_actions">';
        $html[] = $this->get_publication_actions($publication)->as_html();
        $html[] = '</div>';
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }
}
