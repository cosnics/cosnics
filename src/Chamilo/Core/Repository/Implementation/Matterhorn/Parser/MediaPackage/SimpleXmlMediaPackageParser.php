<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn\Parser\MediaPackage;

use Chamilo\Core\Repository\Implementation\Matterhorn\Attachment;
use Chamilo\Core\Repository\Implementation\Matterhorn\ExternalObject;
use Chamilo\Core\Repository\Implementation\Matterhorn\Parser\MediaPackageParser;
use Chamilo\Core\Repository\Implementation\Matterhorn\Track;
use Chamilo\Core\Repository\Implementation\Matterhorn\TrackAudio;
use Chamilo\Core\Repository\Implementation\Matterhorn\TrackVideo;
use Chamilo\Libraries\Utilities\StringUtilities;

class SimpleXmlMediaPackageParser extends MediaPackageParser
{

    public function process()
    {
        $result = $this->get_response();
        
        $object = new ExternalObject();
        $object->set_xml($result);
        $object->set_id((string) $result->attributes()->id);
        $object->set_title((string) $result->mediapackage->title);
        $object->set_start_time();
        $object->set_duration((int) $result->dcExtent);
        $object->set_description((string) $result->dcDescription);
        $object->set_created(strtotime((string) $result->dcCreated));
        $object->set_modified(strtotime((string) $result->modified));
        $object->set_license((string) $result->mediapackage->license);
        $object->set_type(
            (string) StringUtilities :: getInstance()->createString((string) $result->mediaType)->underscored());
        $object->set_subjects((string) $result->mediapackage->subjects->subject);
        $object->set_start_time((string) $result->dcCreated);
        $object->set_owner_id((string) $result->mediapackage->creators->creator[0]);
        $object->set_owner_name((string) $result->mediapackage->creators->creator[0]);
        $object->set_contributors((string) $result->mediapackage->contributors->contributor[0]);
        
        if ($result->mediapackage->series)
        {
            $object->set_series_id((string) $result->mediapackage->series);
        }
        
        foreach ($result->mediapackage->media->track as $media_track)
        {
            $track = new Track();
            $track->set_ref((string) $media_track->attributes()->ref);
            $track->set_type((string) $media_track->attributes()->type);
            $track->set_id((string) $media_track->attributes()->id);
            $track->set_mimetype((string) $media_track->mimetype);
            
            $tags = array();
            foreach ($media_track->tags as $tag)
            {
                $tags[] = (string) $tag;
            }
            
            $track->set_tags($tags);
            
            $track->set_url((string) $media_track->url);
            $track->set_checksum((string) $media_track->checksum);
            $track->set_duration((int) $media_track->duration);
            
            if ($media_track->audio instanceof \SimpleXMLElement && $media_track->audio->count() > 0)
            {
                $audio_track = $media_track->audio[0];
                
                $audio = new TrackAudio();
                $audio->set_id((string) $audio_track->attributes()->id);
                $audio->set_device((string) $audio_track->attributes()->device);
                $audio->set_encoder((string) $audio_track->encoder->type);
                $audio->set_bitdepth((string) $audio_track->bitdepth);
                $audio->set_channels((int) $audio_track->channels);
                $audio->set_samplingrate((string) $audio_track->samplingrate);
                $audio->set_bitrate((string) $audio_track->bitrate);
                
                $track->set_audio($audio);
            }
            
            if ($media_track->video instanceof \SimpleXMLElement && $media_track->video->count() > 0)
            {
                $video_track = $media_track->video[0];
                
                $video = new TrackVideo();
                $video->set_id((string) $video_track->attributes()->id);
                $video->set_device((string) $video_track->attributes()->device);
                $video->set_encoder((string) $video_track->encoder->type);
                $video->set_framerate((string) $video_track->framerate);
                $video->set_bitrate((string) $video_track->bitrate);
                $video->set_resolution((string) $video_track->resolution);
                $video->set_scantype((string) $video_track->scantype->attributes()->type);
                
                $track->set_video($video);
            }
            
            $object->add_track($track);
        }
        
        foreach ($result->mediapackage->metadata->catalog as $metadata_catalog)
        {
            $catalog = new Attachment();
            $catalog->set_id((string) $metadata_catalog->attributes()->id);
            $catalog->set_ref((string) $metadata_catalog->attributes()->ref);
            $catalog->set_type((string) $metadata_catalog->attributes()->type);
            $catalog->set_mimetype((string) $metadata_catalog->mimetype);
            
            $tags = array();
            foreach ($metadata_catalog->tags as $tag)
            {
                $tags[] = (string) $tag;
            }
            
            $catalog->set_tags($tags);
            $catalog->set_url((string) $metadata_catalog->url);
            
            $object->add_metadata($catalog);
        }
        
        foreach ($result->mediapackage->attachments->attachment as $attachment)
        {
            $attach = new Attachment();
            $attach->set_id((string) $attachment->attributes()->id);
            $attach->set_ref((string) $attachment->attributes()->ref);
            $attach->set_type((string) $attachment->attributes()->type);
            $attach->set_mimetype((string) $attachment->mimetype);
            
            $tags = array();
            foreach ($attachment->tags as $tag)
            {
                $tags[] = (string) $tag;
            }
            
            $attach->set_tags($tags);
            $attach->set_url((string) $attachment->url);
            
            $object->add_attachment($attach);
        }
        
        return $object;
    }
}
