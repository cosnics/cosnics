<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class InvalidRubricDataException extends RubricStructureException
{
    /**
     * InvalidRubricDataException constructor.
     *
     * @param string $type
     * @param int $id
     * @param RubricData $expectedRubricData
     * @param RubricData $givenRubricData
     */
    public function __construct(string $type, int $id, RubricData $expectedRubricData, RubricData $givenRubricData)
    {
        parent::__construct(
            sprintf(
               'The %s with id %s was expected to have rubric data %s instead rubric data %s was given',
                $type, $id, $expectedRubricData->getId(), $givenRubricData->getId()
            )
        );
    }
}
