<?php
namespace Chamilo\Application\Calendar\Integration\Chamilo\Core\Home;

class Manager
{

    public function getBlockTypes()
    {
        return array(
            'Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type\Day',
            'Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Type\Month');
    }
}