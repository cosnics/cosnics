<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client\Result;

use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestResult;
use DOMDocument;
use SimpleXMLElement;

/**
 *
 * @package Chamilo\Libraries\Protocol\Webservice\Rest\Client\Result
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Xml extends RestResult
{
    const PARSE_DOM = 'DOM';
    const PARSE_SIMPLEXML = 'simplexml';

    /**
     *
     * @param string $parse
     * @return \DOMDocument|\SimpleXMLElement
     */
    public function get_response_content($parse = self::PARSE_DOM)
    {
        if ($parse == self::PARSE_DOM)
        {
            $document = new DOMDocument();
            $document->loadXML(parent::get_response_content(false));
            return $document;
        }
        elseif ($parse == self::PARSE_SIMPLEXML)
        {
            return new SimpleXMLElement(parent::get_response_content());
        }
        else
        {
            parent::get_response_content(false);
        }
    }
}
