<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EntityTable extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
{
    const TABLE_IDENTIFIER = DataClass::PROPERTY_ID;

    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceInterface
     */
    protected $entityService;

    /**
     * @var \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * EntityTable constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceInterface $entityService
     */
    public function __construct(
        Application $component, AssignmentDataProvider $assignmentDataProvider,
        ContentObjectPublication $contentObjectPublication,
        EntityServiceInterface $entityService
    )
    {
        parent::__construct($component, $assignmentDataProvider);
        $this->contentObjectPublication = $contentObjectPublication;
        $this->entityService = $entityService;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    public function getContentObjectPublication()
    {
        return $this->contentObjectPublication;
    }


    /**
     * @return \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceInterface
     */
    public function getEntityService()
    {
        return $this->entityService;
    }

}