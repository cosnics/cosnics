<?php
namespace Chamilo\Libraries\File\ImageManipulation\Gd;

use Chamilo\Libraries\File\ImageManipulation\ImageManipulation;

/**
 * $Id: gd_image_manipulation.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * 
 * @package common.image_manipulation.gd
 */
/**
 * This class provide image manipulation using php's GD-extension
 */
class GdImageManipulation extends ImageManipulation
{

    private $gd_image = null;

    public function __construct($source_file)
    {
        parent::__construct($source_file);
        $this->load_gd_image();
    }

    public function crop($width, $height, $offset_x = ImageManipulation::CROP_CENTER, $offset_y = ImageManipulation::CROP_CENTER)
    {
        if (! function_exists('imagecopy'))
        {
            return FALSE;
        }
        if ($offset_x == ImageManipulation::CROP_CENTER)
        {
            $offset_x = ($this->width - $width) / 2;
        }
        if ($offset_y == ImageManipulation::CROP_CENTER)
        {
            $offset_y = ($this->height - $height) / 2;
        }
        $result = imagecreatetruecolor($width, $height);
        if (imagecopy($result, $this->gd_image, 0, 0, $offset_x, $offset_y, $width, $height))
        {
            $this->gd_image = $result;
            $this->width = $width;
            $this->height = $height;
            return true;
        }
        return false;
    }

    public function resize($width, $height)
    {
        if (! function_exists('imagecopyresampled'))
        {
            return FALSE;
        }
        $result = imagecreatetruecolor($width, $height);
        if (imagecopyresampled($result, $this->gd_image, 0, 0, 0, 0, $width, $height, $this->width, $this->height))
        {
            $this->gd_image = $result;
            $this->width = $width;
            $this->height = $height;
            return true;
        }
        return false;
    }

    public function write_to_file($file = null)
    {
        if (is_null($file))
        {
            $file = $this->source_file;
        }
        $extension = $this->get_image_extension();
        $extension = str_replace('jpg', 'jpeg', $extension);
        $create_function = 'image' . $extension;
        if (! function_exists($create_function))
        {
            return FALSE;
        }
        return $create_function($this->gd_image, $file);
    }

    /**
     * Loads the image file in memory using the imagecreatefromXXX functions.
     */
    private function load_gd_image()
    {
        $extension = $this->get_image_extension();
        $extension = str_replace('jpg', 'jpeg', $extension);
        $create_function = 'imagecreatefrom' . $extension;
        if (! function_exists($create_function))
        {
            return FALSE;
        }
        $this->gd_image = $create_function($this->source_file);
    }
}
