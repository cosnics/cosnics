<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn;

use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Format\Utilities\Html5MediaValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class Track
{

    private $id;

    private $ref;

    private $type;

    private $mimetype;

    private $tags;

    private $url;

    private $checksum;

    private $duration;

    private $audio;

    private $video;

    /**
     *
     * @return the $mimetype
     */
    /**
     *
     * @return the $id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     *
     * @return the $ref
     */
    public function get_ref()
    {
        return $this->ref;
    }

    /**
     *
     * @return the $type
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     *
     * @param $id the $id to set
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param $ref the $ref to set
     */
    public function set_ref($ref)
    {
        $this->ref = $ref;
    }

    /**
     *
     * @param $type the $type to set
     */
    public function set_type($type)
    {
        $this->type = $type;
    }

    public function get_mimetype()
    {
        return $this->mimetype;
    }

    /**
     *
     * @return the $tags
     */
    public function get_tags()
    {
        return $this->tags;
    }

    /**
     *
     * @return the $url
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     *
     * @return the $checksum
     */
    public function get_checksum()
    {
        return $this->checksum;
    }

    /**
     *
     * @return the $duration
     */
    public function get_duration()
    {
        return $this->duration;
    }

    /**
     *
     * @return the $audio
     */
    public function get_audio()
    {
        return $this->audio;
    }

    /**
     *
     * @return the $video
     */
    public function get_video()
    {
        return $this->video;
    }

    /**
     *
     * @param $mimetype the $mimetype to set
     */
    public function set_mimetype($mimetype)
    {
        $this->mimetype = $mimetype;
    }

    /**
     *
     * @param $tags the $tags to set
     */
    public function set_tags($tags)
    {
        $this->tags = $tags;
    }

    /**
     *
     * @param $url the $url to set
     */
    public function set_url($url)
    {
        $this->url = $url;
    }

    /**
     *
     * @param $checksum the $checksum to set
     */
    public function set_checksum($checksum)
    {
        $this->checksum = $checksum;
    }

    /**
     *
     * @param $duration the $duration to set
     */
    public function set_duration($duration)
    {
        $this->duration = $duration;
    }

    /**
     *
     * @param $audio the $audio to set
     */
    public function set_audio($audio)
    {
        $this->audio = $audio;
    }

    /**
     *
     * @param $video the $video to set
     */
    public function set_video($video)
    {
        $this->video = $video;
    }

    public function as_string()
    {
        $html = array();
        
        $html[] = '<table class="no_border"><tr><td style="width: 22px;">';
        $html[] = Utilities :: mimetype_to_image($this->get_mimetype());
        $html[] = '</td><td>';
        if ($this->get_video())
        {
            $html[] = '<b>' . Translation :: get('Video') . ':</b> ' . $this->get_video()->as_string();
            $html[] = '<br/>';
        }
        if ($this->get_audio())
        {
            $html[] = '<b>' . Translation :: get('Audio') . ':</b> ' . $this->get_audio()->as_string();
        }
        $html[] = '</td></tr></table>';
        return implode("", $html);
    }

    public function get_mimetype_type()
    {
        $type = explode('/', $this->get_mimetype());
        return $type[0];
    }

    public function is_audio()
    {
        if ($this->get_mimetype_type() == 'audio')
        {
            return true;
        }
        return false;
    }

    public function is_video()
    {
        if ($this->get_mimetype_type() == 'video')
        {
            return true;
        }
        return false;
    }

    public function is_html5()
    {
        $audio = ($this->get_audio() && $this->get_audio()->is_html5()) || ! $this->get_audio();
        $video = ($this->get_video() && $this->get_video()->is_html5()) || ! $this->get_video();
        $audio_or_video = $this->get_audio() || $this->get_video();
        
        $url = explode('/', $this->get_url());
        $file_name = $url[sizeof($url)];
        
        $name = explode('.', $file_name);
        $extension = $name[sizeof($name)];
        
        if ($video && $audio && $audio_or_video)
        {
            $codecs = array();
            if ($this->get_audio())
            {
                $codecs[] = $this->get_audio()->get_encoder();
            }
            if ($this->get_video())
            {
                $codecs[] = $this->get_video()->get_encoder();
            }
            return Html5MediaValidator :: is_container($extension, $this->get_mimetype(), $codecs);
        }
        else
        {
            return false;
        }
    }

    public function get_quality()
    {
        if (! $this->is_video())
        {
            return false;
        }
        
        return $this->get_video()->get_quality();
    }

    public function get_extension()
    {
        $file_properties = FileProperties :: from_url($this->get_url());
        return $file_properties->get_extension();
    }
}
