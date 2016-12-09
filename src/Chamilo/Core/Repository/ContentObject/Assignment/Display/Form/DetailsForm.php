<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Form;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DetailsForm extends FormValidator
{

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score
     */
    private $score;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note
     */
    private $note;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    private $assignmentDataProvider;

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score $score
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note $note
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param string $postUrl
     */
    public function __construct(Score $score = null, Note $note = null, AssignmentDataProvider $assignmentDataProvider, $postUrl)
    {
        parent::__construct('details', 'post', $postUrl);
        
        $this->score = $score;
        $this->note = $note;
        $this->assignmentDataProvider = $assignmentDataProvider;
        
        $this->buildForm();
        $this->setDefaults();
    }

    protected function buildForm()
    {
        $this->addElement('select', Score::PROPERTY_SCORE, Translation::get('Score'), $this->getScoreChoices());
        $this->add_html_editor(Note::PROPERTY_NOTE, Translation::get('Note'), false);
        
        $this->addElement(
            'style_button', 
            null, 
            Translation::get('Save', null, Utilities::COMMON_LIBRARIES), 
            null, 
            null, 
            'floppy-save');
    }

    protected function getScoreChoices()
    {
        $choices = array();
        
        $choices[- 1] = Translation::get('NoScore');
        
        for ($i = 0; $i <= 100; $i ++)
        {
            $choices[$i] = $i . '%';
        }
        
        return $choices;
    }

    public function setDefaults()
    {
        $defaultValues = array();
        
        if ($this->score instanceof Score)
        {
            $defaultValues[Score::PROPERTY_SCORE] = $this->score->getScore();
        }
        
        if ($this->note instanceof Note)
        {
            $defaultValues[Note::PROPERTY_NOTE] = $this->note->getNote();
        }
        
        return parent::setDefaults($defaultValues);
    }
}