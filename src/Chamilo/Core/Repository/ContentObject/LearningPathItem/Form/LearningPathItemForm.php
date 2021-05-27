<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPathItem\Form;

use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\LearningPathItem;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.content_object.learning_path_item
 */
class LearningPathItemForm extends ContentObjectForm
{

    public function build_creation_form($default_content_object = null)
    {
        parent::build_creation_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->addElement('text', LearningPathItem::PROPERTY_REFERENCE, Translation::get('Reference'));
    }

    public function build_editing_form($object)
    {
        parent::build_editing_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->addElement('text', LearningPathItem::PROPERTY_REFERENCE, Translation::get('Reference'));
    }

    public function create_content_object()
    {
        $object = new LearningPathItem();
        $object->set_reference($this->exportValue(LearningPathItem::PROPERTY_REFERENCE));
        $this->set_content_object($object);

        return parent::create_content_object();
    }

    public function setDefaults($defaults = [])
    {
        $object = $this->get_content_object();
        if ($object)
        {
            $defaults[LearningPathItem::PROPERTY_REFERENCE] = $object->get_reference();
            parent::setDefaults($defaults);
        }
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_reference($this->exportValue(LearningPathItem::PROPERTY_REFERENCE));

        return parent::update_content_object();
    }
}
