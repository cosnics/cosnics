<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn;

use Chamilo\Core\Repository\External\General\Streaming\StreamingMediaExternalObject;
use DOMDocument;

/**
 *
 * @author magali.gillard
 */
class ExternalObject extends StreamingMediaExternalObject
{
    const OBJECT_TYPE = 'matterhorn';
    const PROPERTY_SERIES_ID = 'series_id';
    const PROPERTY_SERIES = 'series';
    const PROPERTY_CONTRIBUTORS = 'contributors';
    const PROPERTY_SUBJECTS = 'subjects';
    const PROPERTY_LANGUAGE = 'language';
    const PROPERTY_LICENSE = 'license';
    const PROPERTY_TRACKS = 'tracks';
    const PROPERTY_STREAMING = 'streaming';
    const PROPERTY_ATTACHMENTS = 'attachments';
    const PROPERTY_METADATA = 'metadata';
    const PROPERTY_XML = 'xml';
    const PROPERTY_ID = 'id';
    const PROPERTY_START_TIME = 'start_time';
    const PROPERTY_DURATION = 'duration';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_CATALOGS = 'catalogs';
    const PROPERTY_CREATORS = 'creators';

    public function get_xml()
    {
        return $this->get_default_property(self :: PROPERTY_XML);
    }

    public function set_xml($xml)
    {
        return $this->set_default_property(self :: PROPERTY_XML, $xml);
    }

    public function get_metadata()
    {
        return $this->get_default_property(self :: PROPERTY_METADATA);
    }

    public function set_metadata($metadata)
    {
        return $this->set_default_property(self :: PROPERTY_METADATA, $metadata);
    }

    public function get_attachments()
    {
        return $this->get_default_property(self :: PROPERTY_ATTACHMENTS);
    }

    public function set_attachments($attachments)
    {
        return $this->set_default_property(self :: PROPERTY_ATTACHMENTS, $attachments);
    }

    public function get_series_id()
    {
        return $this->get_default_property(self :: PROPERTY_SERIES_ID);
    }

    public function set_series_id($series_id)
    {
        return $this->set_default_property(self :: PROPERTY_SERIES_ID, $series_id);
    }

    public function get_series()
    {
        return $this->get_connector()->get_series($this->get_series_id());
    }

    public function get_contributors()
    {
        return $this->get_default_property(self :: PROPERTY_CONTRIBUTORS);
    }

    public function set_contributors($contributors)
    {
        return $this->set_default_property(self :: PROPERTY_CONTRIBUTORS, $contributors);
    }

    public function get_subjects()
    {
        return $this->get_default_property(self :: PROPERTY_SUBJECTS);
    }

    public function set_subjects($subjects)
    {
        return $this->set_default_property(self :: PROPERTY_SUBJECTS, $subjects);
    }

    public function get_language()
    {
        return $this->get_default_property(self :: PROPERTY_LANGUAGE);
    }

    public function set_language($language)
    {
        return $this->set_default_property(self :: PROPERTY_LANGUAGE, $language);
    }

    public function set_streaming($streaming)
    {
        $this->set_default_property(self :: PROPERTY_STREAMING, $streaming);
    }

    public function get_streaming()
    {
        return $this->get_default_property(self :: PROPERTY_STREAMING);
    }

    public function set_tracks($tracks)
    {
        $this->set_default_property(self :: PROPERTY_TRACKS, $tracks);
    }

    public function get_tracks()
    {
        return $this->get_default_property(self :: PROPERTY_TRACKS);
    }

    public function get_audio_tracks()
    {
        $tracks = $this->get_default_property(self :: PROPERTY_TRACKS);
        $audio_tracks = array();
        foreach ($tracks as $track)
        {
            if ($track->is_audio())
            {
                $audio_tracks[] = $track;
            }
        }
        return $audio_tracks;
    }

    public function get_video_tracks()
    {
        $tracks = $this->get_default_property(self :: PROPERTY_TRACKS);
        $video_tracks = array();
        foreach ($tracks as $track)
        {
            if ($track->is_video())
            {
                $video_tracks[] = $track;
            }
        }
        return $video_tracks;
    }

    public function set_license($license)
    {
        $this->set_default_property(self :: PROPERTY_LICENSE, $license);
    }

    public function get_license()
    {
        return $this->get_default_property(self :: PROPERTY_LICENSE);
    }

    public function get_start_time()
    {
        return $this->get_default_property(self :: PROPERTY_START_TIME);
    }

    public function set_start_time($start_time)
    {
        $this->set_default_property(self :: PROPERTY_START_TIME, $start_time);
    }

    public function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    public function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    public function get_duration()
    {
        return $this->get_default_property(self :: PROPERTY_DURATION);
    }

    public function set_duration($duration)
    {
        $this->set_default_property(self :: PROPERTY_DURATION, $duration);
    }

    public function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    public function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    public function get_creators()
    {
        return $this->get_default_property(self :: PROPERTY_CREATORS);
    }

    public function set_creators($creators)
    {
        $this->set_default_property(self :: PROPERTY_CREATORS, $creators);
    }

    public function get_catalogs()
    {
        return $this->get_default_property(self :: PROPERTY_CATALOGS);
    }

    public function set_catalogs($catalogs)
    {
        $this->set_default_property(self :: PROPERTY_CATALOGS, $catalogs);
    }

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_SUBJECTS, 
                self :: PROPERTY_LANGUAGE, 
                self :: PROPERTY_CONTRIBUTORS, 
                self :: PROPERTY_CONTRIBUTORS, 
                self :: PROPERTY_TRACKS, 
                self :: PROPERTY_LICENSE, 
                self :: PROPERTY_STREAMING));
    }

    public static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }

    public function add_track($track)
    {
        $tracks = $this->get_tracks();
        $tracks[] = $track;
        $this->set_tracks($tracks);
    }

    public function add_attachment($attachment)
    {
        $attachments = $this->get_attachments();
        $attach = explode('/', $attachment->get_type());
        
        $attachments[$attach[1]] = $attachment;
        $this->set_attachments($attachments);
    }

    public function add_metadata($catalog)
    {
        $metadata = $this->get_metadata();
        $type = explode('/', $catalog->get_type());
        
        $metadata[$type[1]] = $catalog;
        $this->set_metadata($metadata);
    }

    public function get_search_preview()
    {
        $attachments = $this->get_attachments();
        return $attachments['search+preview'];
    }

    public function is_usable()
    {
        return \Chamilo\Core\Repository\External\ExternalObject :: is_usable();
    }

    public function render_xml()
    {
        $document = new DOMDocument();
        $document->formatOutput = true;
        
        $mediapackage = $document->createElement('mediapackage');
        $document->appendChild($mediapackage);
        
        $start_time = $document->createAttribute('start');
        $start_time->appendChild($document->createTextNode($this->get_start_time()));
        $mediapackage->appendChild($start_time);
        
        $id = $document->createAttribute('id');
        $id->appendChild($document->createTextNode($this->get_id()));
        $mediapackage->appendChild($id);
        
        $duration = $document->createAttribute('duration');
        $duration->appendChild($document->createTextNode($this->get_duration()));
        $mediapackage->appendChild($duration);
        
        $title = $document->createElement('title');
        $title->appendChild($document->createTextNode($this->get_title()));
        $mediapackage->appendChild($title);
        
        $creators = $document->createElement('creators');
        $mediapackage->appendChild($creators);
        
        $creator_elem = $document->createElement('creator');
        $creator_elem->appendChild($document->createTextNode($this->get_creators()));
        $creators->appendChild($creator_elem);
        
        $contributors = $document->createElement('contributors');
        $mediapackage->appendChild($contributors);
        
        $contributor_elem = $document->createElement('contributor');
        $contributor_elem->appendChild($document->createTextNode($this->get_contributors()));
        $contributors->appendChild($contributor_elem);
        
        $subjects = $document->createElement('subjects');
        $mediapackage->appendChild($subjects);
        
        $subject_elem = $document->createElement('subject');
        $subject_elem->appendChild($document->createTextNode($this->get_subjects()));
        $subjects->appendChild($subject_elem);
        
        $language = $document->createElement('language');
        $language->appendChild($document->createTextNode($this->get_language()));
        $mediapackage->appendChild($language);
        
        $license = $document->createElement('license');
        $license->appendChild($document->createTextNode($this->get_license()));
        $mediapackage->appendChild($license);
        
        $series = $document->createElement('series');
        $series->appendChild($document->createTextNode($this->get_series()->get_id()));
        $mediapackage->appendChild($series);
        
        $series_title = $document->createElement('seriestitle');
        $series_title->appendChild($document->createTextNode($this->get_series()->get_title()));
        $mediapackage->appendChild($series_title);
        
        // medias
        $media = $document->createElement('media');
        $mediapackage->appendChild($media);
        
        // tracks
        foreach ($this->get_tracks() as $track)
        {
            $track_elem = $document->createElement('track');
            $media->appendChild($track_elem);
            
            $ref = $document->createAttribute('ref');
            $ref->appendChild($document->createTextNode($track->get_ref()));
            $track_elem->appendChild($ref);
            
            $type = $document->createAttribute('type');
            $type->appendChild($document->createTextNode($track->get_type()));
            $track_elem->appendChild($type);
            
            $id = $document->createAttribute('id');
            $id->appendChild($document->createTextNode($track->get_id()));
            $track_elem->appendChild($id);
            
            $mimetype = $document->createElement('mimetype');
            $mimetype->appendChild($document->createTextNode($track->get_mimetype()));
            $track_elem->appendChild($mimetype);
            
            $tags = $document->createElement('tags');
            $track_elem->appendChild($tags);
            
            foreach ($track->get_tags() as $tag)
            {
                $tag_elem = $document->createElement('tag');
                $tag_elem->appendChild($document->createTextNode($tag));
                $tags->appendChild($tag_elem);
            }
            
            $url = $document->createElement('url');
            $url->appendChild($document->createTextNode($track->get_url()));
            $track_elem->appendChild($url);
            
            $checksum = $document->createElement('checksum');
            $track_elem->appendChild($checksum);
            
            $checksum_type = $document->createAttribute('type');
            $checksum_type->appendChild($document->createTextNode($track->get_checksum()));
            $checksum->appendChild($checksum_type);
            
            $duration = $document->createElement('duration');
            $duration->appendChild($document->createTextNode($track->get_duration()));
            $track_elem->appendChild($duration);
            
            // audio_track
            $audio_elem = $document->createElement('audio');
            $track_elem->appendChild($audio_elem);
            
            if ($track->get_audio())
            {
                $id = $document->createAttribute('id');
                $id->appendChild($document->createTextNode($track->get_audio()->get_id()));
                $audio_elem->appendChild($id);
                
                $device = $document->createElement('device');
                $device->appendChild($document->createTextNode($track->get_audio()->get_device()));
                $audio_elem->appendChild($device);
                
                $encoder = $document->createElement('encoder');
                $audio_elem->appendChild($encoder);
                
                $encoder_type = $document->createAttribute('type');
                $encoder_type->appendChild($document->createTextNode($track->get_audio()->get_encoder()));
                $encoder->appendChild($encoder_type);
                
                $channels = $document->createElement('channels');
                $channels->appendChild($document->createTextNode($track->get_audio()->get_channels()));
                $audio_elem->appendChild($channels);
                
                $bitrate = $document->createElement('bitrate');
                $bitrate->appendChild($document->createTextNode($track->get_audio()->get_bitrate()));
                $audio_elem->appendChild($bitrate);
                
                $bitdepth = $document->createElement('bitdepth');
                $bitdepth->appendChild($document->createTextNode($track->get_audio()->get_bitdepth()));
                $audio_elem->appendChild($bitdepth);
                
                $samplingrate = $document->createElement('samplingrate');
                $samplingrate->appendChild($document->createTextNode($track->get_audio()->get_samplingrate()));
                $audio_elem->appendChild($samplingrate);
            }
            // video_track
            $video_elem = $document->createElement('video');
            $track_elem->appendChild($video_elem);
            if ($track->get_video())
            {
                $id = $document->createAttribute('id');
                $id->appendChild($document->createTextNode($track->get_video()->get_id()));
                $video_elem->appendChild($id);
                
                $device = $document->createElement('device');
                $device->appendChild($document->createTextNode($track->get_video()->get_device()));
                $video_elem->appendChild($device);
                
                $encoder = $document->createElement('encoder');
                $encoder->appendChild($document->createTextNode($track->get_video()->get_encoder()));
                $video_elem->appendChild($encoder);
                
                $bitrate = $document->createElement('bitrate');
                $bitrate->appendChild($document->createTextNode($track->get_video()->get_bitrate()));
                $video_elem->appendChild($bitrate);
                
                $framerate = $document->createElement('framerate');
                $framerate->appendChild($document->createTextNode($track->get_video()->get_framerate()));
                $video_elem->appendChild($framerate);
                
                $scantype = $document->createElement('scantype');
                $scantype->appendChild($document->createTextNode($track->get_video()->get_scantype()));
                $video_elem->appendChild($scantype);
                
                $resolution = $document->createElement('resolution');
                $resolution->appendChild($document->createTextNode($track->get_video()->get_resolution()));
                $video_elem->appendChild($resolution);
            }
        }
        // metadata
        $metadata_elem = $document->createElement('metadata');
        $mediapackage->appendChild($metadata_elem);
        
        // catalog
        foreach ($this->get_metadata() as $metadata)
        {
            $catalog = $document->createElement('catalog');
            $metadata_elem->appendChild($catalog);
            
            $ref = $document->createAttribute('ref');
            $ref->appendChild($document->createTextNode($metadata->get_ref()));
            $catalog->appendChild($ref);
            
            $type = $document->createAttribute('type');
            $type->appendChild($document->createTextNode($metadata->get_type()));
            $catalog->appendChild($type);
            
            $id = $document->createAttribute('id');
            $id->appendChild($document->createTextNode($metadata->get_id()));
            $catalog->appendChild($id);
            
            $mimetype = $document->createElement('mimetype');
            $mimetype->appendChild($document->createTextNode($metadata->get_mimetype()));
            $catalog->appendChild($mimetype);
            
            $tags = $document->createElement('tags');
            $catalog->appendChild($tags);
            
            foreach ($metadata->get_tags() as $tag)
            {
                $tag_elem = $document->createElement('tag');
                $tag_elem->appendChild($document->createTextNode($tag));
                $tags->appendChild($tag_elem);
            }
            
            $url = $document->createElement('url');
            $url->appendChild($document->createTextNode($metadata->get_url()));
            $catalog->appendChild($url);
        }
        // attachments
        $attachments = $document->createElement('attachments');
        $mediapackage->appendChild($attachments);
        
        // attachment
        foreach ($this->get_attachments() as $attachment)
        {
            $attachment_elem = $document->createElement('attachment');
            $attachments->appendChild($attachment_elem);
            
            $ref = $document->createAttribute('ref');
            $ref->appendChild($document->createTextNode($attachment->get_ref()));
            $attachment_elem->appendChild($ref);
            
            $type = $document->createAttribute('type');
            $type->appendChild($document->createTextNode($attachment->get_type()));
            $attachment_elem->appendChild($type);
            
            $id = $document->createAttribute('id');
            $id->appendChild($document->createTextNode($attachment->get_id()));
            $attachment_elem->appendChild($id);
            
            $mimetype = $document->createElement('mimetype');
            $mimetype->appendChild($document->createTextNode($attachment->get_mimetype()));
            $attachment_elem->appendChild($mimetype);
            
            $tags = $document->createElement('tags');
            $attachment_elem->appendChild($tags);
            
            foreach ($attachment->get_tags() as $tag)
            {
                $tag_elem = $document->createElement('tag');
                $tag_elem->appendChild($document->createTextNode($tag));
                $tags->appendChild($tag_elem);
            }
            
            $url = $document->createElement('url');
            $url->appendChild($document->createTextNode($attachment->get_url()));
            $attachment_elem->appendChild($url);
        }
        
        // dump(htmlentities($document->saveXML()));
        
        $this->set_xml($document);
    }

    public function is_html5()
    {
        foreach ($this->get_tracks() as $track)
        {
            if ($track->is_html5())
            {
                return true;
            }
        }
    }
}
