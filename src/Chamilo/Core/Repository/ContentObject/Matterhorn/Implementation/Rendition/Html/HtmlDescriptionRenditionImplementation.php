<?php
namespace Chamilo\Core\Repository\ContentObject\Matterhorn\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Matterhorn\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{
    const TAB_GENERAL = 'general';
    const TAB_TRACKS = 'tracks';

    public function render()
    {
        return ContentObjectRendition :: launch($this);
    }

    public function get_description()
    {
        $object = $this->get_content_object();
        
        $html = array();
        
        $preview = ContentObjectRenditionImplementation :: launch(
            $object, 
            ContentObjectRendition :: FORMAT_HTML, 
            ContentObjectRendition :: VIEW_PREVIEW, 
            $this->get_context());
        
        $html[] = '<div class="link_url" style="margin-top: 1em;">' . $preview . '<br/></div>';
        
        $tabs = new DynamicTabsRenderer('matterhorn');
        $tabs->add_tab(
            new DynamicContentTab(
                self :: TAB_GENERAL, 
                Translation :: get('General'), 
                Theme :: getInstance()->getImagesPath() . 'tabs/' . self :: TAB_GENERAL . '.png', 
                $this->get_properties_table()));
        $tabs->add_tab(
            new DynamicContentTab(
                self :: TAB_TRACKS, 
                Translation :: get('Tracks'), 
                Theme :: getInstance()->getImagesPath() . 'tabs/' . self :: TAB_TRACKS . '.png', 
                $this->get_tracks_table()));
        
        $html[] = $tabs->render();
        
        return implode(PHP_EOL, $html);
    }

    public function get_display_properties()
    {
        $properties = array();
        $properties[Translation :: get('Series')] = $this->get_object()->get_series()->get_title();
        $properties[Translation :: get('Contributor')] = $this->get_object()->get_contributors();
        $properties[Translation :: get('Subject')] = $this->get_object()->get_subjects();
        $properties[Translation :: get('License')] = $this->get_object()->get_license();
        
        return $properties;
    }

    public function get_tracks_table()
    {
        $table_data = array();
        
        foreach ($this->get_object()->get_tracks() as $key => $track)
        {
            $table_row = array();
            $table_row[] = $key + 1;
            $table_row[] = Utilities :: mimetype_to_image($track->get_mimetype());
            
            if ($track->get_video())
            {
                $table_row[] = $track->get_video()->as_string();
            }
            else
            {
                $table_row[] = '';
            }
            
            if ($track->get_audio())
            {
                $table_row[] = $track->get_audio()->as_string();
            }
            else
            {
                $table_row[] = '';
            }
            
            $actions = new Toolbar();
            $actions->add_item(
                new ToolbarItem(
                    Translation :: get('DownloadTrack'), 
                    Theme :: getInstance()->getCommonImagesPath() . 'action_download.png', 
                    $track->get_url(), 
                    ToolbarItem :: DISPLAY_ICON));
            
            $table_row[] = $actions->as_html();
            
            $table_data[] = $table_row;
        }
        
        $table = new SortableTableFromArray($table_data, 0);
        
        $table->set_header(0, '#', false);
        $table->set_header(1, Translation :: get('Type'), false);
        $table->set_header(2, Translation :: get('Video'), false);
        $table->set_header(3, Translation :: get('Audio'), false);
        $table->set_header(4, '', false);
        
        return $table->as_html();
    }
}
