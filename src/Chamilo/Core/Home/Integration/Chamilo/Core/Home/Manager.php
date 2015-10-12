<?php
namespace Chamilo\Core\Home\Integration\Chamilo\Core\Home;

class Manager
{

    public function getBlockTypes()
    {
        return array(
            'Chamilo\Core\Home\Integration\Chamilo\Core\Home\Type\Banner',
            'Chamilo\Core\Home\Integration\Chamilo\Core\Home\Type\External',
            'Chamilo\Core\Home\Integration\Chamilo\Core\Home\Type\Publish',
            'Chamilo\Core\Home\Integration\Chamilo\Core\Home\Type\StaticContent');
    }
}