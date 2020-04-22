<?php
namespace Chamilo\Libraries\File\ImageManipulation\Gd;

use Chamilo\Libraries\File\ImageManipulation\ImageManipulation;
use Exception;

/**
 * This class provide image manipulation using php's GD-extension
 *
 * @package Chamilo\Libraries\File\ImageManipulation\Gd$GdImageManipulation
 */
class GdImageManipulation extends ImageManipulation
{

    /**
     * @var resource
     */
    private $gdImage = null;

    /**
     *
     * @param string $sourceFile
     *
     * @throws \Exception
     */
    public function __construct($sourceFile)
    {
        parent::__construct($sourceFile);
        $this->load_gd_image();
    }

    /**
     * @param integer $width
     * @param integer $height
     * @param integer $offsetX
     * @param integer $offsetY
     *
     * @return boolean
     */
    public function crop(
        $width, $height, $offsetX = ImageManipulation::CROP_CENTER, $offsetY = ImageManipulation::CROP_CENTER
    )
    {
        if (!function_exists('imagecopy'))
        {
            return false;
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
     * Loads the image file in memory using the imagecreatefromXXX functions.
     *
     * @throws \Exception
     */
    private function load_gd_image()
    {
        $extension = $this->get_image_extension();
        $extension = str_replace('jpg', 'jpeg', $extension);
        $createFunction = 'imagecreatefrom' . $extension;

        if (!function_exists($createFunction))
        {
            throw new Exception($createFunction . ' not found');
        }

        $this->gdImage = $createFunction($this->sourceFile);
    }

    /**
     * Resize an image to an exact set of dimensions, ignoring aspect ratio.
     *
     * @param integer $width The width of the image after resizing
     * @param integer $height The height of the image after resizing
     *
     * @return boolean True if successfull, false if not
     * @throws \Exception
     */
    public function resize($width, $height)
    {
        if (!function_exists('imagecopyresampled'))
        {
            throw new Exception('imagecopyresampled not found');
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
     * Write the resulting image (after some manipulations to a file)
     *
     * @param string $file Full path of the file to which the image should be written. If null, the original image will
     *     be overwritten.
     *
     * @return boolean
     * @throws \Exception
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

        if (!function_exists($createFunction))
        {
            throw new Exception($createFunction . ' not found');
        }

        return $createFunction($this->gdImage, $file);
    }
}
