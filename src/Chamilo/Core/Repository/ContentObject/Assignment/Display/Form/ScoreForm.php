<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ScoreForm extends FormValidator
{

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    private $entry;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    private $assignmentDataProvider;

    /**
     *
     * @var \HTML_QuickForm_Renderer_Default
     */
    private $renderer;

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     */
    public function __construct(Entry $entry, AssignmentDataProvider $assignmentDataProvider, $postUrl)
    {
        parent :: __construct('score', 'post', $postUrl);

        $this->entry = $entry;
        $this->assignmentDataProvider = $assignmentDataProvider;
        $this->renderer = clone $this->defaultRenderer();

        $this->buildForm();
        $this->setDefaults();

        $this->accept($this->renderer);
    }

    protected function buildForm()
    {
        $this->renderer->setElementTemplate('<div style="vertical-align: middle; float: left;">{element}</div>');

        $this->addElement('select', Score :: PROPERTY_SCORE, Translation :: get('Score'), $this->getScoreChoices());
    }

    protected function getScoreChoices()
    {
        $choices = array();

        $choices[- 1] = Translation :: get('NoScore');

        for ($i = 0; $i <= 100; $i ++)
        {
            $choices[$i] = $i . '%';
        }

        return $choices;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $html = array();

        $html[] = $this->renderer->toHTML();

        return implode('', $html);
    }
}