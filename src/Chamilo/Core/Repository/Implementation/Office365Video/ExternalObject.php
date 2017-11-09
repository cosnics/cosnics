<?php
namespace Chamilo\Core\Repository\Implementation\Office365Video;

use Chamilo\Core\Repository\External\General\Streaming\StreamingMediaExternalObject;
use Chamilo\Libraries\Translation\Translation;

class ExternalObject extends StreamingMediaExternalObject
{
    const OBJECT_TYPE = 'office365-video';
    const ID_SEPARATOR = 'ChannelId=';

    /**
     * \brief Sets the ID of Office 365 video object to the concatenation of the video ID and the channel ID.
     * Calls to the Microsoft Video API require always both the channel and the video Id. Therefore we concatenated
     * these ID's and store
     * them as the ID of this external object.
     * 
     * @see See static function getVideoId(...) and getChannelId(...) for splittin the return value of get_id().
     */
    public function setVideoAndChannelId($videoId, $channelId)
    {
        $this->set_id($videoId . self::ID_SEPARATOR . $channelId);
    }

    /**
     *
     * @see setVideoAndChannelId(...)
     */
    public static function getVideoId($videoAndChannelId)
    {
        $splitId = explode(self::ID_SEPARATOR, $videoAndChannelId);
        return $splitId[0];
    }

    /**
     *
     * @see setVideoAndChannelId(...)
     */
    public function getChannelId($videoAndChannelId)
    {
        $splitId = explode(self::ID_SEPARATOR, $videoAndChannelId);
        return $splitId[1];
    }

    public function get_type()
    {
        return 'video';
    }

    public function get_status_text()
    {
        $status = $this->get_status();
        switch ($status)
        {
            case 0 :
                return Translation::get('PendingProcessing');
            case 1 :
                return Translation::get('Processing');
            case 2 :
                return Translation::get('ReadyForPlay');
            case 3 :
                return Translation::get('ErrorOnUploading');
            case 4 :
                return Translation::get('ErrorOnProcessing');
            case 5 :
                return Translation::get('Timeout');
            case 6 :
                return Translation::get('UnsupportedFormat');
            case 7 :
                return Translation::get('CorruptedFile');
            default :
                return Translation::get('Unknown');
        }
    }

    public static function get_object_type()
    {
        return self::OBJECT_TYPE;
    }

    /**
     * Returns HTML embed code.
     */
    public function getVideoEmbedCode($width = 600, $height = 480)
    {
        return $this->get_connector()->getVideoEmbedCode($this->get_id(), $width, $height);
    }
}
