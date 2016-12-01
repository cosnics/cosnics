<?php
namespace Chamilo\Libraries\Format\Utilities;

class Html5Format
{

    public static function get_video_extensions()
    {
        $extensions = array();
        
        $extensions[] = 'OGV';
        $extensions[] = 'WEBM';
        $extensions[] = 'MP4';
        
        return $extensions;
    }

    public static function get_audio_extensions()
    {
        $extensions = array();
        
        $extensions[] = 'MP3';
        $extensions[] = 'AAC';
        $extensions[] = 'OGG';
        
        return $extensions;
    }

    public function get_extensions()
    {
        return array_merge(self::get_audio_extensions(), self::get_video_extensions());
    }

    public static function get_video_mimetypes()
    {
        $mimetypes = array();
        
        $mimetypes[] = 'video/webm';
        $mimetypes[] = 'video/ogg';
        $mimetypes[] = 'video/mp4';
        
        return $mimetypes;
    }

    public static function get_audio_mimetypes()
    {
        $mimetypes = array();
        
        $mimetypes[] = 'audio/mp3';
        $mimetypes[] = 'audio/m4a';
        $mimetypes[] = 'audio/vorbis';
        
        return $mimetypes;
    }

    static public 

    function get_mimetypes()
    {
        return array_merge(self::get_audio_mimetypes(), self::get_video_mimetypes());
    }

    static public 

    function get_video_codecs()
    {
        $codecs = array();
        
        $codecs[] = 'Theora';
        $codecs[] = 'AVC';
        $codecs[] = 'VP8';
        
        return $codecs;
    }

    static public 

    function get_audio_codecs()
    {
        $codecs = array();
        
        $codecs[] = 'Vorbis';
        $codecs[] = 'AAC';
        $codecs[] = 'MP3';
        
        return $codecs;
    }

    static public 

    function codecs_compatible()
    {
        $codecs_compatible = array();
        
        $codecs_compatible['VP8'] = array('Vorbis');
        $codecs_compatible['AVC'] = array('AAC');
        $codecs_compatible['Theora'] = array('Vorbis');
        
        $codecs_compatible['Vorbis'] = array('VP8', 'Theora');
        $codecs_compatible['AAC'] = array('AVC');
        
        return $codecs_compatible;
    }
}
