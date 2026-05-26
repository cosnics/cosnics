<?php
namespace Chamilo\Libraries\Storage\Factory;

use Chamilo\Libraries\Storage\Service\Tree\UuidAwareTreeListener;
use Gedmo\Mapping\Driver\AttributeReader;
use Gedmo\Tree\TreeListener;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * @package Chamilo\Libraries\Storage\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TreeListenerFactory
{
    public function getTreeListener(): TreeListener
    {
        $treeListener = new UuidAwareTreeListener();
        $treeListener->setAnnotationReader(new AttributeReader());
        $treeListener->setCacheItemPool(new ArrayAdapter());

        return $treeListener;
    }
}