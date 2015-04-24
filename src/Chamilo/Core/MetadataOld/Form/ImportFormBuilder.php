<?php
namespace Chamilo\Core\MetadataOld\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This class builds the form to import the metadata structure
 * 
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImportFormBuilder
{
    const FORM_ELEMENT_METADATA_FILE = 'file';

    /**
     * The FormValidator reference
     * 
     * @var \libraries\format\FormValidator
     */
    private $form;

    /**
     * Constructor
     * 
     * @param FormValidator $form
     */
    public function __construct(FormValidator $form = null)
    {
        if (! $form)
        {
            $form = new FormValidator('import_form');
        }
        
        $this->form = $form;
    }

    /**
     * Builds the form
     */
    public function build_form()
    {
        $form = $this->form;
        
        $form->addElement(
            'file', 
            self :: FORM_ELEMENT_METADATA_FILE, 
            Translation :: get('File', null, Utilities :: COMMON_LIBRARIES));
        
        $form->addElement(
            'style_submit_button', 
            'submit', 
            Translation :: get('Upload', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'positive update'));
    }
}