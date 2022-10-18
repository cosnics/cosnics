<?php
namespace Chamilo\Core\Metadata\Vocabulary\Table\User;

use Chamilo\Core\Metadata\Vocabulary\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Table data provider for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTableDataProvider extends RecordTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count_vocabulary_users($condition);
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return DataManager::retrieve_vocabulary_users($condition, $count, $offset, $orderBy);
    }
}