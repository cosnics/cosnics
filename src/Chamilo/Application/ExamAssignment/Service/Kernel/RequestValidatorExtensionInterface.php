<?php

namespace Chamilo\Application\ExamAssignment\Service\Kernel;

/**
 * Interface RequestValidatorExtensionInterface
 * @package Chamilo\Application\ExamAssignment\Service\Kernel
 */
interface RequestValidatorExtensionInterface
{
    /**
     * @param string $context
     * @param string|null $action
     *
     * @return mixed
     */
    public function isActionAllowed(string $context, string $action = null);
}
