<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Service;

use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use JMS\Serializer\Serializer;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class VerificationIconService
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Presence $presence
     *
     * @return string
     */
    public function renderVerificationIconForPresence(Presence $presence): string
    {
        $verifyIcon = $presence->getVerifyIcon();

        if (empty($verifyIcon))
        {
            return '';
        }

        try
        {
            $data = $this->serializer->deserialize($verifyIcon, 'array', 'json');
        }
        catch (\Exception $ex)
        {
            return '';
        }

        $outputCode = $data['result'];

        if (empty($outputCode))
        {
            return '';
        }

        return '<svg width="120" height="120" xmlns="http://www.w3.org/2000/svg">' .
                 '<rect x="0" y="0" width="120" height="120" fill="white" stroke="currentColor" stroke-width="1"></rect>' .
                 '<g transform="translate(60, 60)">' .
                    $this->parseAndRenderShape(substr($outputCode, 2)) .
                 '</g>' .
                '</svg>';
    }

    protected function parseAndRenderShape(string $str): string
    {
        $shape = $this->parseShape($str[0]);
        if (empty($shape))
        {
            return '';
        }
        $shapeDetails = $this->parseShapeAttributes(substr($str, 0, 3));
        $fillStroke = $this->parseFillStrokeAttributes(substr($str, 3));
        return '<' . $shape . ' ' . $shapeDetails . ' ' . $fillStroke . '>' . '</' . $shape . '>';
    }

    /**
     * @param string $str
     *
     * @return string
     */
    protected function parseShape(string $str): string
    {
        switch ($str)
        {
            case 'r':
                return 'rect';
            case 'c':
                return 'circle';
            case 'p':
                return 'polygon';
            default:
                return '';
        }
    }

    /**
     * @param string $str
     *
     * @return string
     */
    protected function parseShapeAttributes(string $str): string
    {
        switch ($str)
        {
            case 'r00':
                return 'x="-50" y="-50" width="100" height="100"';
            case 'c00':
                return 'cx="0" cy="0" r="50"';
            case 'p00':
                return 'points="50,-50 -50,50 -50,-50"';
            case 'p01':
                return 'points="-50,-50 50,50 -50,50"';
            case 'p10':
                return 'points="50,-50 50,50 -50,-50"';
            case 'p11':
                return 'points="50,-50 50,50 -50,50"';
            default:
                return '';
        }
    }

    /**
     * @param string $str
     *
     * @return string
     */
    protected function parseFillStrokeAttributes(string $str): string
    {
        $colors = ['#000000', '#ff0000', '#ffed00', '#306eff', '#ff69b4', '#228b22', '#fbb117', '#00ff00', '#d462ff'];
        $stroked = substr($str, 0, 3) == 'fxx';
        if ($stroked)
        {
            $index = (int) substr($str, 4, 2);
            return 'fill="white" stroke="' . $colors[$index] . '" stroke-width="5"';
        }
        $index = (int) substr($str, 1, 2);
        return 'fill="' . $colors[$index] . '"';
    }
}