<?php

namespace Chamilo\Libraries\Format\Structure\WizardHeader;

/**
 * Describes a header for a wizard
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WizardHeaderRenderer
{
    /**
     * The wizard header object that needs to be rendered
     *
     * @var WizardHeader
     */
    protected $wizardHeader;

    /**
     * WizardHeaderRenderer constructor.
     *
     * @param WizardHeader $wizardHeader
     */
    public function __construct(WizardHeader $wizardHeader)
    {
        $this->wizardHeader = $wizardHeader;
    }

    /**
     * Renders the wizard header
     */
    public function render()
    {
        $html = array();

        $html[] = '<ul class="nav nav-wizard publication-wizard">';

        foreach($this->getStepTitles() as $index => $stepTitle)
        {
            $class = $this->wizardHeader->isStepSelected($index) ? 'active' : '';
            $html[] = '<li class="' . $class . '"><a href="#">' . $stepTitle .
                '</a></li>';
        }

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the step titles
     *
     * @return \string[]
     */
    protected function getStepTitles()
    {
        return $this->wizardHeader->getStepTitles();
    }

}