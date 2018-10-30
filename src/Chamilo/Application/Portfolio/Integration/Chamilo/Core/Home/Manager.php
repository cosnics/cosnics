<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Home;

/**
 *
 * @package Chamilo\Application\Portfolio\Integration\Chamilo\Core\Home
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Manager
{

    public function getBlockTypes()
    {
        return array('Chamilo\Application\Portfolio\Integration\Chamilo\Core\Home\Type\FavouriteUsers');
    }
}