<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query;

use Chamilo\Libraries\Protocol\Security\Architecture\Interface\HashableInterface;
use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;

/**
 * Describes the group by functionality of a query.
 * Uses ConditionVariable to define the group_by's
 *
 * @package Chamilo\Libraries\Storage\Query
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 *
 * @psalm-template TKey of array-key
 * @template-implements Collection<TKey,\Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\ConditionVariable>
 * @template-implements Selectable<TKey,\Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\ConditionVariable>
 * @psalm-consistent-constructor
 */
class GroupBy extends ArrayCollection implements HashableInterface
{
    use HashableTrait;

    /**
     * @return string[]
     */
    public function getHashParts(): array
    {
        $hashes = [];

        foreach ($this as $conditionVariable)
        {
            $hashes[] = $conditionVariable->getHashParts();
        }

        sort($hashes);

        return $hashes;
    }
}
