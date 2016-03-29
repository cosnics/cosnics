<?php
namespace Chamilo\Core\Menu\Form\Item;

use Chamilo\Core\Menu\Form\ItemForm;
use Chamilo\Core\Menu\Storage\DataClass\ApplicationItem;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Menu\Form\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ApplicationItemForm extends ItemForm
{

    public function build_form()
    {
        $this->addElement('category', Translation :: get('Properties'));
        $this->addElement('checkbox', ApplicationItem :: PROPERTY_USE_TRANSLATION, Translation :: get('UseTranslation'));
        $this->addElement(
            'select', 
            ApplicationItem :: PROPERTY_APPLICATION, 
            Translation :: get('Application'), 
            $this->get_applications());
        $this->addRule(ApplicationItem :: PROPERTY_APPLICATION, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('category');
    }

    public function build_creation_form()
    {
        parent :: build_creation_form();
        $this->build_form();
    }

    public function build_editing_form()
    {
        parent :: build_editing_form();
        $this->build_form();
    }

    public function setDefaults($defaults = array ())
    {
        $item = $this->get_item();
        
        $defaults[ApplicationItem :: PROPERTY_APPLICATION] = $item->get_application();
        $defaults[ApplicationItem :: PROPERTY_USE_TRANSLATION] = $item->get_use_translation();
        
        parent :: setDefaults($defaults);
    }

    public function get_applications()
    {
        $items = Application :: get_active_packages();
        $applications = array();

        foreach ($items as $item)
        {
            $applications[$item] = Translation :: get('TypeName', null, $item);
        }

        return $applications;
    }
}
