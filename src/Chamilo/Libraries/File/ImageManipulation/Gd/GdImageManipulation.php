<?php
namespace Chamilo\Libraries\File\ImageManipulation\Gd;

use Chamilo\Libraries\File\ImageManipulation\ImageManipulation;

/**
 * This class provide image manipulation using php's GD-extension
 *
 * @package Chamilo\Libraries\File\ImageManipulation\Gd$GdImageManipulation
 */
class GdImageManipulation extends ImageManipulation
{

    private $gdImage = null;

    /**
     *
     * @param string $sourceFile
     */
    public function __construct($sourceFile)
    {
        parent::__construct($sourceFile);
        $this->load_gd_image();
    }

    /**
     *
     * @see \Chamilo\Libraries\File\ImageManipulation\ImageManipulation::crop()
     */
    public function crop($width, $height, $offsetX = ImageManipulation::CROP_CENTER, $offsetY = ImageManipulation::CROP_CENTER)
    {
        if (! function_exists('imagecopy'))
        {
            return FALSE;
        }

        if ($offsetX == ImageManipulation::CROP_CENTER)
        {
            $offsetX = ($this->width - $width) / 2;
        }

        if ($offsetY == ImageManipulation::CROP_CENTER)
        {
            $offsetY = ($this->height - $height) / 2;
        }

        $result = imagecreatetruecolor($width, $height);

        if (imagecopy($result, $this->gdImage, 0, 0, $offsetX, $offsetY, $width, $height))
        {
            $this->gdImage = $result;
            $this->width = $width;
            $this->height = $height;
            return true;
        }

        return false;
    }

    /**
     *
     * @see \Chamilo\Libraries\File\ImageManipulation\ImageManipulation::resize()
     */
    public function resize($width, $height)
    {
        if (! function_exists('imagecopyresampled'))
        {
            return FALSE;
        }

        $result = imagecreatetruecolor($width, $height);

        if (imagecopyresampled($result, $this->gdImage, 0, 0, 0, 0, $width, $height, $this->width, $this->height))
        {
            $this->gdImage = $result;
            $this->width = $width;
            $this->height = $height;
            return true;
        }

        return false;
    }

    /**
     *
     * @see \Chamilo\Libraries\File\ImageManipulation\ImageManipulation::write_to_file()
     */
    public function write_to_file($file = null)
    {
        if (is_null($file))
        {
            $file = $this->sourceFile;
        }

        $extension = $this->get_image_extension();
        $extension = str_replace('jpg', 'jpeg', $extension);
        $createFunction = 'image' . $extension;

        if (! function_exists($createFunction))
        {
            return FALSE;
        }

        if($extension == 'png') {
            imagealphablending( $this->gdImage, FALSE );
            imagesavealpha( $this->gdImage, TRUE );
        }

        return $createFunction($this->gdImage, $file);
    }

    /**
     * Loads the image file in memory using the imagecreatefromXXX functions.
     */
    private function load_gd_image()
    {
        $extension = $this->get_image_extension();
        $extension = str_replace('jpg', 'jpeg', $extension);
        $createFunction = 'imagecreatefrom' . $extension;

        if (! function_exists($createFunction))
        {
            return false;
        }

        $this->gdImage = $createFunction($this->sourceFile);
    }
}
