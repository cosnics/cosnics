<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Form;

use Chamilo\Core\Repository\ContentObject\Wiki\Storage\DataClass\Wiki;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.content_object.wiki
 */
class WikiForm extends ContentObjectForm
{

    public function build_creation_form()
    {
        parent::build_creation_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->addElement('checkbox', 'locked', Translation::get('WikiLocked'));
        $this->add_html_editor('links', Translation::get('WikiToolBoxLinks'), false);
        // $this->addElement('textarea', 'links', Translation :: get('WikiToolBoxLinks'),
        // array('rows' => 5, 'cols' => 100));
    }

    public function build_editing_form()
    {
        parent::build_editing_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->addElement('checkbox', 'locked', Translation::get('WikiLocked'));
        $this->add_html_editor('links', Translation::get('WikiToolBoxLinks'), false);
        // $this->addElement('textarea', 'links', Translation :: get('WikiToolBoxLinks'),
        // array('rows' => 5, 'cols' => 100));
    }

    public function create_content_object()
    {
        $object = new Wiki();
        $object->set_locked($this->exportValue(Wiki::PROPERTY_LOCKED));
        $object->set_links($this->exportValue(Wiki::PROPERTY_LINKS));
        $this->set_content_object($object);

        return parent::create_content_object();
    }

    public function setDefaults($defaults = array())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[ContentObject::PROPERTY_ID] = $lo->get_id();

            $defaults[ContentObject::PROPERTY_TITLE] = $lo->get_title();
            $defaults[ContentObject::PROPERTY_DESCRIPTION] = $lo->get_description();
            $defaults[Wiki::PROPERTY_LOCKED] = $lo->get_locked();
            $defaults[Wiki::PROPERTY_LINKS] = $lo->get_links();
        }

        parent::setDefaults($defaults);
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_locked($this->exportValue(Wiki::PROPERTY_LOCKED));
        $object->set_links($this->exportValue(Wiki::PROPERTY_LINKS));
        $this->set_content_object($object);

        return parent::update_content_object();
    }
}
