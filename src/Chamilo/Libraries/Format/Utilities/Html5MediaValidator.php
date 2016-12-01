<?php
namespace Chamilo\Libraries\Format\Utilities;

class Html5MediaValidator
{

    public function is_video($codec)
    {
        return true;
    }

    public function is_audio($codec)
    {
        return true;
    }

    public function is_container($extension, $mimetype, $codecs)
    {
        if ($extension && ! in_array($extension, Html5Format::get_extensions()))
        {
            return false;
        }
        
        if ($mimetype && ! in_array($mimetype, Html5Format::get_mimetypes()))
        {
            return false;
        }
        
        if (count($codecs) > 1)
        {
            $value = Html5Format::codecs_compatible();
            if (key_exists($codecs[0], $value) && in_array($codecs[1], $value[$codecs[0]]))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return true;
        }
    }
}
