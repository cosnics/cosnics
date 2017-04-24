<?php
namespace Chamilo\Core\Menu\Package;

use Chamilo\Core\Menu\Rights;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Core\Menu\Storage\DataClass\LanguageCategoryItem;
use Chamilo\Core\Menu\Storage\DataClass\RightsLocation;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Menu\Package
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     * Runs the install-script.
     */
    public function extra()
    {
        $rights_utilities = Rights::getInstance();
        $location = $rights_utilities->create_subtree_root_location(static::package(), 0, Rights::TREE_TYPE_ROOT, true);
        
        if (! $location instanceof RightsLocation)
        {
            return false;
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, 
                Translation::get(
                    'ObjectCreated', 
                    array('OBJECT' => Translation::get('RightsTree')), 
                    Utilities::COMMON_LIBRARIES));
        }
        
        $languageItem = new LanguageCategoryItem();
        $languageItem->set_display(Item::DISPLAY_BOTH);
        
        if (! $languageItem->create())
        {
            return false;
        }
        else
        {
            $itemTitle = new ItemTitle();
            $itemTitle->set_title(Translation::get('ChangeLanguage'));
            $itemTitle->set_isocode(Translation::getInstance()->getLanguageIsocode());
            $itemTitle->set_item_id($languageItem->get_id());
            
            if (! $itemTitle->create())
            {
                return false;
            }
        }
        
        return true;
    }
}
