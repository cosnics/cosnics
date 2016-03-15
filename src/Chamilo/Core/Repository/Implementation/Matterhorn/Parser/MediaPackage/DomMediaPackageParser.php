<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn\Parser\MediaPackage;

use Chamilo\Core\Repository\Implementation\Matterhorn\Attachment;
use Chamilo\Core\Repository\Implementation\Matterhorn\ExternalObject;
use Chamilo\Core\Repository\Implementation\Matterhorn\Metadata;
use Chamilo\Core\Repository\Implementation\Matterhorn\Parser\MediaPackageParser;
use Chamilo\Core\Repository\Implementation\Matterhorn\Track;
use Chamilo\Core\Repository\Implementation\Matterhorn\TrackAudio;
use Chamilo\Core\Repository\Implementation\Matterhorn\TrackVideo;
use Chamilo\Libraries\Utilities\StringUtilities;

class DomMediaPackageParser extends MediaPackageParser
{

    public function process()
    {
        $result = $this->get_response();
        $media_package = $result->getElementsByTagName('mediapackage')->item(0);
        $matterhorn_external_repository_object = new ExternalObject();
        $matterhorn_external_repository_object->set_xml($media_package);
        $matterhorn_external_repository_object->set_id($media_package->getAttribute('id'));
        
        $matterhorn_external_repository_object->set_start_time(
            $result->getElementsByTagName('dcCreated')->item(0)->nodeValue);
        $matterhorn_external_repository_object->set_duration(
            $result->getElementsByTagName('dcExtent')->item(0)->nodeValue);
        $matterhorn_external_repository_object->set_title(
            $media_package->getElementsByTagName('title')->item(0)->nodeValue);
        $matterhorn_external_repository_object->set_description(
            $result->getElementsByTagName('dcDescription')->item(0)->nodeValue);
        $matterhorn_external_repository_object->set_contributors(
            $media_package->getElementsByTagName('contributor')->item(0)->nodeValue);
        $matterhorn_external_repository_object->set_creators(
            $media_package->getElementsByTagName('creator')->item(0)->nodeValue);
        
        if ($media_package->getElementsByTagName('series')->length > 0)
        {
            $matterhorn_external_repository_object->set_series_id(
                $media_package->getElementsByTagName('series')->item(0)->nodeValue);
        }
        
        $matterhorn_external_repository_object->set_owner_id(
            $media_package->getElementsByTagName('creator')->item(0)->nodeValue);
        $matterhorn_external_repository_object->set_created(
            strtotime($result->getElementsByTagName('dcCreated')->item(0)->nodeValue));
        
        $matterhorn_external_repository_object->set_subjects(
            $media_package->getElementsByTagName('subject')->item(0)->nodeValue);
        $matterhorn_external_repository_object->set_license(
            $media_package->getElementsByTagName('license')->item(0)->nodeValue);
        $matterhorn_external_repository_object->set_type(
            (string) StringUtilities :: getInstance()->createString(
                $result->getElementsByTagName('mediaType')->item(0)->nodeValue)->underscored());
        $matterhorn_external_repository_object->set_modified(
            strtotime($result->getElementsByTagName('modified')->item(0)->nodeValue));
        
        foreach ($media_package->getElementsByTagName('media')->item(0)->getElementsByTagName('track') as $media_track)
        {
            $track = new Track();
            $track->set_ref($media_track->getAttribute('ref'));
            $track->set_type($media_track->getAttribute('type'));
            $track->set_id($media_track->getAttribute('id'));
            $track->set_mimetype($media_track->getElementsByTagName('mimetype')->item(0)->nodeValue);
            
            $tags = array();
            foreach ($media_track->getElementsByTagName('tag') as $tag)
            {
                $tags[] = $tag->nodeValue;
            }
            
            $track->set_tags($tags);
            
            $track->set_url($media_track->getElementsByTagName('url')->item(0)->nodeValue);
            $track->set_checksum($media_track->getElementsByTagName('checksum')->item(0)->nodeValue);
            $track->set_duration($media_track->getElementsByTagName('duration')->item(0)->nodeValue);
            
            if ($media_track->getElementsByTagName('audio')->length > 0)
            {
                $audio_track = $media_track->getElementsByTagName('audio')->item(0);
                $audio = new TrackAudio();
                $audio->set_id($audio_track->getAttribute('id'));
                $audio->set_device($audio_track->getElementsByTagName('device')->item(0)->nodeValue);
                $audio->set_encoder($audio_track->getElementsByTagName('encoder')->item(0)->getAttribute('type'));
                $audio->set_bitdepth($audio_track->getElementsByTagName('bitdepth')->item(0)->nodeValue);
                $audio->set_channels($audio_track->getElementsByTagName('channels')->item(0)->nodeValue);
                $audio->set_samplingrate($audio_track->getElementsByTagName('samplingrate')->item(0)->nodeValue);
                $audio->set_bitrate($audio_track->getElementsByTagName('bitrate')->item(0)->nodeValue);
                $track->set_audio($audio);
            }
            
            if ($media_track->getElementsByTagName('video')->length > 0)
            {
                $video_track = $media_track->getElementsByTagName('video')->item(0);
                $video = new TrackVideo();
                $video->set_id($video_track->getAttribute('id'));
                $video->set_device($video_track->getElementsByTagName('device')->item(0)->nodeValue);
                $video->set_encoder($video_track->getElementsByTagName('encoder')->item(0)->getAttribute('type'));
                $video->set_framerate($video_track->getElementsByTagName('framerate')->item(0)->nodeValue);
                $video->set_bitrate($video_track->getElementsByTagName('bitrate')->item(0)->nodeValue);
                $video->set_resolution($video_track->getElementsByTagName('resolution')->item(0)->nodeValue);
                $video->set_scantype($video_track->getElementsByTagName('scantype')->item(0)->nodeValue);
                
                $track->set_video($video);
            }
            $matterhorn_external_repository_object->add_track($track);
        }
        
        foreach ($media_package->getElementsByTagName('attachments')->item(0)->getElementsByTagName('attachment') as $attachment)
        {
            $attach = new Attachment();
            $attach->set_id($attachment->getAttribute('id'));
            $attach->set_ref($attachment->getAttribute('ref'));
            $attach->set_type($attachment->getAttribute('type'));
            $attach->set_mimetype($attachment->getElementsByTagName('mimetype')->item(0)->nodeValue);
            
            $tags = array();
            foreach ($attachment->getElementsByTagName('tag') as $tag)
            {
                $tags[] = $tag->nodeValue;
            }
            $attach->set_tags($tags);
            
            $attach->set_url($attachment->getElementsByTagName('url')->item(0)->nodeValue);
            $matterhorn_external_repository_object->add_attachment($attach);
        }
        
        foreach ($media_package->getElementsByTagName('metadata')->item(0)->getElementsByTagName('catalog') as $metadata)
        {
            $catalog = new Metadata();
            $catalog->set_id($metadata->getAttribute('id'));
            $catalog->set_ref($metadata->getAttribute('ref'));
            
            $tags = array();
            foreach ($metadata->getElementsByTagName('tag') as $tag)
            {
                $tags[] = $tag->nodeValue;
            }
            $catalog->set_tags($tags);
            
            $catalog->set_type($metadata->getAttribute('type'));
            $catalog->set_mimetype($metadata->getElementsByTagName('mimetype')->item(0)->nodeValue);
            
            $catalog->set_url($metadata->getElementsByTagName('url')->item(0)->nodeValue);
            $matterhorn_external_repository_object->add_metadata($catalog);
        }
        return $matterhorn_external_repository_object;
    }
}
