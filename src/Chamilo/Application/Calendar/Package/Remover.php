<?php
namespace Chamilo\Application\Calendar\Package;

use Chamilo\Libraries\Architecture\Application\WebApplicationRemover;

class Remover extends WebApplicationRemover
{
    public const CONTEXT = Installer::CONTEXT;

    public function get_additional_packages()
    {
        return ['Chamilo\Application\Calendar\Extension\Personal'];
    }
}
