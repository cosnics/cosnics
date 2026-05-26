<?php
namespace Chamilo\Libraries\Storage\Service\Tree;

use Doctrine\Common\EventArgs;
use Gedmo\Tree\TreeListener;

/**
 * @package Chamilo\Libraries\Storage\Service\Tree
 * @author Dany Maillard <https://github.com/maidmaid>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UuidAwareTreeListener extends TreeListener
{
    protected function getEventAdapter(EventArgs $args): UuidAwareTreeAdapter
    {
        $adapter = new UuidAwareTreeAdapter();
        $adapter->setEventArgs($args);

        return $adapter;
    }
}