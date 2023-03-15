<?php
namespace Chamilo\Core\Repository\Common\Renderer;

abstract class ContentObjectRenderer
{
    public const TYPE_GALLERY = 'GalleryTable';
    public const TYPE_SLIDESHOW = 'Slideshow';
    public const TYPE_TABLE = 'Table';

    /**
     * @return string[]
     */
    public static function getAvailableRendererTypes(): array
    {
        return [
            self::TYPE_TABLE,
            self::TYPE_GALLERY,
            self::TYPE_SLIDESHOW
        ];
    }
}
