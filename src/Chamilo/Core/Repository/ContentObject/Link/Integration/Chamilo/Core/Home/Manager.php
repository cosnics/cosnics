<?php
namespace Chamilo\Core\Repository\ContentObject\Link\Integration\Chamilo\Core\Home;

class Manager
{

    public function getBlockTypes()
    {
        return array('Chamilo\Core\Repository\ContentObject\Link\Integration\Chamilo\Core\Home\Type\Linker');
    }
}