<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathStepContextRepository;

/**
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LearningPathStepContextService
{
    /**
     * @var LearningPathStepContextRepository
     */
    protected $learningPathStepContextRepository;

    /**
     * LearningPathStepContextService constructor.
     *
     * @param LearningPathStepContextRepository $learningPathStepContextRepository
     */
    public function __construct(LearningPathStepContextRepository $learningPathStepContextRepository)
    {
        $this->learningPathStepContextRepository = $learningPathStepContextRepository;
    }

}