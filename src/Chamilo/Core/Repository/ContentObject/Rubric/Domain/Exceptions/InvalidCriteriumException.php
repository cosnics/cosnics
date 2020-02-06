<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Choice;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class InvalidCriteriumException extends RubricStructureException
{
    /**
     * InvalidCriteriumException constructor.
     *
     * @param Choice $choice
     * @param CriteriumNode $expectedNode
     */
    public function __construct(Choice $choice, CriteriumNode $expectedNode)
    {
        parent::__construct(
            sprintf(
               'The choice with id %s has an invalid criterium. [Expected] %s. [Given] %s',
                $choice->getId(), $expectedNode->getId(),
               $choice->getCriterium() instanceof CriteriumNode ? $choice->getCriterium()->getId() : 'no criterium'
            )
        );
    }
}
