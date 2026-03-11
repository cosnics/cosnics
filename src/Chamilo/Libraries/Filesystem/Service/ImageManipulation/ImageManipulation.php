<?php
namespace Chamilo\Libraries\Filesystem\Service\ImageManipulation;

/**
 * @package Chamilo\Libraries\Filesystem\Service\ImageManipulation
 */
abstract class ImageManipulation
{
    /**
     * When cropping an image, use this offset value to get the exacte centre of the image
     */
    public const int CROP_CENTER = - 1;
    public const int DIMENSION_HEIGHT = 1;
    public const int DIMENSION_WIDTH = 0;
    /**
     * Final dimensions will be less than or equal to the entered width and height. Useful for ensuring a maximum
     * height and/or width.
     */
    public const int SCALE_INSIDE = 0;
    /**
     * Final dimensions will be greater than or equal to the entered width and height. Ideal for cropping the result to
     * a square.
     */
    public const int SCALE_OUTSIDE = 1;

    protected int $height;

    protected string $sourceFile;

    protected int $width;

    public function __construct(string $sourceFile)
    {
        $this->sourceFile = $sourceFile;
        $dimension = getimagesize($sourceFile);
        $this->width = $dimension[self::DIMENSION_WIDTH];
        $this->height = $dimension[self::DIMENSION_HEIGHT];
    }

    public function createThumbnail(int $width, ?int $height = null): bool
    {
        if (is_null($height)) {
            $height = $width;
        }
        if ($this->scale($width, $height, self::SCALE_OUTSIDE)) {
            return $this->crop($width, $height);
        }

        return false;
    }

    abstract public function crop(
        int $width, int $height, int $offsetX = self::CROP_CENTER, int $offsetY = self::CROP_CENTER
    ): bool;

    /**
     * @throws \Exception
     */
    public static function factory(string $sourceFile): GdImageManipulation
    {
        return new GdImageManipulation($sourceFile);
    }

    protected function getImageExtension(): string
    {
        $info = getimagesize($this->sourceFile);
        $extensions = ['1' => 'gif', '2' => 'jpg', '3' => 'png'];
        $extension = array_key_exists($info[2], $extensions) ? $extensions[$info[2]] : null;

        return (is_null($extension) ? 'jpeg' : $extension);
    }

    /**
     * @return int[]|bool
     */
    public static function rescale(
        int $originalWidth, int $originalHeight, int $width, int $height, int $type = self::SCALE_INSIDE
    ): bool|array
    {
        $aspect = $originalHeight / $originalWidth;

        if ($type == self::SCALE_OUTSIDE) {
            $newAspect = $height / $width;
            $width = ($aspect < $newAspect ? 9999999 : $width);
            $height = ($aspect > $newAspect ? 9999999 : $height);
        }

        // don't scale up
        if ($width >= $originalWidth && $height >= $originalHeight) {
            return false;
        }

        $newAspect = $height / $width;

        if ($aspect < $newAspect) {
            $width = (int) min($width, $originalWidth);
            $height = (int) round($width * $aspect);
        }
        else {
            $height = (int) min($height, $originalHeight);
            $width = (int) round($height / $aspect);
        }

        return [self::DIMENSION_WIDTH => $width, self::DIMENSION_HEIGHT => $height];
    }

    abstract public function resize(int $width, int $height): bool;

    public function scale(int $width, int $height, int $type = self::SCALE_INSIDE): bool
    {
        $newDimensions = static::rescale($this->width, $this->height, $width, $height, $type);

        return $this->resize($newDimensions[self::DIMENSION_WIDTH], $newDimensions[self::DIMENSION_HEIGHT]);
    }

    abstract public function writeToFile(?string $sourceFile = null): bool;
}
