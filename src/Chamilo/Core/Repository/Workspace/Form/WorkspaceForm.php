<?php
namespace Chamilo\Core\Repository\Workspace\Form;

use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceForm extends FormValidator
{

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    private $workspace;

    /**
     * Constructor
     * 
     * @param string $form_url
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     */
    public function __construct($formUrl, Workspace $workspace = null)
    {
        parent::__construct('workspace', self::FORM_METHOD_POST, $formUrl);
        
        $this->workspace = $workspace;
        
        $this->buildForm();
        $this->setFormDefaults();
    }

    /**
     * Builds this form
     */
    protected function buildForm()
    {
        $this->addElement('text', Workspace::PROPERTY_NAME, Translation::get('Name'), array("size" => "50"));
        
        $this->addRule(
            Workspace::PROPERTY_NAME, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->add_html_editor(Workspace::PROPERTY_DESCRIPTION, Translation::get('Description'));
        
        $this->addSaveResetButtons();
    }

    /**
     * Sets the default values
     * 
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     */
    protected function setFormDefaults()
    {
        $defaults = array();
        
        if ($this->workspace instanceof Workspace)
        {
            $defaults[Workspace::PROPERTY_NAME] = $this->workspace->getName();
            $defaults[Workspace::PROPERTY_DESCRIPTION] = $this->workspace->getDescription();
        }
        
        $this->setDefaults($defaults);
    }
}