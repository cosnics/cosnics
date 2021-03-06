<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client\Result;

use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestResult;

/**
 *
 * @package Chamilo\Libraries\Protocol\Webservice\Rest\Client\Result
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Json extends RestResult
{

    /**
     *
     * @param boolean $parse
     * @return \stdClass
     */
    public function get_response_content($parse = true)
    {
        if ($parse)
        {
            return json_decode(parent::get_response_content(false));
        }
        else
        {
            parent::get_response_content(false);
        }
    }
}
