<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn\Parser\MediaPackage;

use Chamilo\Core\Repository\Implementation\Matterhorn\Attachment;
use Chamilo\Core\Repository\Implementation\Matterhorn\ExternalObject;
use Chamilo\Core\Repository\Implementation\Matterhorn\Metadata;
use Chamilo\Core\Repository\Implementation\Matterhorn\Parser\MediaPackageParser;
use Chamilo\Core\Repository\Implementation\Matterhorn\Track;
use Chamilo\Core\Repository\Implementation\Matterhorn\TrackAudio;
use Chamilo\Core\Repository\Implementation\Matterhorn\TrackVideo;

class ObjectMediaPackageParser extends MediaPackageParser
{

    public function process()
    {
        $result = $this->get_response();
        $matterhorn_external_repository_object = new ExternalObject();
        $matterhorn_external_repository_object->set_id($result->id);
        $matterhorn_external_repository_object->set_owner_id($result->creators->creator);
        $matterhorn_external_repository_object->set_created(strtotime($result->start));
        $matterhorn_external_repository_object->set_start_time($result->start);
        $matterhorn_external_repository_object->set_duration($result->duration);
        $matterhorn_external_repository_object->set_title($result->title);
        $matterhorn_external_repository_object->set_series_id($result->series);
        
        $matterhorn_external_repository_object_track = new Track();
        foreach ($result->media->track as $track)
        {
            $matterhorn_external_repository_object_track->set_type($track->type);
            $matterhorn_external_repository_object_track->set_id($track->id);
            $matterhorn_external_repository_object_track->set_mimetype($track->mimetype);
            $matterhorn_external_repository_object_track->set_tags($track->tags->tag);
            
            $matterhorn_external_repository_object_track->set_url($track->url);
            $matterhorn_external_repository_object_track->set_checksum($track->checksum);
            $matterhorn_external_repository_object_track->set_duration($track->duration);
            
            $matterhorn_external_repository_object_track_video = new TrackVideo();
            $matterhorn_external_repository_object_track_video->set_id($track->video->id);
            $matterhorn_external_repository_object_track_video->set_device($track->video->device);
            $matterhorn_external_repository_object_track_video->set_encoder($track->video->encoder);
            $matterhorn_external_repository_object_track_video->set_bitrate($track->video->bitrate);
            $matterhorn_external_repository_object_track_video->set_framerate($track->video->framerate);
            $matterhorn_external_repository_object_track_video->set_resolution($track->video->resolution);
            $matterhorn_external_repository_object_track->set_video($matterhorn_external_repository_object_track_video);
            
            $matterhorn_external_repository_object_track_audio = new TrackAudio();
            $matterhorn_external_repository_object_track_audio->set_id($track->audio->id);
            $matterhorn_external_repository_object_track_audio->set_device($track->audio->device);
            $matterhorn_external_repository_object_track_audio->set_encoder($track->audio->encoder);
            $matterhorn_external_repository_object_track_audio->set_bitrate($track->audio->bitrate);
            $matterhorn_external_repository_object_track_audio->set_channels($track->audio->channels);
            $matterhorn_external_repository_object_track_audio->set_bitdepth($track->audio->bitdepth);
            $matterhorn_external_repository_object_track_audio->set_samplingrate($track->audio->sampligrate);
            $matterhorn_external_repository_object_track->set_audio($matterhorn_external_repository_object_track_audio);
        }
        $matterhorn_external_repository_object->set_tracks($matterhorn_external_repository_object_track);
        
        foreach ($result->metadata->catalog as $catalog)
        {
            $matterhorn_external_repository_object_metadata = new Metadata();
            $matterhorn_external_repository_object_metadata->set_type($catalog->type);
            $matterhorn_external_repository_object_metadata->set_ref($catalog->ref);
            $matterhorn_external_repository_object_metadata->set_id($catalog->id);
            $matterhorn_external_repository_object_metadata->set_mimetype($catalog->mimetype);
            $matterhorn_external_repository_object_metadata->set_url($catalog->url);
            $matterhorn_external_repository_object_metadata->set_tags($catalog->tags->tag);
            $matterhorn_external_repository_object->add_metadata($matterhorn_external_repository_object_metadata);
        }
        
        foreach ($result->attachments->attachment as $attachment)
        {
            $matterhorn_external_repository_object_attachment = new Attachment();
            $matterhorn_external_repository_object_attachment->set_type($attachment->type);
            $matterhorn_external_repository_object_attachment->set_ref($attachment->ref);
            $matterhorn_external_repository_object_attachment->set_id($attachment->id);
            $matterhorn_external_repository_object_attachment->set_mimetype($attachment->mimetype);
            $matterhorn_external_repository_object_attachment->set_url($attachment->url);
            $matterhorn_external_repository_object_attachment->set_tags($attachment->tags->tag);
            $matterhorn_external_repository_object->add_attachment($matterhorn_external_repository_object_attachment);
        }
        return $matterhorn_external_repository_object;
    }
}
