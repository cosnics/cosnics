<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Query\FilterParametersTranslator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Repository
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class RubricRepository
{
    /**
     * @var DataClassRepository
     */
    private $dataClassRepository;

    /**
     * @var FilterParametersTranslator
     */
    private $filterParametersTranslator;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     * @param \Chamilo\Libraries\Storage\Query\FilterParametersTranslator $filterParametersTranslator
     */
    public function __construct(
        DataClassRepository $dataClassRepository, FilterParametersTranslator $filterParametersTranslator
    )
    {
        $this->dataClassRepository = $dataClassRepository;
        $this->filterParametersTranslator = $filterParametersTranslator;
    }

    /**
     * @param Evaluation $evaluation
     *
     * @return Rubric|object
     */
    public function getRubricForEvaluation(Evaluation $evaluation)
    {
        return null;
        //return $this->findOneBy(['assignmentId' => $evaluation->getId()]);
    }

}