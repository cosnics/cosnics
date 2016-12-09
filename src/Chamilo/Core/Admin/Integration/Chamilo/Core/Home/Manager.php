<?php
namespace Chamilo\Core\Admin\Integration\Chamilo\Core\Home;

class Manager
{

    public function getBlockTypes()
    {
        return array('Chamilo\Core\Admin\Integration\Chamilo\Core\Home\Type\PortalHome');
    }
}