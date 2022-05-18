<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib
 */

/**
 * Instances of this class group generic information about a complex object item
 *
 * @author Sven Vanpoucke
 */
class ComplexContentObjectItem extends CompositeDataClass implements DisplayOrderDataClassListenerSupport
{
    const PROPERTY_ADD_DATE = 'add_date';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    const PROPERTY_PARENT = 'parent_id';
    const PROPERTY_REF = 'ref_id';
    const PROPERTY_USER_ID = 'user_id';

    /**
     *
     * @var ContentObject
     */
    private $reference_object;

    public function __construct($default_properties = [], $additional_properties = null)
    {
        parent::__construct($default_properties, $additional_properties);
        $this->addListener(new DisplayOrderDataClassListener($this));
    }

    /**
     * Checks this object before saving + adds some default values
     *
     * @return boolean
     */
    public function checkBeforeSave()
    {
        if (!$this->get_add_date())
        {
            $this->set_add_date(time());
        }

        if (!$this->get_display_order())
        {
            $this->set_display_order(DataManager::select_next_display_order($this->get_parent()));
        }

        if (StringUtilities::getInstance()->isNullOrEmpty($this->get_ref()))
        {
            $this->addError(Translation::get('ReferenceObjectShouldNotBeEmpty'));
        }
        else
        {
            $ref_content_object = DataManager::retrieve_by_id(ContentObject::class, $this->get_ref());
            if (!$ref_content_object)
            {
                $this->addError(Translation::get('ReferenceObjectDoesNotExist'));
            }
        }

        if (StringUtilities::getInstance()->isNullOrEmpty($this->get_parent()))
        {
            $this->addError(Translation::get('ReferenceObjectShouldNotBeEmpty'));
        }
        else
        {
            $parent_content_object = DataManager::retrieve_by_id(ContentObject::class, $this->get_parent());
            if (!$parent_content_object)
            {
                $this->addError(Translation::get('ParentObjectDoesNotExist'));
            }
        }

        return !$this->hasErrors();
    }

    public static function factory($class, &$record = [])
    {
        if (is_subclass_of($class, ComplexContentObjectItem::class))
        {
            return parent::factory($class, $record);
        }
        elseif (is_subclass_of($class, ContentObject::class))
        {
            $class = ClassnameUtilities::getInstance()->getNamespaceFromClassname($class) . '\Complex' .
                ClassnameUtilities::getInstance()->getClassNameFromNamespace(
                    $class
                );

            return parent::factory($class, $record);
        }
        else
        {
            throw new ClassNotExistException($class);
        }
    }

    public function get_add_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_ADD_DATE);
    }

    /**
     * Retrieves the allowed types to add to this complex learning object item
     *
     * @return Array of learning object types
     */
    public function get_allowed_types()
    {
        return [];
    }

    /**
     * Get the default property names
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(
                self::PROPERTY_REF,
                self::PROPERTY_PARENT,
                self::PROPERTY_USER_ID,
                self::PROPERTY_DISPLAY_ORDER,
                self::PROPERTY_ADD_DATE
            )
        );
    }

    public function get_display_order()
    {
        return $this->getDefaultProperty(self::PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the properties that define the context for the display order (the properties on which has to be limited)
     *
     * @return Condition
     */
    public function get_display_order_context_properties()
    {
        return array(new PropertyConditionVariable(ComplexContentObjectItem::class, self::PROPERTY_PARENT));
    }

    /**
     * Returns the property for the display order
     *
     * @return string
     */
    public function get_display_order_property()
    {
        return new PropertyConditionVariable(ComplexContentObjectItem::class, self::PROPERTY_DISPLAY_ORDER);
    }

    public function get_parent()
    {
        return $this->getDefaultProperty(self::PROPERTY_PARENT);
    }

    public function get_parent_object()
    {
        return DataManager::retrieve_by_id(
            ContentObject::class, $this->get_parent()
        );
    }

    public function get_ref()
    {
        return $this->getDefaultProperty(self::PROPERTY_REF);
    }

    public function get_ref_object()
    {
        if (!isset($this->reference_object))
        {
            $this->reference_object = DataManager::retrieve_by_id(
                ContentObject::class, $this->get_ref()
            );
        }

        return $this->reference_object;
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'repository_complex_content_object_item';
    }

    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function is_complex()
    {
        return count($this->get_allowed_types()) > 0;
    }

    public function set_add_date($add_date)
    {
        $this->setDefaultProperty(self::PROPERTY_ADD_DATE, $add_date);
    }

    public function set_display_order($display_order)
    {
        $this->setDefaultProperty(self::PROPERTY_DISPLAY_ORDER, $display_order);
    }

    public function set_parent($parent)
    {
        $this->setDefaultProperty(self::PROPERTY_PARENT, $parent);
    }

    public function set_ref($ref)
    {
        $this->setDefaultProperty(self::PROPERTY_REF, $ref);
    }

    /**
     *
     * @param ContentObject $reference_object
     */
    public function set_ref_object(ContentObject $reference_object)
    {
        $this->reference_object = $reference_object;
    }

    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }
}
