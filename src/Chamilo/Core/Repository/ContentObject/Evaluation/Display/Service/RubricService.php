<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository\EntityRepository;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository\RubricRepository;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class RubricService
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository\EntityRepository
     */
    protected $entityRepository;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository\RubricRepository
     */
    protected $rubricRepository;

    /**
     * @var \Chamilo\Core\Repository\Workspace\Service\ContentObjectService
     */
    protected $contentObjectService;

    public function __construct(EntityRepository $entityRepository, RubricRepository $rubricRepository, ContentObjectService $contentObjectService)
    {
        $this->entityRepository = $entityRepository;
        $this->rubricRepository = $rubricRepository;
        $this->contentObjectService = $contentObjectService;
    }

    /**
     * @param Evaluation $evaluation
     *
     * @return bool
     */
    public function evaluationHasRubric(Evaluation $evaluation): bool
    {
        try
        {
            $rubric = $this->contentObjectService->findById($evaluation->getRubricId());
            return $rubric instanceof Rubric;
        }
        catch (\TypeError | \Exception $e)
        {
            return false;
        }
    }

    public function isSelfEvaluationAllowed(Evaluation $evaluation) : bool
    {
        // todo
        return true;
    }
}
