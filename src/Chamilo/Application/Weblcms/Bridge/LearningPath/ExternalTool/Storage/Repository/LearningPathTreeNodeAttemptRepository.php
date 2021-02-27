<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool\Storage\Repository;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;

/**
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathTreeNodeAttemptRepository
{
    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * ExternalToolResultRepository constructor.
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(
        \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
    )
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param int $id
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findTreeNodeAttemptById(int $id)
    {
        return $this->dataClassRepository->retrieveById(LearningPathTreeNodeAttempt::class, $id);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt $treeNodeAttempt
     *
     * @return bool
     */
    public function updateTreeNodeAttempt(LearningPathTreeNodeAttempt $treeNodeAttempt)
    {
        return $this->dataClassRepository->update($treeNodeAttempt);
    }

}