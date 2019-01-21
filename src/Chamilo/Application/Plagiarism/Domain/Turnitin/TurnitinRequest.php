<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TurnitinRequest extends Request
{
    /**
     * @param string $method HTTP method
     * @param string|UriInterface $uri URI
     * @param array $headers Request headers
     * @param string|null|resource|StreamInterface $body Request body
     * @param string $secretKey
     */
    public function __construct(
        $method, $uri, $secretKey = '', $body = null, array $headers = []
    )
    {
        $headers[] = 'X-Turnitin-Integration-Name: Chamilo';
        $headers[] = 'X-Turnitin-Integration-Version: 1.0';
        $headers[] = 'Authorization: Bearer ' . $secretKey;

        parent::__construct($method, $uri, $headers, $body);
    }
}