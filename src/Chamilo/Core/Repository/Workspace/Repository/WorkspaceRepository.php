<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceRepository
{

    /**
     * @param integer $identifier
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    public function findWorkspaceByIdentifier($identifier)
    {
        return DataManager :: retrieve_by_id(Workspace :: class_name(), $identifier);
    }
}