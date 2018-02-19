<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Form;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.weblcms.tool.assignment.php.component Custom form for the submissions feedback
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Bert De Clercq (Hogeschool Gent)
 */
class SubmissionSubmitForm extends FormValidator
{

    public function __construct($choices, $url = '')
    {
        parent::__construct('assignment', 'post', $url);
        
        $publication_id = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        
        // Retrieving assignment
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $publication_id);
        
        $assignment = $publication->get_content_object();
        
        // $this->addElement('category', Translation :: get('Properties', null, Utilities :: COMMON_LIBRARIES));
        // Submit as
        if ($assignment->get_allow_group_submissions())
        {
            $this->addElement(
                'select', 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_ID, 
                Translation::get('SubmitAs'), 
                $choices, 
                array('class' => 'form-control'));
        }
        // $this->addElement('category');
        
        $buttons = array();
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('SelectGroup'), 
            null, 
            null, 
            'arrow-right');
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->setDefaults();
    }

    public function setDefaults($defaults = array())
    {
        $submitter_type = Request::get(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_TYPE);
        
        if ($submitter_type ==
             \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP ||
             $submitter_type ==
             \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP)
        {
            $defaults[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_ID] = $submitter_type .
             Request::get(Manager::PARAM_TARGET_ID);
    }
    
    parent::setDefaults($defaults);
}
}
