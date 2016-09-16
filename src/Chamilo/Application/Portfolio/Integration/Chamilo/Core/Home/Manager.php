<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Home;

class Manager
{

    public function getBlockTypes()
    {
        return array('Chamilo\Application\Portfolio\Integration\Chamilo\Core\Home\Type\FavouriteUsers');
    }
}