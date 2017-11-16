<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn;

use Chamilo\Core\Repository\External\General\Streaming\StreamingMediaExternalObjectDisplay;
use Chamilo\Core\Repository\Implementation\Matterhorn\Stream\Stream;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 *
 * @author magali.gillard
 */
class ExternalObjectDisplay extends StreamingMediaExternalObjectDisplay
{
    const TAB_GENERAL = 'General';
    const TAB_TRACKS = 'Tracks';
    const TAB_ATTACHMENTS = 'Attachments';
    const TAB_METADATA = 'Metadata';

    public function get_display_properties()
    {
        $properties = parent::get_display_properties();
        
        $properties[Translation::get('Series')] = $this->get_object()->get_series()->get_title();
        $properties[Translation::get('Contributor')] = $this->get_object()->get_contributors();
        $properties[Translation::get('Subject')] = $this->get_object()->get_subjects();
        $properties[Translation::get('License')] = $this->get_object()->get_license();
        
        return $properties;
    }

    public function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        $settings = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting::get(
            'url', 
            $object->get_external_repository_id());
        
        $html = array();
        
        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(__NAMESPACE__, true) . 'Plugin/Projekktor/projekktor.js');
        
        if ($is_thumbnail)
        {
            $search_preview = $object->get_search_preview();
            
            if ($search_preview instanceof Attachment)
            {
                $width = 320;
                $height = 356;
                
                $parameters = array();
                $parameters[Application::PARAM_CONTEXT] = __NAMESPACE__;
                $parameters[Application::PARAM_ACTION] = Manager::ACTION_STREAM;
                $parameters[Stream::PARAM_TYPE] = Stream::TYPE_PREVIEW;
                $parameters[Manager::PARAM_EXTERNAL_REPOSITORY] = $object->get_external_repository_id();
                $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
                
                $redirect = new Redirect($parameters);
                $image_url = $redirect->getUrl();
                
                $html[] = '<img class="thumbnail" src="' . $image_url . '"/>';
            }
            else
            {
                $html[] = parent::get_preview($is_thumbnail);
            }
        }
        else
        {
            $width = 620;
            $height = 596;
            $video_tracks = $object->get_video_tracks();
            
            $tabs = new DynamicTabsRenderer('matterhorn_preview');
            
            if (count($video_tracks) > 0)
            {
                $video = array();
                
                $video[] = '<video class="projekktor" title="' . htmlentities($object->get_title()) .
                     '" width="640" height="390" controls>';
                $video[] = '</video>';
                
                $video[] = '<script type="text/javascript">';
                $video[] = '$(document)
        .ready(
                function() {
                    projekktor(
                            \'video\',
                            {
                                width : 640,
                                height : 390,
                                controls : true,
                                playbackQuality : \'small\',
                                toggleMute : true,
                                playbackQualities : [ {
                                    key : \'small\',
                                    minHeight : 240,
                                    minWidth : 240
                                }, {
                                    key : \'medium\',
                                    minHeight : 360,
                                    minWidth : [ {
                                        ratio : 1.77,
                                        minWidth : 640
                                    }, {
                                        ratio : 1.33,
                                        minWidth : 480
                                    } ]
                                }, {
                                    key : \'large\',
                                    minHeight : 480,
                                    minWidth : [ {
                                        ratio : 1.77,
                                        minWidth : 853
                                    }, {
                                        ratio : 1.33,
                                        minWidth : 640
                                    } ]
                                }, {
                                    key : \'hd720\',
                                    minHeight : 720,
                                    minWidth : [ {
                                        ratio : 1.77,
                                        minWidth : 1280
                                    }, {
                                        ratio : 1.33,
                                        minWidth : 960
                                    } ]
                                } ],
                                playlist : [ {';
                
                $track_codes = array();
                $i = 0;
                
                foreach ($object->get_tracks() as $track)
                {
                    if ($track->is_video())
                    {
                        $track_url = $track->get_url();
                        
                        $track_code = array();
                        $track_code[] = $i . ' : { src : \'';
                        $track_code[] = $track_url;
                        $track_code[] = '\', type : \'';
                        $track_code[] = $track->get_mimetype();
                        $track_code[] = '\', quality : \'';
                        $track_code[] = $track->get_quality();
                        $track_code[] = '\'';
                        $track_code[] = '}';
                        $track_codes[] = implode('', $track_code);
                        $i ++;
                    }
                }
                
                $video[] = implode(', ' . "\n", $track_codes);
                $video[] = '    } ]
                            });
                })';
                $video[] = '</script>';
                
                $html[] = implode(PHP_EOL, $video);
            }
            
            $audio_tracks = $object->get_audio_tracks();
            if (count($audio_tracks) > 0)
            {
                $audio = array();
                
                $audio[] = ResourceManager::getInstance()->get_resource_html(
                    Path::getInstance()->getJavascriptPath(__NAMESPACE__, true) . 'Plugin/jquery.jplayer.js');
                $audio[] = '<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){

	$("#jquery_jplayer_1").jPlayer({
		ready: function (event) {
			$(this).jPlayer("setMedia", {
				title: "' . htmlentities($object->get_title()) . '",';
                
                foreach ($object->get_tracks() as $track)
                {
                    
                    $audio[] = $track->get_extension() . ': "' . $track->get_url() . '",';
                }
                
                $audio[] = '});
		},
		wmode: "window",
		smoothPlayBar: true,
		keyEnabled: true,
		remainingDuration: true,
		toggleDuration: true
	});
});
//]]>
</script>';
                
                $audio[] = ' <div id="jquery_jplayer_1" class="jp-jplayer"></div>

		<div id="jp_container_1" class="jp-audio">
			<div class="jp-type-single">
				<div class="jp-gui jp-interface">
					<ul class="jp-controls">
						<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
						<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
						<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
						<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
						<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
						<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
					</ul>
					<div class="jp-progress">
						<div class="jp-seek-bar">
							<div class="jp-play-bar"></div>
						</div>
					</div>
					<div class="jp-volume-bar">
						<div class="jp-volume-bar-value"></div>
					</div>
					<div class="jp-time-holder">
						<div class="jp-current-time"></div>
						<div class="jp-duration"></div>

						<ul class="jp-toggles">
							<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
							<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
						</ul>
					</div>
				</div>
				<div class="jp-details">
					<ul>
						<li><span class="jp-title"></span></li>
					</ul>
				</div>
				<div class="jp-no-solution">
					<span>Update Required</span>
					To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
				</div>
			</div>
		</div>';
                
                $html[] = implode(PHP_EOL, $audio);
            }
        }
        
        return implode(PHP_EOL, $html);
    }

    public function get_title()
    {
        $object = $this->get_object();
        return '<h3>' . $object->get_title() . ' (' . DatetimeUtilities::format_seconds_to_minutes(
            $object->get_duration() / 1000) . ')</h3>';
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
        $headers[] = new SortableStaticTableColumn(Translation::get('#'));
        $headers[] = new SortableStaticTableColumn(Translation::get('Type'));
        $headers[] = new SortableStaticTableColumn(Translation::get('Video'));
        $headers[] = new SortableStaticTableColumn(Translation::get('Audio'));
        $headers[] = new StaticTableColumn('');
        
        $table = new SortableTableFromArray($table_data, $headers);
        
        return $table->toHtml();
    }

    public function get_attachments_table()
    {
        $table_data = array();
        
        foreach ($this->get_object()->get_attachments() as $attachment)
        {
            $table_row = array();
            $table_row[] = $attachment->get_mimetype();
            $table_row[] = $attachment->get_type_as_image();
            
            $actions = new Toolbar();
            $actions->add_item(
                new ToolbarItem(
                    Translation::get('DownloadAttachment'), 
                    Theme::getInstance()->getCommonImagePath('Action/Download'), 
                    $attachment->get_url(), 
                    ToolbarItem::DISPLAY_ICON));
            
            $table_row[] = $actions->as_html();
            
            $table_data[] = $table_row;
        }
        
        $headers = array();
        $headers[] = new SortableStaticTableColumn(Translation::get('Type'));
        $headers[] = new SortableStaticTableColumn(Translation::get('ContentType'));
        $headers[] = new StaticTableColumn('');
        
        $table = new SortableTableFromArray($table_data, $headers, array(), 0);
        
        return $table->toHtml();
    }

    public function get_metadata_table()
    {
        $table_data = array();
        
        foreach ($this->get_object()->get_metadata() as $metadata)
        {
            $table_row = array();
            $table_row[] = $metadata->get_mimetype();
            $table_row[] = $metadata->get_type_as_image();
            
            $actions = new Toolbar();
            $actions->add_item(
                new ToolbarItem(
                    Translation::get('DownloadMetadata'), 
                    Theme::getInstance()->getCommonImagePath('Action/Download'), 
                    $metadata->get_url(), 
                    ToolbarItem::DISPLAY_ICON));
            
            $table_row[] = $actions->as_html();
            
            $table_data[] = $table_row;
        }
        
        $headers = array();
        $headers[] = new SortableStaticTableColumn(Translation::get('Type'));
        $headers[] = new SortableStaticTableColumn(Translation::get('ContentType'));
        $headers[] = new StaticTableColumn('');
        
        $table = new SortableTableFromArray($table_data, $headers, array(), 0);
        
        return $table->as_html();
    }

    public function as_html()
    {
        $html = array();
        $html[] = $this->get_title();
        $html[] = $this->get_preview() . '<br/>';
        
        $tabs = new DynamicTabsRenderer('matterhorn');
        $tabs->add_tab(
            new DynamicContentTab(
                self::TAB_GENERAL, 
                Translation::get('General'), 
                Theme::getInstance()->getImagePath(
                    'Chamilo\Core\Repository\Implementation\Matterhorn', 
                    'Tabs/' . self::TAB_GENERAL), 
                $this->get_properties_table()));
        $tabs->add_tab(
            new DynamicContentTab(
                self::TAB_TRACKS, 
                Translation::get('Tracks'), 
                Theme::getInstance()->getImagePath(
                    'Chamilo\Core\Repository\Implementation\Matterhorn', 
                    'Tabs/' . self::TAB_TRACKS), 
                $this->get_tracks_table()));
        
        $html[] = $tabs->render();
        
        return implode(PHP_EOL, $html);
    }
}
