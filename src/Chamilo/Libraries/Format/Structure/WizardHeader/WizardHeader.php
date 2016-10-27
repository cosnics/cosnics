<?php

namespace Chamilo\Libraries\Format\Structure\WizardHeader;

/**
 * Describes a header for a wizard
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WizardHeader
{
    /**
     * The titles of steps in the wizard
     *
     * @var string[]
     */
    private $stepTitles;

    /**
     * The index of the selected step
     *
     * @var int
     */
    private $selectedStepIndex;

    public function __construct($stepTitles = array(), $selectedStepIndex = 0)
    {
        $this->stepTitles = $stepTitles;
        $this->selectedStepIndex;
    }

    /**
     * @return \string[]
     */
    public function getStepTitles()
    {
        return $this->stepTitles;
    }

    /**
     * @param \string[] $stepTitles
     */
    public function setStepTitles($stepTitles)
    {
        $this->stepTitles = $stepTitles;
    }

    /**
     * @return int
     */
    public function getSelectedStepIndex()
    {
        return $this->selectedStepIndex;
    }

    /**
     * @param int $selectedStepIndex
     */
    public function setSelectedStepIndex($selectedStepIndex)
    {
        $this->selectedStepIndex = $selectedStepIndex;
    }

    /**
     * Checks whether or not the given step index is currently selected
     *
     * @param int $stepIndex
     *
     * @return bool
     */
    public function isStepSelected($stepIndex)
    {
        return $stepIndex == $this->selectedStepIndex;
    }

    /**
     * Adds a step title
     *
     * @param string $stepTitle
     */
    public function addStepTitle($stepTitle)
    {
        $this->stepTitles[] = $stepTitle;
    }
}