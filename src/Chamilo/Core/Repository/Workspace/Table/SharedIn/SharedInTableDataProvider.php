<?php
namespace Chamilo\Core\Repository\Workspace\Table\SharedIn;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Workspace\Table\Share\ShareTableDataProvider;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SharedInTableDataProvider extends ShareTableDataProvider
{
    /**
     *
     * @see \Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableDataProvider::retrieve_data()
     */
    public function retrieve_data($condition, $offset, $limit, $orderProperty = null)
    {
        return $this->getWorkspaceService()->getWorkspacesForContentObject(
            $this->get_component()->get_parameter(Manager::PARAM_CONTENT_OBJECT_ID),
            $limit,
            $offset,
            $orderProperty
        );
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableDataProvider::count_data()
     */
    public function count_data($condition)
    {
        return $this->getWorkspaceService()->countWorkspacesForContentObject(
            $this->get_component()->get_parameter(Manager::PARAM_CONTENT_OBJECT_ID)
        );
    }
}