<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseDeleter\Forms;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This form can be used to delete a course
 *
 * @author Mattias De Pauw - Hogeschool Gent
 */
class CourseDeleterForm extends FormValidator
{

    private $parent;

    /**
     * Constructor
     *
     * @param Tool $parent The repository tool in which this
     */
    public function __construct($parent)
    {
        parent :: __construct();
        $this->parent = $parent;
    }

    /**
     * Builds the form.
     * The message is showed to the user and a checkbox is added to allow the
     * user to confirm the message.
     */
    public function buildForm()
    {
        $this->addElement('static', '', '', Translation :: get('DeleteWarningMessage'));
        $this->addElement('checkbox', 'confirm', Translation :: get('Confirm', null, Utilities :: COMMON_LIBRARIES));
        $this->addRule(
            'confirm',
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $prevnext[] = $this->createElement(
            'submit',
            $this->parent->get_url(),
            Translation :: get('Submit', null, Utilities :: COMMON_LIBRARIES));
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->updateAttributes(array('action' => $this->parent->get_url()));
    }
}
