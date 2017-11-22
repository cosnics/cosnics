<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionDetail;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This form allows a user to edit and save notes with a submission.
 * 
 * @author Bert De Clercq (Hogeschool Gent)
 */
class SubmissionDetailNotesSection extends FormValidator
{

    /**
     * Caching variable for the note tracker.
     * 
     * @var \application\weblcms\integration\core\tracking\tracker\SubmissionNote The note tracker
     */
    private $note_tracker;

    /**
     * Fills the form with all the data.
     * 
     * @param $note_tracker \application\weblcms\integration\core\tracking\tracker\SubmissionNote
     * @param $url string The url of the main page
     */
    public function __construct($note_tracker, $url = '')
    {
        parent::__construct('submission_notes', 'post', $url);
        $this->note_tracker = $note_tracker;
        
        $html_editor_options = array();
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar'] = 'RepositoryQuestion';
        
        $this->add_html_editor(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionNote::PROPERTY_NOTE, 
            Translation::get('NoteLong'), 
            false, 
            $html_editor_options);
        
        $save_button = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Save', null, Utilities::COMMON_LIBRARIES), 
            null, 
            null, 
            'arrow-right');
        $this->addElement($save_button);
        
        $this->set_initial_note();
    }

    /**
     * If a note already exists, set this note on the form.
     */
    private function set_initial_note()
    {
        if ($this->note_tracker)
        {
            $defaults = array();
            $defaults[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionNote::PROPERTY_NOTE] = $this->note_tracker->get_note();
            parent::setDefaults($defaults);
        }
    }

    /**
     * Updates or creates the note tracker.
     */
    public function set_note()
    {
        if ($this->note_tracker)
        {
            $this->update_tracker();
        }
        else
        {
            $this->create_tracker();
        }
    }

    /**
     * Creates a note tracker.
     * 
     * @return boolean
     */
    public function create_tracker()
    {
        $values = $this->exportValues();
        $note = $values[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionNote::PROPERTY_NOTE];
        
        if ($note == "" || $note == "<br />")
        {
            return false;
        }
        
        $arguments = array(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionNote::PROPERTY_SUBMISSION_ID => Request::get(
                Manager::PARAM_SUBMISSION), 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionNote::PROPERTY_CREATED => time(), 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionNote::PROPERTY_MODIFIED => time(), 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionNote::PROPERTY_USER_ID => Request::get(
                Manager::PARAM_TARGET_ID), 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionNote::PROPERTY_NOTE => $note);
        Event::trigger('NoteSubmission', \Chamilo\Application\Weblcms\Manager::context(), $arguments);
        
        return true;
    }

    /**
     * Updates the score tracker if the note is modified.
     * 
     * @return boolean
     */
    public function update_tracker()
    {
        $values = $this->exportValues();
        $note = $values[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionNote::PROPERTY_NOTE];
        
        if ($note == "" || $note == "<br />")
        {
            return $this->note_tracker->delete();
        }
        
        $this->note_tracker->set_note($note);
        $this->note_tracker->set_modified(time());
        
        return $this->note_tracker->update();
    }
}
