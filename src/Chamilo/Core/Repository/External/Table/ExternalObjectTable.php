<?php
namespace Chamilo\Core\Repository\External\Table;

use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;

class ExternalObjectTable extends DataClassListTableRenderer
{

    public static function factory($component)
    {
        $class = $component->get_external_repository_browser()->get_external_repository()->get_implementation() .
             '\Table\ExternalObject\ExternalObjectTable';
        return new $class($component);
    }
}
