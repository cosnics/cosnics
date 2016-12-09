<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionSubmit;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionsManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\WizardHeader\NumericWizardHeaderRenderer;
use Chamilo\Libraries\Format\Structure\WizardHeader\WizardHeader;
use Chamilo\Libraries\Platform\Translation;

/**
 * Renders the wizard for the submission submit
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class SubmissionSubmitWizardComponent extends SubmissionsManager
{

    /**
     *
     * @return string
     */
    public function render_header()
    {
        $translator = Translation::getInstance();
        
        $html = array();
        
        $html[] = parent::render_header();
        
        $wizardHeader = new WizardHeader();
        
        if ($this->allowGroupSubmissions())
        {
            $wizardHeader->addStepTitle($translator->getTranslation('SelectGroupStep', null, Manager::context()));
        }
        
        $wizardHeader->addStepTitle($translator->getTranslation('SelectSubmissionStep', null, Manager::context()));
        $wizardHeader->addStepTitle($translator->getTranslation('ConfirmationStep', null, Manager::context()));
        
        $selectedStepIndex = $this->getSelectedStepIndex();
        $wizardHeader->setSelectedStepIndex($selectedStepIndex);
        
        $wizardHeaderRenderer = new NumericWizardHeaderRenderer($wizardHeader);
        
        $html[] = $wizardHeaderRenderer->render();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the publication
     * 
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    protected function getPublication()
    {
        return \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $this->get_publication_id());
    }

    /**
     * Renders the confirmation message
     * 
     * @param string $confirmationMessage
     *
     * @return string
     */
    protected function renderConfirmationMessage($confirmationMessage)
    {
        $html = array();
        
        $html[] = '<div class="alert alert-success" style="font-size: 18px; text-align: center;">';
        
        $fontAwesomeGlyph = new BootstrapGlyph('ok', array('assignment-success-check'));
        
        $html[] = $fontAwesomeGlyph->render();
        $html[] = $confirmationMessage;
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Returns whether or not the assignment allows group submissions
     * 
     * @return bool
     */
    protected function allowGroupSubmissions()
    {
        return $this->getPublication()->get_content_object()->get_allow_group_submissions();
    }

    /**
     * Returns the selected step index
     * 
     * @return bool
     */
    abstract protected function getSelectedStepIndex();
}