<?php
namespace Chamilo\Libraries\Storage\Service\Tree;

use Doctrine\ORM\Decorator\EntityManagerDecorator;

/**
 * @package Chamilo\Libraries\Storage\Service\Tree
 * @author Dany Maillard <https://github.com/maidmaid>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UuidAwareEntityManager extends EntityManagerDecorator
{
    public function createQueryBuilder(): UuidAwareQueryBuilder
    {
        return new UuidAwareQueryBuilder($this);
    }
}