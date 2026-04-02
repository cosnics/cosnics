<?php
namespace Chamilo\Libraries\Filesystem\Service;

/**
 * @package Chamilo\Libraries\Filesystem\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ImageConverter
{
    /**
     * @param string $filePath
     *
     * @return string
     */
    public function getPictureAsBase64String(string $filePath): string
    {
        $type = exif_imagetype($filePath);
        $mime = image_type_to_mime_type($type);

        $fileResource = fopen($filePath, 'r');
        $imageBinary = fread($fileResource, filesize($filePath));
        $imgString = base64_encode($imageBinary);

        fclose($fileResource);

        return 'data:' . $mime . ';base64,' . $imgString;
    }
}