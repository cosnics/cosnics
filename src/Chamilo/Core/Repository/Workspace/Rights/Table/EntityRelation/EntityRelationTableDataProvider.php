<?php
namespace Chamilo\Core\Repository\Workspace\Rights\Table\EntityRelation;

use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityRelationTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $orderProperty = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderProperty);
        return DataManager::retrieves(WorkspaceEntityRelation::class, $parameters);
    }

    public function count_data($condition)
    {
        $parameters = new DataClassCountParameters($condition);
        return DataManager::count(WorkspaceEntityRelation::class, $parameters);
    }
}