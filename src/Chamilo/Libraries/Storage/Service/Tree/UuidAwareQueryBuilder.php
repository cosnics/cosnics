<?php
namespace Chamilo\Libraries\Storage\Service\Tree;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Libraries\Storage\Service\Tree
 * @author Dany Maillard <https://github.com/maidmaid>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UuidAwareQueryBuilder extends QueryBuilder
{
    public function setParameter($key, $value, $type = null): static
    {
        if (null === $type) {
            if ($value instanceof Uuid) {
                $type = 'uuid';
            }

            if (method_exists($value, 'getIdentifier')) {
                if ($value->getIdentifier() instanceof Uuid) {
                    $value = $value->getIdentifier();
                    $type = 'uuid';
                }
            }
        }

        return parent::setParameter($key, $value, $type);
    }
}