<?php
namespace Chamilo\Libraries\Format\Structure\WizardHeader;

/**
 * Describes a header for a wizard
 *
 * @package Chamilo\Libraries\Format\Structure\WizardHeader
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WizardHeader
{

    private int $selectedStepIndex;

    /**
     * @var string[]
     */
    private array $stepTitles;

    /**
     * @param string[] $stepTitles
     */
    public function __construct(array $stepTitles = [], int $selectedStepIndex = 0)
    {
        $this->stepTitles = $stepTitles;
        $this->selectedStepIndex = $selectedStepIndex;
    }

    public function addStepTitle(string $stepTitle)
    {
        $this->stepTitles[] = $stepTitle;
    }

    public function getSelectedStepIndex(): int
    {
        return $this->selectedStepIndex;
    }

    public function setSelectedStepIndex(int $selectedStepIndex)
    {
        $this->selectedStepIndex = $selectedStepIndex;
    }

    /**
     * @return string[]
     */
    public function getStepTitles(): array
    {
        return $this->stepTitles;
    }

    /**
     * @param string[] $stepTitles
     */
    public function setStepTitles(array $stepTitles)
    {
        $this->stepTitles = $stepTitles;
    }

    public function isStepSelected(int $stepIndex): bool
    {
        return $stepIndex == $this->selectedStepIndex;
    }
}