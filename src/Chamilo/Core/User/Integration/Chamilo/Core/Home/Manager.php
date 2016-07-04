<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Home;

class Manager
{

    public function getBlockTypes()
    {
        return array('Chamilo\Core\User\Integration\Chamilo\Core\Home\Type\Login');
    }
}