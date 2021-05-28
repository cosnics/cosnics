<?php
namespace Chamilo\Core\Repository\ContentObject\PortfolioItem\Form;

use Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\PortfolioItem;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.content_object.portfolio_item
 */
class PortfolioItemForm extends ContentObjectForm
{

    public function build_creation_form($htmleditor_options = [], $in_tab = false)
    {
        parent::build_creation_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->addElement('text', PortfolioItem::PROPERTY_REFERENCE, Translation::get('Reference'));
    }

    public function build_editing_form($htmleditor_options = [], $in_tab = false)
    {
        parent::build_editing_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->addElement('text', PortfolioItem::PROPERTY_REFERENCE, Translation::get('Reference'));
    }

    public function create_content_object()
    {
        $object = new PortfolioItem();
        $object->set_reference($this->exportValue(PortfolioItem::PROPERTY_REFERENCE));
        $this->set_content_object($object);

        return parent::create_content_object();
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        $object = $this->get_content_object();
        $defaults[PortfolioItem::PROPERTY_REFERENCE] = $object->get_reference();
        parent::setDefaults($defaults);
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_reference($this->exportValue(PortfolioItem::PROPERTY_REFERENCE));

        return parent::update_content_object();
    }
}
