<?php
namespace Chamilo\Application\Survey\Form;

use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class PublicationForm extends FormValidator
{
    const TYPE_EDIT = 1;
    const TYPE_CREATE = 2;
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_ELEMENTS = 'target_users_and_groups_elements';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    const PARAM_FOREVER = 'forever';
    const PARAM_FROM_DATE = 'from_date';
    const PARAM_TO_DATE = 'to_date';

    private $publication;

    private $content_object;

    private $user;

    private $title;

    function __construct($form_type, $content_object, $user, $action, $publication, $title)
    {
        parent :: __construct('survey_publication_settings', 'post', $action);

        $this->content_object = $content_object;
        $this->user = $user;
        $this->publication = $publication;
        $this->form_type = $form_type;
        $this->title = $title;

        switch ($this->form_type)
        {
            case self :: TYPE_EDIT :
                $this->build_edit_form();
                break;
            case self :: TYPE_CREATE :
                $this->build_create_form();
                break;
        }

        $this->add_footer($this->form_type);
        $this->setDefaults();
    }

    function build_edit_form()
    {
        $this->addElement(
            'text',
            Publication :: PROPERTY_TITLE,
            Translation :: get('PublicationTitle'),
            array('size' => 100, 'value' => $this->publication->getTitle()));
        $this->addRule(Publication :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_forever_or_timewindow(Translation :: get('PublicationPeriod'));
    }

    function build_create_form()
    {
        $this->addElement(
            'text',
            Publication :: PROPERTY_TITLE,
            Translation :: get('PublicationTitle'),
            array('size' => 100, 'value' => $this->title));
        $this->addRule(Publication :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_forever_or_timewindow(Translation :: get('PublicationPeriod'));
        $this->addElement('hidden', 'ids', serialize($this->content_object));
    }

    function add_footer($form_type)
    {
        $submit = ($form_type == self :: TYPE_CREATE) ? Translation :: get(
            'Publish',
            null,
            Utilities :: COMMON_LIBRARIES) : Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES);
        $buttons[] = $this->createElement('style_submit_button', 'submit', $submit);
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_publication()
    {
        $values = $this->exportValues();

        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = DatetimeUtilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
            $to = DatetimeUtilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
        }

        $publication = $this->publication;
        $publication->setFromDate($from);
        $publication->setTitle($values[Publication :: PROPERTY_TITLE]);
        $publication->setToDate($to);

        if ($publication->update())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function create_publications()
    {
        $values = $this->exportValues();

        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = DatetimeUtilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
            $to = DatetimeUtilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
        }

        $ids = unserialize($values['ids']);

        $succes = false;

        foreach ($ids as $id)
        {
            $publication = new Publication();
            $publication->setContentObjectId($id);
            $publication->setPublisherId($this->user->get_id());
            $publication->setTitle($values[Publication :: PROPERTY_TITLE]);
            $publication->setPublished(time());
            $publication->setFromDate($from);
            $publication->setToDate($to);

            if (! $publication->create())
            {
                $succes = false;
            }
            else
            {
                $succes = true;
            }
        }
        return $succes;
    }

    /**
     * Sets the default values of the form.
     * By default the publication is for everybody who has access to the tool and
     * the publication will be available forever.
     */
    function setDefaults()
    {
        $defaults = array();
        if (! $this->publication)
        {
            $defaults[self :: PARAM_FOREVER] = 1;
        }
        else
        {

            if ($this->publication->getFromDate() == 0)
            {
                $defaults[self :: PARAM_FOREVER] = 1;
            }
            else
            {
                $defaults[self :: PARAM_FOREVER] = 0;
                $defaults[self :: PARAM_FROM_DATE] = $this->publication->getFromDate();
                $defaults[self :: PARAM_TO_DATE] = $this->publication->getToDate();
            }
        }
        parent :: setDefaults($defaults);
    }
}
?>