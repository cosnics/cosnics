<?php
namespace Chamilo\Libraries\Architecture;

/**
 * @package Chamilo\Libraries\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ActionResult
{
    private string $actionType;

    private string $context;

    private string $entityType;

    private int $failedActions;

    private int $totalActions;

    /**
     * @throws \Exception
     */
    public function __construct(
        int $totalActions, int $failedActions, string $context, string $actionType, string $entityType
    )
    {
        $this->totalActions = $totalActions;
        $this->failedActions = $failedActions;
        $this->context = $context;
        $this->actionType = $actionType;
        $this->entityType = $entityType;
    }

    public function getActionType(): string
    {
        return $this->actionType;
    }

    public function setActionType(string $actionType): void
    {
        $this->actionType = $actionType;
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function setContext(string $context): void
    {
        $this->context = $context;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function setEntityType(string $entityType): void
    {
        $this->entityType = $entityType;
    }

    public function getFailedActions(): int
    {
        return $this->failedActions;
    }

    public function setFailedActions(int $failedActions): void
    {
        $this->failedActions = $failedActions;
    }

    public function getTotalActions(): int
    {
        return $this->totalActions;
    }

    public function setTotalActions(int $totalActions): void
    {
        $this->totalActions = $totalActions;
    }

    public function hasFailed(): bool
    {
        return $this->getFailedActions() > 0;
    }

    public function hasFailedCompletely(): bool
    {
        return $this->hasFailed() && $this->getFailedActions() == $this->getTotalActions();
    }

    public function hasSucceeded(): bool
    {
        return !$this->hasFailed();
    }

    public function isSingleAction(): bool
    {
        return $this->getTotalActions() == 1;
    }
}