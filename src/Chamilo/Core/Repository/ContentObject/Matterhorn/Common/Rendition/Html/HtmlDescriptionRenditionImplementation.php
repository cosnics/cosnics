<?php
namespace Chamilo\Core\Repository\ContentObject\Matterhorn\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Matterhorn\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{
    const TAB_GENERAL = 'General';
    const TAB_TRACKS = 'Tracks';

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_description()
    {
        $object = $this->get_content_object();
        
        $html = array();
        
        $preview = ContentObjectRenditionImplementation::launch(
            $object, 
            ContentObjectRendition::FORMAT_HTML, 
            ContentObjectRendition::VIEW_PREVIEW, 
            $this->get_context());
        
        $html[] = '<div class="link_url" style="margin-top: 1em;">' . $preview . '<br/></div>';
        
        $tabs = new DynamicTabsRenderer('matterhorn');
        $tabs->add_tab(
            new DynamicContentTab(
                self::TAB_GENERAL, 
                Translation::get('General'), 
                Theme::getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Matterhorn', 
                    'Tabs/' . self::TAB_GENERAL), 
                $this->get_properties_table()));
        $tabs->add_tab(
            new DynamicContentTab(
                self::TAB_TRACKS, 
                Translation::get('Tracks'), 
                Theme::getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Matterhorn', 
                    'Tabs/' . self::TAB_TRACKS), 
                $this->get_tracks_table()));
        
        $html[] = $tabs->render();
        
        return implode(PHP_EOL, $html);
    }

    public function get_display_properties()
    {
        $properties = array();
        $properties[Translation::get('Series')] = $this->get_object()->get_series()->get_title();
        $properties[Translation::get('Contributor')] = $this->get_object()->get_contributors();
        $properties[Translation::get('Subject')] = $this->get_object()->get_subjects();
        $properties[Translation::get('License')] = $this->get_object()->get_license();
        
        return $properties;
    }

    public function get_tracks_table()
    {
        $table_data = array();
        
        foreach ($this->get_object()->get_tracks() as $key => $track)
        {
            $table_row = array();
            $table_row[] = $key + 1;
            $table_row[] = $track->get_mimetype();
            
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
                    Translation::get('DownloadTrack'), 
                    Theme::getInstance()->getCommonImagePath('Action/Download'), 
                    $track->get_url(), 
                    ToolbarItem::DISPLAY_ICON));
            
            $table_row[] = $actions->as_html();
            
            $table_data[] = $table_row;
        }
        
        $headers = array();
        $headers[] = new StaticTableColumn('#');
        $headers[] = new StaticTableColumn(Translation::get('Type'));
        $headers[] = new StaticTableColumn(Translation::get('Video'));
        $headers[] = new StaticTableColumn(Translation::get('Audio'));
        $headers[] = new StaticTableColumn('');
        
        $table = new SortableTableFromArray($table_data, $headers);
        return $table->toHtml();
    }
}
