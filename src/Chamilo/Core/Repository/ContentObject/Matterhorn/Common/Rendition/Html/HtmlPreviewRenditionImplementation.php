<?php
namespace Chamilo\Core\Repository\ContentObject\Matterhorn\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Matterhorn\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

class HtmlPreviewRenditionImplementation extends HtmlRenditionImplementation
{
    const PARAM_QUALITY = 'quality';

    public function render()
    {
        $object = $this->get_object();
        
        $settings = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting::get(
            'url', 
            $object->get_external_repository_id());
        
        $html = array();
        
        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\Implementation\Matterhorn', true) .
                 'Plugin/Projekktor/projekktor.js');
        
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
                    $track_code = array();
                    $track_code[] = $i . ' : { src : \'';
                    $track_code[] = $track->get_url();
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
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\Implementation\Matterhorn', true) .
                     'Plugin/jquery.jplayer.js');
            $audio[] = '<script type="text/javascript">
$(document).ready(function(){

	$("#jquery_jplayer_' . $this->get_content_object()->get_id() . '").jPlayer({
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
		toggleDuration: true,
        cssSelectorAncestor: "#jp_container_' . $this->get_content_object()->get_id() . '"
	});
});
</script>';
            
            $audio[] = ' <div id="jquery_jplayer_' . $this->get_content_object()->get_id() . '" class="jp-jplayer"></div>

		<div id="jp_container_' . $this->get_content_object()->get_id() . '" class="jp-audio">
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
        
        return implode(PHP_EOL, $html);
    }
}
