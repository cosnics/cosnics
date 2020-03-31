<?php
namespace Chamilo\Core\Repository\ContentObject\PhysicalLocation\Form;

use Chamilo\Core\Repository\ContentObject\PhysicalLocation\Storage\DataClass\PhysicalLocation;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.content_object.physical_location
 */
/**
 * This class represents a form to create or update physical_locations
 */
class PhysicalLocationForm extends ContentObjectForm
{

    // Inherited
    public function create_content_object()
    {
        $object = new PhysicalLocation();
        $object->set_location($this->exportValue(PhysicalLocation::PROPERTY_LOCATION));
        $this->set_content_object($object);
        return parent::create_content_object();
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_location($this->exportValue(PhysicalLocation::PROPERTY_LOCATION));
        return parent::update_content_object();
    }

    protected function build_creation_form()
    {
        parent::build_creation_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->add_textfield(
            PhysicalLocation::PROPERTY_LOCATION,
            Translation::get('Location'),
            true,
            array('size' => '100'));
    }

    protected function build_editing_form()
    {
        parent::build_editing_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->add_textfield(
            PhysicalLocation::PROPERTY_LOCATION,
            Translation::get('Location'),
            true,
            array('size' => '100'));
    }

    public function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[PhysicalLocation::PROPERTY_LOCATION] = $lo->get_location();
        }
        else
        {
            $defaults[PhysicalLocation::PROPERTY_LOCATION] = 'grote markt, 1000, brussel, belgium';
        }
        parent::setDefaults($defaults);
    }
}
