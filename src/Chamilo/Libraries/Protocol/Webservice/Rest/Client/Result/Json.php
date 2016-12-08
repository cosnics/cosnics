<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client\Result;

use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestResult;

class Json extends RestResult
{

    /**
     *
     * @return stdClass
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
