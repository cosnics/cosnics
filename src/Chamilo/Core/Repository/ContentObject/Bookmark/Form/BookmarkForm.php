<?php
namespace Chamilo\Core\Repository\ContentObject\Bookmark\Form;

use Chamilo\Core\Repository\ContentObject\Bookmark\Storage\DataClass\Bookmark;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: bookmark_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.bookmark
 */
class BookmarkForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent::build_creation_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->add_textfield(Bookmark::PROPERTY_URL, Translation::get('URL'), true, array('size' => '100'));
        $this->add_textfield(
            Bookmark::PROPERTY_APPLICATION, 
            Translation::get('Application'), 
            true, 
            array('size' => '100'));
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent::build_editing_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->add_textfield(Bookmark::PROPERTY_URL, Translation::get('URL'), true, array('size' => '100'));
        $this->add_textfield(
            Bookmark::PROPERTY_APPLICATION, 
            Translation::get('Application'), 
            true, 
            array('size' => '100'));
        $this->addElement('category');
    }

    public function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[Bookmark::PROPERTY_URL] = $lo->get_url();
            $defaults[Bookmark::PROPERTY_APPLICATION] = $lo->get_application();
        }
        else
        {
            $defaults[Bookmark::PROPERTY_URL] = 'http://';
        }
        parent::setDefaults($defaults);
    }

    public function create_content_object()
    {
        $object = new Bookmark();
        $object->set_url($this->exportValue(Bookmark::PROPERTY_URL));
        $object->set_application($this->exportValue(Bookmark::PROPERTY_APPLICATION));
        $this->set_content_object($object);
        return parent::create_content_object();
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_url($this->exportValue(Bookmark::PROPERTY_URL));
        $object->set_application($this->exportValue(Bookmark::PROPERTY_APPLICATION));
        return parent::update_content_object();
    }
}
