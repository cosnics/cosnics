<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket;

use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

class ExternalObjectDisplay extends \Chamilo\Core\Repository\External\ExternalObjectDisplay
{

    public function get_display_properties()
    {
        $object = $this->get_object();
        $properties = parent :: get_display_properties();
        $properties[Translation :: get('Branches')] = implode(', ', $object->get_branches());
        if ($object->get_download_link())
        {
            $toolbar_item = new ToolbarItem(
                Translation :: get('Download'), 
                Theme :: getInstance()->getImagePath() . 'action_download.png', 
                $object->get_download_link(), 
                ToolbarItem :: DISPLAY_ICON);
            
            $properties[Translation :: get('Download')] = $toolbar_item->as_html();
        }
        return $properties;
    }

    public function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        if ($object->get_logo())
        {
            $class = ($is_thumbnail ? 'thumbnail' : 'with_border');
            $html = array();
            $html[] = '<img class="' . $class . '" src="' . $object->get_logo() . '" />';
            return implode("\n", $html);
        }
        else
        {
            return parent :: get_preview($is_thumbnail);
        }
    }

    public function as_html()
    {
        $html = array();
        $html[] = parent :: as_html();
        
        $object = $this->get_object();
        
        // tags
        $tags = $object->get_tags();
        if ($tags)
        {
            $data = array();
            foreach ($tags as $tag)
            {
                $row = array();
                $row[] = $tag->get_name();
                $row[] = $tag->get_author();
                $row[] = DatetimeUtilities :: format_locale_date(null, $tag->get_time());
                $row[] = $tag->get_branch();
                $toolbar_item = new ToolbarItem(
                    Translation :: get('Download'), 
                    Theme :: getInstance()->getImagePath() . 'action_download.png', 
                    $tag->get_download_link(), 
                    ToolbarItem :: DISPLAY_ICON);
                $row[] = $toolbar_item->as_html();
                $data[] = $row;
            }
            
            $table = new SortableTableFromArray($data);
            $table->set_header(0, Translation :: get('Name'));
            $table->set_header(1, Translation :: get('Author'));
            $table->set_header(2, Translation :: get('Time'));
            $table->set_header(3, Translation :: get('Branch'));
            $table->set_header(4, Translation :: get('Download'));
            $html[] = '<h3>' . Translation :: get('Tags') . '</h3>';
            $html[] = $table->as_html();
        }
        
        // changesets
        $changesets = $object->get_changesets();
        if ($changesets)
        {
            $data = array();
            foreach ($changesets as $changeset)
            {
                $row = array();
                $row[] = $changeset->get_revision();
                
                $row[] = $changeset->get_author();
                $row[] = $changeset->get_message();
                $row[] = DatetimeUtilities :: format_locale_date(null, $changeset->get_time());
                $row[] = $changeset->get_branch();
                $toolbar_item = new ToolbarItem(
                    Translation :: get('Download'), 
                    Theme :: getInstance()->getImagePath() . 'action_download.png', 
                    $changeset->get_download_link(), 
                    ToolbarItem :: DISPLAY_ICON);
                $row[] = $toolbar_item->as_html();
                $data[] = $row;
            }
            
            $table = new SortableTableFromArray($data);
            $table->set_header(0, Translation :: get('Revision'));
            $table->set_header(1, Translation :: get('Author'));
            $table->set_header(2, Translation :: get('Message'));
            $table->set_header(3, Translation :: get('Time'));
            $table->set_header(4, Translation :: get('Branch'));
            $table->set_header(5, Translation :: get('Download'));
            $html[] = '<h3>' . Translation :: get('Changesets') . '</h3>';
            $html[] = $table->as_html();
        }
        
        return implode("\n", $html);
    }
}
