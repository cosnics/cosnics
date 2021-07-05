<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service;

use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricResultRepository;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Architecture\ContextIdentifier;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EvaluationRubricService
{
    /**
     * @var ContentObjectService
     */
    protected $contentObjectService;

    /**
     * @var RubricResultRepository
     */
    protected $rubricResultRepository;

    /**
     * EvaluationRubricService constructor.
     * @param ContentObjectService $contentObjectService
     * @param RubricResultRepository $rubricResultRepository
     */
    public function __construct(ContentObjectService $contentObjectService, RubricResultRepository $rubricResultRepository)
    {
        $this->contentObjectService = $contentObjectService;
        $this->rubricResultRepository = $rubricResultRepository;
    }

    /**
     * @param Evaluation $evaluation
     *
     * @return bool
     */
    public function evaluationHasRubric(Evaluation $evaluation) : bool
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
     *
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

    /**
     * @param ContextIdentifier $entryContextIdentifier
     *
     * @return bool
     */
    public function entryHasResults(ContextIdentifier $entryContextIdentifier) : bool
    {
        return $this->rubricResultRepository->countRubricResultsForContextIdentifier($entryContextIdentifier) > 0;
    }
}
