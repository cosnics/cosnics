<?php

namespace Chamilo\Application\ExamAssignment\Service\Decorator;

/**
 * Interface ExamRendererDecoratorInterface
 * @package Chamilo\Application\ExamAssignment\Service\Decorator
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
interface ExamRendererDecoratorInterface
{
    /**
     * @return string
     */
    public function renderBelowExamList();
}
