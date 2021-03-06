<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client\Data;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestData;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Protocol\Webservice\Rest\Client\Data
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Form extends RestData
{

    /**
     *
     * @return string
     * @throws \Exception
     */
    public function prepare()
    {
        if (is_array($this->getData()))
        {
            return $this->getData();
        }
        else
        {
            throw new Exception(Translation::get('NotAnArray'));
        }
    }
}
