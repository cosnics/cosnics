<?php
namespace Chamilo\Libraries\Filesystem\Service\ImageManipulation;

use Exception;
use GdImage;

/**
 * @package Chamilo\Libraries\Filesystem\Service\ImageManipulation
 */
class GdImageManipulation extends ImageManipulation
{
    private GdImage|null|false $gdImage = null;

    /**
     * @throws \Exception
     */
    public function __construct(string $sourceFile)
    {
        parent::__construct($sourceFile);
        $this->loadGdImage();
    }

    /**
     * @throws \Exception
     */
    public function crop(int $width, int $height, int $offsetX = self::CROP_CENTER, int $offsetY = self::CROP_CENTER
    ): void
    {
        if (!function_exists('imagecopy')) {
            throw new \Exception('imagecopy function is not available');
        }

        if ($offsetX == ImageManipulation::CROP_CENTER) {
            $offsetX = ($this->width - $width) / 2;
        }

        if ($offsetY == ImageManipulation::CROP_CENTER) {
            $offsetY = ($this->height - $height) / 2;
        }

        $result = imagecreatetruecolor($width, $height);

        if (imagecopy($result, $this->gdImage, 0, 0, $offsetX, $offsetY, $width, $height)) {
            $this->gdImage = $result;
            $this->width = $width;
            $this->height = $height;
        }
        else{
            throw new \Exception('imagecopy failed');
        }
    }

    /**
     * @throws \Exception
     */
    private function loadGdImage(): void
    {
        $extension = $this->getImageExtension();
        $extension = str_replace('jpg', 'jpeg', $extension);
        $createFunction = 'imagecreatefrom' . $extension;

        if (!function_exists($createFunction)) {
            throw new Exception($createFunction . ' not found');
        }

        $this->gdImage = $createFunction($this->sourceFile);
    }

    /**
     * @throws \Exception
     */
    public function resize(int $width, int $height): void
    {
        if (!function_exists('imagecopyresampled')) {
            throw new Exception('imagecopyresampled not found');
        }

        $result = imagecreatetruecolor($width, $height);

        if (imagecopyresampled($result, $this->gdImage, 0, 0, 0, 0, $width, $height, $this->width, $this->height)) {
            $this->gdImage = $result;
            $this->width = $width;
            $this->height = $height;
        }
        else{
            throw new Exception('imagecopyresampled failed');
        }
    }

    /**
     * @throws \Exception
     */
    public function writeToFile(?string $sourceFile = null): void
    {
        if (is_null($sourceFile)) {
            $sourceFile = $this->sourceFile;
        }

        $extension = $this->getImageExtension();
        $extension = str_replace('jpg', 'jpeg', $extension);
        $createFunction = 'image' . $extension;

        if (!function_exists($createFunction)) {
            throw new Exception($createFunction . ' not found');
        }

        $createFunction($this->gdImage, $sourceFile);
    }
}
