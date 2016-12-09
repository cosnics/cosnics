<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn;

use Chamilo\Libraries\Format\Utilities\Html5MediaValidator;
use Chamilo\Libraries\Platform\Translation;

class TrackAudio
{

    private $id;

    private $device;

    private $encoder;

    private $bitdepth;

    private $channels;

    private $samplingrate;

    private $bitrate;

    public function get_id()
    {
        return $this->id;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return the $device
     */
    public function get_device()
    {
        return $this->device;
    }

    /**
     *
     * @return the $encoder
     */
    public function get_encoder()
    {
        return $this->encoder;
    }

    /**
     *
     * @return the $bitdepth
     */
    public function get_bitdepth()
    {
        return $this->bitdepth;
    }

    /**
     *
     * @return the $channels
     */
    public function get_channels()
    {
        return $this->channels;
    }

    /**
     *
     * @return the $samplingrate
     */
    public function get_samplingrate()
    {
        return $this->samplingrate;
    }

    /**
     *
     * @return the $bitrate
     */
    public function get_bitrate()
    {
        return $this->bitrate;
    }

    /**
     *
     * @param $device the $device to set
     */
    public function set_device($device)
    {
        $this->device = $device;
    }

    /**
     *
     * @param $encoder the $encoder to set
     */
    public function set_encoder($encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     *
     * @param $bitdepth the $bitdepth to set
     */
    public function set_bitdepth($bitdepth)
    {
        $this->bitdepth = $bitdepth;
    }

    /**
     *
     * @param $channels the $channels to set
     */
    public function set_channels($channels)
    {
        $this->channels = $channels;
    }

    /**
     *
     * @param $samplingrate the $samplingrate to set
     */
    public function set_samplingrate($samplingrate)
    {
        $this->samplingrate = $samplingrate;
    }

    /**
     *
     * @param $bitrate the $bitrate to set
     */
    public function set_bitrate($bitrate)
    {
        $this->bitrate = $bitrate;
    }

    public function as_string()
    {
        $html = array();
        if ($this->get_device())
        {
            $html[] = $this->get_device();
        }
        $html[] = $this->get_encoder();
        $html[] = $this->get_bitdepth() . Translation::get('bit');
        $html[] = $this->get_channels_as_string();
        $html[] = $this->get_samplingrate() . Translation::get('hz');
        $html[] = round($this->get_bitrate() / 1000) . Translation::get('kbps');
        
        return implode(", ", $html);
    }

    private function get_channels_as_string()
    {
        switch ($this->get_channels())
        {
            case 1 :
                return Translation::get('Mono');
                break;
            case 2 :
                return Translation::get('Stereo');
                break;
            
            default :
                return ($this->get_channels() . ' ' . Translation::get('Surround'));
        }
    }

    public function is_html5()
    {
        return Html5MediaValidator::is_audio($this->get_encoder());
    }
}
