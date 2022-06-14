<?php
namespace Chamilo\Libraries\Format\Structure\WizardHeader;

/**
 * Describes a header for a wizard
 *
 * @package Chamilo\Libraries\Format\Structure\WizardHeader
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NumericWizardHeaderRenderer extends WizardHeaderRenderer
{

    /**
     * @return string[]
     */
    protected function getStepTitles(): array
    {
        $stepTitles = $this->wizardHeader->getStepTitles();
        foreach ($stepTitles as $index => $stepTitle)
        {
            $stepTitles[$index] = ($index + 1) . '. ' . $stepTitle;
        }

        return $stepTitles;
    }
}