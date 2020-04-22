<?php
namespace Chamilo\Libraries\File\ImageManipulation;

use Chamilo\Libraries\File\ImageManipulation\Gd\GdImageManipulation;

/**
 * An abstract class for handling image manipulations.
 * Implement new image manipulation methods by creating a class which extends this abstract class.
 *
 * @package Chamilo\Libraries\File\ImageManipulation
 */
abstract class ImageManipulation
{
    /**
     * When cropping an image, use this offset value to get the exacte center of
     * the image
     */
    const CROP_CENTER = - 1;

    const DIMENSION_HEIGHT = 1;
    const DIMENSION_WIDTH = 0;

    /**
     * Final dimensions will be less than or equal to the entered width and
     * height.
     * Useful for ensuring a maximum height and/or width.
     */
    const SCALE_INSIDE = 0;

    /**
     * Final dimensions will be greater than or equal to the entered width and
     * height.
     * Ideal for cropping the result to a square.
     */
    const SCALE_OUTSIDE = 1;

    /**
     * The file on which the manipulations will be done
     *
     * @var string
     */
    protected $sourceFile;

    /**
     *
     * @var integer
     */
    protected $width;

    /**
     *
     * @var integer
     */
    protected $height;

    /**
     * Constructor
     *
     * @param string $sourceFile Full path of the image file on which the manipulations should be done
     */
    public function __construct($sourceFile)
    {
        $this->sourceFile = $sourceFile;
        $dimension = getimagesize($sourceFile);
        $this->width = $dimension[self::DIMENSION_WIDTH];
        $this->height = $dimension[self::DIMENSION_HEIGHT];
    }

    /**
     * Creates a thumbnail from by rescaling the image to the given width &
     * height (using the SCALE_OUTSIDE parameter).
     * After this, the resulting
     * image will be cropped. The result is an image which the exact given with
     * and height.
     *
     * @param integer $width With of the resulting image
     * @param integer $height Height of the resulting image (if null, the height will be the same as the width,
     *        resulting in a square image)
     *
     * @return boolean
     */
    public function create_thumbnail($width, $height = null)
    {
        if (is_null($height))
        {
            $height = $width;
        }
        if ($this->scale($width, $height, self::SCALE_OUTSIDE))
        {
            return $this->crop($width, $height);
        }

        return false;
    }

    /**
     * Crop an image to the rectangle specified by the given offsets and
     * dimensions.
     *
     * @param integer $width The width of the image after cropping
     * @param integer $height The height of the image after cropping
     * @param integer $offsetX
     * @param integer $offsetY
     *
     * @return boolean
     */
    abstract public function crop($width, $height, $offsetX = self::CROP_CENTER, $offsetY = self::CROP_CENTER);

    /**
     * Create an imagemanipulation instance
     *
     * @param string $sourceFile Full path of the image file on which the manipulations should be done
     *
     * @return \Chamilo\Libraries\File\ImageManipulation\Gd\GdImageManipulation
     * @throws \Exception
     */
    public static function factory($sourceFile)
    {
        return new GdImageManipulation($sourceFile);
    }

    /**
     * Gets the image extension from the source file.
     *
     * @return string
     */
    protected function get_image_extension()
    {
        $info = getimagesize($this->sourceFile);
        $extensions = array('1' => 'gif', '2' => 'jpg', '3' => 'png');
        $extension = array_key_exists($info[2], $extensions) ? $extensions[$info[2]] : '';

        return $extension;
    }

    /**
     * Static function to calculate resized image dimensions
     *
     * @param integer $originalWidth
     * @param integer $originalHeight
     * @param integer $width
     * @param integer $height
     * @param integer $type
     *
     * @return string[]|boolean
     */
    public static function rescale($originalWidth, $originalHeight, $width, $height, $type = self::SCALE_INSIDE)
    {
        $aspect = $originalHeight / $originalWidth;

        if ($type == self::SCALE_OUTSIDE)
        {
            $new_aspect = $height / $width;
            $width = ($aspect < $new_aspect ? 9999999 : $width);
            $height = ($aspect > $new_aspect ? 9999999 : $height);
        }

        // don't scale up
        if ($width >= $originalWidth && $height >= $originalHeight)
        {
            return false;
        }

        $new_aspect = $height / $width;

        if ($aspect < $new_aspect)
        {
            $width = (int) min($width, $originalWidth);
            $height = (int) round($width * $aspect);
        }
        else
        {
            $height = (int) min($height, $originalHeight);
            $width = (int) round($height / $aspect);
        }

        return array(self::DIMENSION_WIDTH => $width, self::DIMENSION_HEIGHT => $height);
    }

    /**
     * Resize an image to an exact set of dimensions, ignoring aspect ratio.
     *
     * @param integer $width The width of the image after resizing
     * @param integer $height The height of the image after resizing
     *
     * @return boolean True if successfull, false if not
     */
    abstract public function resize($width, $height);

    /**
     * Resize an image maintaining the original aspect-ratio.
     * Images which are
     * allready smaller than the given width and height won't be scaled.
     *
     * @param integer $width
     * @param integer $height
     * @param integer $type
     *
     * @return boolean
     */
    public function scale($width, $height, $type = self::SCALE_INSIDE)
    {
        $new_dimensions = $this->rescale($this->width, $this->height, $width, $height, $type);

        return $this->resize($new_dimensions[self::DIMENSION_WIDTH], $new_dimensions[self::DIMENSION_HEIGHT]);
    }

    /**
     * Write the resulting image (after some manipulations to a file)
     *
     * @param string $sourceFile Full path of the file to which the image should be written. If null, the original image
     *        will be overwritten.
     *
     * @return boolean
     */
    abstract public function write_to_file($sourceFile = null);
}
