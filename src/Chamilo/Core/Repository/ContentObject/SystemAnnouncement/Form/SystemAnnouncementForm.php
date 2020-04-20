<?php
namespace Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Form;

use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Storage\DataClass\SystemAnnouncement;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.content_object.system_announcement
 */

/**
 * This class represents a form to create or update system announcements
 */
class SystemAnnouncementForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent::build_creation_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->add_select(
            SystemAnnouncement::PROPERTY_ICON, Translation::get('Icon'), SystemAnnouncement::get_possible_icons()
        );
    }

    protected function build_editing_form()
    {
        parent::build_editing_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->add_select(
            SystemAnnouncement::PROPERTY_ICON, Translation::get('Icon'), SystemAnnouncement::get_possible_icons()
        );
    }

    public function create_content_object()
    {
        $object = new SystemAnnouncement();
        $object->set_icon($this->exportValue(SystemAnnouncement::PROPERTY_ICON));
        $this->set_content_object($object);

        return parent::create_content_object();
    }

    public function setDefaults($defaults = array())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[SystemAnnouncement::PROPERTY_ICON] = $lo->get_icon();
        }
        parent::setDefaults($defaults);
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_icon($this->exportValue(SystemAnnouncement::PROPERTY_ICON));

        return parent::update_content_object();
    }
}
