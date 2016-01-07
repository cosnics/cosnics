<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Form;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class NoteForm extends FormValidator
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
        parent :: __construct('note', 'post', $postUrl);

        $this->entry = $entry;
        $this->assignmentDataProvider = $assignmentDataProvider;
        $this->renderer = clone $this->defaultRenderer();

        $this->buildForm();
        $this->setDefaults();

        $this->accept($this->renderer);
    }

    protected function buildForm()
    {
        $this->renderer->setElementTemplate('<div>{element}</div>');

        $this->add_html_editor(Note :: PROPERTY_NOTE, Translation :: get('Note'));
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