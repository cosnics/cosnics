<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathChildRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * Service class to manage LearningPathChild classes
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathChildService
{
    /**
     * @var LearningPathChildRepository
     */
    protected $learningPathChildRepository;

    /**
     * @var LearningPathChildValidator
     */
    protected $learningPathChildValidator;

    /**
     * LearningPathChildService constructor.
     *
     * @param LearningPathChildRepository $learningPathChildRepository
     * @param LearningPathChildValidator $learningPathChildValidator
     */
    public function __construct(
        LearningPathChildRepository $learningPathChildRepository,
        LearningPathChildValidator $learningPathChildValidator
    )
    {
        $this->learningPathChildRepository = $learningPathChildRepository;
        $this->learningPathChildValidator = $learningPathChildValidator;
    }

    /**
     * Adds a given content object to a learning path. Validates the content object to make sure that the
     * system does not create a cycle. Uses the LearningPathTree for calculations.
     *
     * @param LearningPathTreeNode $parentLearningPathTreeNode
     * @param ContentObject $childContentObject
     *
     * @return LearningPathChild
     */
    public function addContentObjectToLearningPath(
        LearningPathTreeNode $parentLearningPathTreeNode, ContentObject $childContentObject
    )
    {
        if (!$this->learningPathChildValidator->canContentObjectBeAdded(
            $parentLearningPathTreeNode, $childContentObject
        )
        )
        {
            throw new \RuntimeException(
                'You are not allowed to add the given content object to the parent learning path'
            );
        }

        $learningPathChild = new LearningPathChild();
        $learningPathChild->setParentLearningPathId((int) $parentLearningPathTreeNode->getContentObject()->getId());
        $learningPathChild->setContentObjectId((int) $childContentObject->getId());

        if (!$this->learningPathChildRepository->create($learningPathChild))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not create a LearningPathChildObject for parent %s and child %s',
                    $learningPathChild->getParentLearningPathId(), $learningPathChild->getContentObjectId()
                )
            );
        }

        return $learningPathChild;
    }
}