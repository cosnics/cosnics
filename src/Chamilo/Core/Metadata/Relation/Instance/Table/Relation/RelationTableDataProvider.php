<?php
namespace Chamilo\Core\Metadata\Relation\Instance\Table\Relation;

use Chamilo\Core\Metadata\Relation\Instance\Storage\DataManager;
use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @package Chamilo\Core\Metadata\Relation\Instance\Table\Relation
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RelationTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count(RelationInstance::class, new DataClassCountParameters($condition));
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderBy);

        return DataManager::retrieves(RelationInstance::class, $parameters);
    }
}