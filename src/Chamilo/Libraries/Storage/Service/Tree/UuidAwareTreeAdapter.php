<?php
namespace Chamilo\Libraries\Storage\Service\Tree;

use Gedmo\Mapping\Event\Adapter\ORM as BaseAdapterORM;
use Gedmo\Tree\Mapping\Event\TreeAdapter;

/**
 * @package Chamilo\Libraries\Storage\Service\Tree
 * @author Dany Maillard <https://github.com/maidmaid>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UuidAwareTreeAdapter extends BaseAdapterORM implements TreeAdapter
{
    public function getObjectManager(): UuidAwareEntityManager
    {
        return new UuidAwareEntityManager(parent::getObjectManager());
    }
}