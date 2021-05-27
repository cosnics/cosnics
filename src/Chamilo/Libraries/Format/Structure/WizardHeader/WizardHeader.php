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

    /**
     * The titles of steps in the wizard
     *
     * @var string[]
     */
    private $stepTitles;

    /**
     * The index of the selected step
     *
     * @var integer
     */
    private $selectedStepIndex;

    /**
     *
     * @param string[] $stepTitles
     * @param integer $selectedStepIndex
     */
    public function __construct($stepTitles = [], $selectedStepIndex = 0)
    {
        $this->stepTitles = $stepTitles;
        $this->selectedStepIndex;
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

    /**
     *
     * @return integer
     */
    public function getSelectedStepIndex()
    {
        return $this->selectedStepIndex;
    }

    /**
     *
     * @param integer $selectedStepIndex
     */
    public function setSelectedStepIndex($selectedStepIndex)
    {
        $this->selectedStepIndex = $selectedStepIndex;
    }

    /**
     *
     * @return string[]
     */
    public function getStepTitles()
    {
        return $this->stepTitles;
    }

    /**
     *
     * @param string[] $stepTitles
     */
    public function setStepTitles($stepTitles)
    {
        $this->stepTitles = $stepTitles;
    }

    /**
     * Checks whether or not the given step index is currently selected
     *
     * @param integer $stepIndex
     *
     * @return boolean
     */
    public function isStepSelected($stepIndex)
    {
        return $stepIndex == $this->selectedStepIndex;
    }
}