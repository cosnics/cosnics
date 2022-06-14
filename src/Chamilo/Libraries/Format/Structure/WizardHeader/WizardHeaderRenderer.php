<?php
namespace Chamilo\Libraries\Format\Structure\WizardHeader;

/**
 * Describes a header for a wizard
 *
 * @package Chamilo\Libraries\Format\Structure\WizardHeader
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WizardHeaderRenderer
{

    protected WizardHeader $wizardHeader;

    public function __construct(WizardHeader $wizardHeader)
    {
        $this->wizardHeader = $wizardHeader;
    }

    public function render(): string
    {
        $html = [];

        $html[] = '<ul class="nav nav-wizard publication-wizard">';

        foreach ($this->getStepTitles() as $index => $stepTitle)
        {
            $class = $this->wizardHeader->isStepSelected($index) ? 'active' : '';
            $html[] = '<li class="' . $class . '"><a href="#">' . $stepTitle . '</a></li>';
        }

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    protected function getStepTitles(): array
    {
        return $this->wizardHeader->getStepTitles();
    }
}