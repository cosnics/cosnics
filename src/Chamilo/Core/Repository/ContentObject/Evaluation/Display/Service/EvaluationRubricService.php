<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service;

use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EvaluationRubricService
{
    /**
     * @var \Chamilo\Core\Repository\Workspace\Service\ContentObjectService
     */
    protected $contentObjectService;

    /**
     * EvaluationRubricService constructor.
     * @param ContentObjectService $contentObjectService
     */
    public function __construct(ContentObjectService $contentObjectService)
    {
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

    /**
     * @param Evaluation $evaluation
     * @return Rubric|null
     */
    public function getRubricForEvaluation(Evaluation $evaluation)
    {
        try
        {
            $rubric = $this->contentObjectService->findById($evaluation->getRubricId());
            if ($rubric instanceof Rubric)
            {
                return $rubric;
            }
            return null;
        }
        catch (\TypeError | \Exception $e)
        {
            return null;
        }
    }

    public function isSelfEvaluationAllowed(Evaluation $evaluation) : bool
    {
        // todo
        return true;
    }
}
