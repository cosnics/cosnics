<?php
namespace Chamilo\Core\Repository\Workspace\Table\SharedIn;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Table\Share\ShareTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

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
     * @var ContentObjectRelationService
     */
    protected $contentObjectRelationService;

    public function countData(?Condition $condition = null): int
    {
        return $this->getContentObjectRelationService()->countWorkspacesForContentObject(
            $this->getSelectedContentObject()
        );
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService
     */
    protected function getContentObjectRelationService()
    {
        if (!isset($this->contentObjectRelationService))
        {
            $this->contentObjectRelationService =
                new ContentObjectRelationService(new ContentObjectRelationRepository());
        }

        return $this->contentObjectRelationService;
    }

    /**
     *
     * @return ContentObject
     */
    protected function getSelectedContentObject()
    {
        return $this->get_table()->get_component()->getContentObject();
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return $this->getContentObjectRelationService()->getWorkspacesForContentObject(
            $this->getWorkspaceService(), $this->getSelectedContentObject()
        );
    }
}