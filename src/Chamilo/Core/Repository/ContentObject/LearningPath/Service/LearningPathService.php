<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * Service to manage learning paths
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathService
{
    /**
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * LearningPathService constructor.
     *
     * @param ContentObjectRepository $contentObjectRepository
     */
    public function __construct(ContentObjectRepository $contentObjectRepository)
    {
        $this->contentObjectRepository = $contentObjectRepository;
    }

    /**
     * Returns a list of learning paths
     *
     * @return LearningPath[]
     */
    public function getLearningPaths()
    {
        /** @var LearningPath[] $learningPaths */
        $learningPaths = $this->contentObjectRepository->findAll(LearningPath::class_name(), new DataClassRetrievesParameters())
            ->as_array();

        return $learningPaths;
    }
}