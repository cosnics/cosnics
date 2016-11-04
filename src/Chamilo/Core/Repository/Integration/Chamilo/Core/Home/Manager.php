<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Home;

class Manager
{

    public function getBlockTypes()
    {
        return array('Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Type\Displayer');
    }
}