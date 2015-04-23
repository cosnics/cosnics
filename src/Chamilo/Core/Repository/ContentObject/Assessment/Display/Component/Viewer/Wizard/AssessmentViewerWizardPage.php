<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\Wizard;

use Chamilo\Libraries\Format\Form\FormValidatorPage;

/**
 * $Id: assessment_viewer_wizard_page.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_display.assessment.component.viewer.wizard
 */
abstract class AssessmentViewerWizardPage extends FormValidatorPage
{

    /**
     * The parent in which the wizard runs.
     */
    private $parent;

    /**
     * Constructor
     * 
     * @param string $name A unique name of this page in the wizard
     * @param Tool $parent The parent in which the wizard runs.
     */
    public function __construct($name, $parent)
    {
        $this->parent = $parent;
        parent :: __construct($name, 'post');
        $this->updateAttributes(array('action' => $parent->get_parent()->get_url()));
    }

    /**
     * Returns the parent in which this wizard runs
     * 
     * @return Component
     */
    public function get_parent()
    {
        return $this->parent;
    }
}
