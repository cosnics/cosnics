<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Table\Entity;

use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EntityTableDataProvider extends RecordTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        $helper_class = $this->getTable()->get_helper_class_name();

        return $helper_class::count_table_data($condition);
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $helper_class = $this->getTable()->get_helper_class_name();

        return $helper_class::retrieve_table_data($condition, $count, $offset, $orderBy);
    }
}