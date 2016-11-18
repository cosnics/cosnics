<?php
namespace Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestionsItem\Form;

use Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestionsItem\Storage\DataClass\FrequentlyAskedQuestionsItem;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: portfolio_item_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.portfolio_item
 */
class FrequentlyAskedQuestionsItemForm extends ContentObjectForm
{

    public function create_content_object()
    {
        $object = new FrequentlyAskedQuestionsItem();
        $object->set_reference($this->exportValue(FrequentlyAskedQuestionsItem::PROPERTY_REFERENCE));
        $this->set_content_object($object);
        return parent::create_content_object();
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_reference($this->exportValue(FrequentlyAskedQuestionsItem::PROPERTY_REFERENCE));
        return parent::update_content_object();
    }

    public function build_creation_form($default_content_object = null)
    {
        parent::build_creation_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->addElement('text', FrequentlyAskedQuestionsItem::PROPERTY_REFERENCE, Translation::get('Reference'));
        $this->addElement('category');
    }

    public function build_editing_form($object)
    {
        parent::build_editing_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->addElement('text', FrequentlyAskedQuestionsItem::PROPERTY_REFERENCE, Translation::get('Reference'));
        $this->addElement('category');
    }

    public function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        $defaults[FrequentlyAskedQuestionsItem::PROPERTY_REFERENCE] = $object->get_reference();
        parent::setDefaults($defaults);
    }
}
