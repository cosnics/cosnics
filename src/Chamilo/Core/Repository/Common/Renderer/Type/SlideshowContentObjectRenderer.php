<?php
namespace Chamilo\Core\Repository\Common\Renderer\Type;

use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Utilities\Utilities;

class SlideshowContentObjectRenderer extends ContentObjectRenderer
{
    const SLIDESHOW_INDEX = 'slideshow';
    const SLIDESHOW_AUTOPLAY = 'autoplay';

    public function as_html()
    {
        if (! Request :: get(self :: SLIDESHOW_INDEX))
        {
            $slideshow_index = 0;
        }
        else
        {
            $slideshow_index = Request :: get(self :: SLIDESHOW_INDEX);
        }
        
        $content_object = DataManager :: retrieve_active_content_objects(
            ContentObject :: class_name(), 
            new DataClassRetrievesParameters($this->get_condition(), 1, $slideshow_index))->next_result();
        $content_object_count = DataManager :: count_active_content_objects(
            ContentObject :: class_name(), 
            new DataClassCountParameters($this->get_condition()));
        if ($content_object_count == 0)
        {
            $html[] = Display :: normal_message(Translation :: get('NoContentObjectsAvailable'), true);
            return implode(PHP_EOL, $html);
        }
        
        $is_first = ($slideshow_index == 0);
        $is_last = ($slideshow_index == $content_object_count - 1);
        
        $parameters = $this->get_parameters();
        
        $play_toolbar = new Toolbar();
        $play_toolbar->add_items($this->get_content_object_actions($content_object));
        if (Request :: get(self :: SLIDESHOW_AUTOPLAY))
        {
            $parameters[self :: SLIDESHOW_INDEX] = Request :: get(self :: SLIDESHOW_INDEX);
            $parameters[self :: SLIDESHOW_AUTOPLAY] = null;
            
            $play_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Stop', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_stop.png', 
                    $this->get_url($parameters), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $parameters[self :: SLIDESHOW_INDEX] = Request :: get(self :: SLIDESHOW_INDEX);
            $parameters[self :: SLIDESHOW_AUTOPLAY] = 1;
            
            $play_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Play', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_play.png', 
                    $this->get_url($parameters), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        $parameters = $this->get_parameters();
        
        $navigation_toolbar = new Toolbar();
        if (! $is_first)
        {
            $parameters[self :: SLIDESHOW_INDEX] = 0;
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('First', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_first.png', 
                    $this->get_url($parameters), 
                    ToolbarItem :: DISPLAY_ICON));
            
            $parameters[self :: SLIDESHOW_INDEX] = $slideshow_index - 1;
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_prev.png', 
                    $this->get_url($parameters), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('First', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_first_na.png', 
                    null, 
                    ToolbarItem :: DISPLAY_ICON));
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_prev_na.png', 
                    null, 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        if (! $is_last)
        {
            $parameters[self :: SLIDESHOW_INDEX] = $slideshow_index + 1;
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_next.png', 
                    $this->get_url($parameters), 
                    ToolbarItem :: DISPLAY_ICON));
            
            $parameters[self :: SLIDESHOW_INDEX] = $content_object_count - 1;
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Last', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_last.png', 
                    $this->get_url($parameters), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_next_na.png', 
                    null, 
                    ToolbarItem :: DISPLAY_ICON));
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Last', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_last_na.png', 
                    null, 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        $table = array();
        $table[] = '<table id="slideshow" class="data_table">';
        $table[] = '<thead>';
        $table[] = '<tr>';
        $table[] = '<th class="actions" style="width: 25%; text-align: left;">';
        $table[] = $play_toolbar->as_html();
        $table[] = '</th>';
        $table[] = '<th style="text-align: center;">' . htmlspecialchars($content_object->get_title()) . ' - ' .
             ($slideshow_index + 1) . '/' . $content_object_count . '</th>';
        $table[] = '<th class="navigation" style="width: 25%; text-align: right;">';
        $table[] = $navigation_toolbar->as_html();
        $table[] = '</th>';
        $table[] = '</tr>';
        $table[] = '</thead>';
        $table[] = '<tbody>';
        $table[] = '<tr><td colspan="3" style="background-color: #f9f9f9; text-align: center;">';
        $table[] = ContentObjectRenditionImplementation :: factory(
            $content_object, 
            ContentObjectRendition :: FORMAT_HTML, 
            ContentObjectRendition :: VIEW_PREVIEW, 
            $this->get_repository_browser())->render();
        $table[] = '</td></tr>';
        
        $table[] = '</tbody>';
        $table[] = '</table>';
        
        if (Request :: get(self :: SLIDESHOW_AUTOPLAY))
        {
            if (! $is_last)
            {
                $autoplay_url = $this->get_url(
                    array(self :: SLIDESHOW_AUTOPLAY => 1, self :: SLIDESHOW_INDEX => $slideshow_index + 1));
            }
            else
            {
                $autoplay_url = $this->get_url(array(self :: SLIDESHOW_AUTOPLAY => 1, self :: SLIDESHOW_INDEX => 0));
            }
            
            $html[] = '<meta http-equiv="Refresh" content="10; url=' . $autoplay_url . '" />';
        }
        
        $html[] = implode(PHP_EOL, $table);
        return implode(PHP_EOL, $html);
    }
}
