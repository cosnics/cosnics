<?php
namespace Chamilo\Core\Menu\Form\Item;

use Chamilo\Core\Menu\Form\ItemForm;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\LinkItem;
use Chamilo\Libraries\Utilities\Utilities;

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

        $this->addElement('category', $this->getTranslator()->trans('Properties', [], 'Chamilo\Core\Menu'));
        $this->add_textfield(
            LinkItem::PROPERTY_URL, $this->getTranslator()->trans('URL', [], 'Chamilo\Core\Menu'), true,
            array('size' => '100')
        );
        $this->addElement(
            'select', LinkItem::PROPERTY_TARGET, $this->getTranslator()->trans('Target', [], 'Chamilo\Core\Menu'),
            LinkItem::getTargetTypes(), array('class' => 'form-control')
        );
        $this->addRule(
            LinkItem::PROPERTY_TARGET,
            $this->getTranslator()->trans('ThisFieldIsRequired', [], Utilities::COMMON_LIBRARIES), 'required'
        );
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
        $defaults[LinkItem::PROPERTY_URL] = $item->getUrl();
        $defaults[LinkItem::PROPERTY_TARGET] = $item->getTarget();

        parent::setItemDefaults($item, $itemTitles, $defaults);
    }
}
