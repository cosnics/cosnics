<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Service;

use Endroid\QrCode\QrCode;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class QRService
{
    public function getQRForURL(string $url, int $size, bool $base64 = true): string
    {
        $qrCode = new QrCode($url);
        $qrCode->setWriterByName('png');
        $qrCode->setSize($size);
        if ($base64)
        {
            return 'data:image/png;base64,' . base64_encode($qrCode->writeString());
        }
        return $qrCode->writeString();
    }
}