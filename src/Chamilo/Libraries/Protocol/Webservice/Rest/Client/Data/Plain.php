<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client\Data;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestData;
use Exception;

class Plain extends RestData
{

    public function prepare()
    {
        if (is_string($this->get_data()))
        {
            return $this->get_data();
        }
        else
        {
            throw new Exception(Translation :: get('NotAString'));
        }
    }
}
