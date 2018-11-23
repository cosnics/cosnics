<?php
namespace Chamilo\Core\Menu\Form\Item;

use Chamilo\Core\Menu\Form\ItemForm;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\LinkItem;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Menu\Form\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LinkItemForm extends ItemForm
{

    public function buildForm()
    {
        parent:: buildForm();

        $this->addElement('category', Translation::get('Properties'));
        $this->add_textfield(LinkItem::PROPERTY_URL, Translation::get('URL'), true, array('size' => '100'));
        $this->addElement(
            'select', LinkItem::PROPERTY_TARGET, Translation::get('Target'), LinkItem::get_target_types(),
            array('class' => 'form-control')
        );
        $this->addRule(LinkItem::PROPERTY_TARGET, Translation::get('ThisFieldIsRequired'), 'required');
        $this->addElement('category');
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\LinkItem $item
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[] $itemTitles
     * @param string[] $defaults
     *
     * @throws \Exception
     */
    public function setItemDefaults(Item $item, array $itemTitles, array $defaults = array())
    {
        $defaults[LinkItem::PROPERTY_URL] = $item->get_url();
        $defaults[LinkItem::PROPERTY_TARGET] = $item->get_target();

        parent::setItemDefaults($item, $itemTitles, $defaults);
    }
}
