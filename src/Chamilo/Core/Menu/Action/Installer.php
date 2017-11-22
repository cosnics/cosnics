<?php
namespace Chamilo\Core\Menu\Action;

use Chamilo\Core\Menu\ItemTitles;
use Chamilo\Core\Menu\Rights;
use Chamilo\Core\Menu\Storage\DataClass\ApplicationItem;
use Chamilo\Core\Menu\Storage\DataClass\CategoryItem;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassResultCache;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;

/**
 *
 * @package Chamilo\Core\Menu\Action
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     *
     * @var integer
     */
    private $itemDisplay;

    /**
     *
     * @var boolean
     */
    private $needsCategory;

    /**
     *
     * @param string[] $formValues
     * @param integer $itemDisplay
     * @param boolean $needsCategory
     */
    public function __construct($formValues, $itemDisplay = \Chamilo\Core\Menu\Storage\DataClass\Item :: DISPLAY_BOTH, $needsCategory = true)
    {
        parent::__construct($formValues);
        $this->itemDisplay = $itemDisplay;
        $this->needsCategory = $needsCategory;
    }

    /**
     *
     * @return integer
     */
    public function getItemDisplay()
    {
        return $this->itemDisplay;
    }

    /**
     *
     * @param integer $itemDisplay
     */
    public function setItemDisplay($itemDisplay)
    {
        $this->itemDisplay = $itemDisplay;
    }

    /**
     *
     * @return boolean
     */
    public function getNeedsCategory()
    {
        return $this->needsCategory;
    }

    /**
     *
     * @param boolean $needsCategory
     */
    public function setNeedsCategory($needsCategory)
    {
        $this->needsCategory = $needsCategory;
    }

    /**
     * Always add a menu-item for a web application.
     * Don't forget to call this method via parent :: extra() in
     * application installers that implement additional logic for this method.
     *
     * @return boolean
     */
    public function extra()
    {
        $context = ClassnameUtilities::getInstance()->getNamespaceParent($this->context(), 5);

        if ($this->getNeedsCategory() === true)
        {

            // Determine whether the category already exists, if not create it
            $info = \Chamilo\Configuration\Package\Storage\DataClass\Package::get($context);

            $category_name = Translation::get(
                (string) StringUtilities::getInstance()->createString($info->get_category())->upperCamelize(),
                null,
                self::context(),
                Translation::getInstance()->getLanguageIsocode());

            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ItemTitle::class_name(), ItemTitle::PROPERTY_TITLE),
                new StaticConditionVariable($category_name));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ItemTitle::class_name(), ItemTitle::PROPERTY_ISOCODE),
                new StaticConditionVariable(Translation::getInstance()->getLanguageIsocode()));

            $parameters = new DataClassDistinctParameters(
                new AndCondition($conditions),
                new DataClassProperties(
                    array(new PropertyConditionVariable(ItemTitle::class, ItemTitle::PROPERTY_ITEM_ID))));
            DataClassResultCache::truncate(ItemTitle::class_name());
            $titles = DataManager::distinct(ItemTitle::class_name(), $parameters);

            if (count($titles) > 0)
            {
                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_TYPE),
                    new StaticConditionVariable(CategoryItem::class_name()));
                $conditions[] = new InCondition(
                    new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_ID),
                    $titles);

                $category = DataManager::retrieve(
                    CategoryItem::class_name(),
                    new DataClassRetrieveParameters(new AndCondition($conditions)));
            }
            else
            {

                $languages = \Chamilo\Configuration\Configuration::getInstance()->getLanguages();
                $category = new CategoryItem();
                $category->set_parent(0);
                $category->set_display(CategoryItem::DISPLAY_BOTH);

                $item_titles = new ItemTitles();
                foreach ($languages as $isocode => $language)
                {
                    $item_title = new ItemTitle();
                    $item_title->set_title(
                        Translation::get(
                            (string) StringUtilities::getInstance()->createString($info->get_category())->upperCamelize(),
                            null,
                            self::context(),
                            $isocode));
                    $item_title->set_isocode($isocode);
                    $item_titles->add($item_title);
                }

                $category->set_titles($item_titles);
                if (! $category->create())
                {
                    return false;
                }
            }
        }

        // Create the actual menu item
        $item = new ApplicationItem();
        $item_title = new ItemTitle();
        $item_title->set_title(Translation::get('TypeName', null, $context));
        $item_title->set_isocode(Translation::getInstance()->getLanguageIsocode());
        $item_titles = new ItemTitles(new ArrayResultSet(array($item_title)));

        $item->set_titles($item_titles);
        $item->set_application($context);
        $item->set_display($this->getItemDisplay());

        if ($this->getNeedsCategory() === true)
        {
            $item->set_parent($category->get_id());
        }
        else
        {
            $item->set_parent(0);
        }

        $item->set_use_translation(1);

        // DataClassResultCache :: truncate(Item :: class_name());

        if (! $item->create())
        {
            return false;
        }

        if (! $this->setDefaultRights($item))
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     */
    public function setDefaultRights(Item $item)
    {
        $rightsUtilities = Rights::getInstance();
        $rightsLocation = $rightsUtilities->get_location_by_identifier(
            'Chamilo\Core\Menu',
            Rights::TYPE_ITEM,
            $item->getId());

        if (! $this->isAvailableForEveryone())
        {
            // Delete the default right (everyone can see the item)
            if (! $rightsUtilities->delete_location_entity_right_for_entity($rightsLocation, 0, 0))
            {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @return boolean
     */
    public function isAvailableForEveryone()
    {
        return true;
    }
}
