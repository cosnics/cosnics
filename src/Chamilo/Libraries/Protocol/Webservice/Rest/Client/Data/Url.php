<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client\Data;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestData;
use Exception;

class Url extends RestData
{

    public function prepare()
    {
        if (is_array($this->get_data()))
        {
            return http_build_query($this->get_data());
        }
        else
        {
            throw new Exception(Translation :: get('NotAnArray'));
        }
    }
}
