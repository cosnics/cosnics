<?php
namespace Chamilo\Application\Calendar\Package;

use Chamilo\Libraries\Architecture\Application\WebApplicationRemover;

class Remover extends WebApplicationRemover
{

    public function get_additional_packages()
    {
        return array('Chamilo\Application\Calendar\Extension\Personal');
    }
}
