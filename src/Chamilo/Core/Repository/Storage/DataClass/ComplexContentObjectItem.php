<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassTypeAwareInterface;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\DataClass\Traits\DataClassTypeAwareTrait;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\Storage\DataClass
 * @author Sven Vanpoucke
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ComplexContentObjectItem extends DataClass
    implements DisplayOrderDataClassListenerSupport, DataClassTypeAwareInterface
{
    use DataClassTypeAwareTrait;

    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ADD_DATE = 'add_date';
    public const PROPERTY_DISPLAY_ORDER = 'display_order';
    public const PROPERTY_PARENT = 'parent_id';
    public const PROPERTY_REF = 'ref_id';
    public const PROPERTY_USER_ID = 'user_id';

    private ?ContentObject $referenceObject;

    public function __construct($default_properties = [], array $optionalProperties = [])
    {
        parent::__construct($default_properties, $optionalProperties);
        $this->setType(static::class);
        $this->addListener(new DisplayOrderDataClassListener($this));
    }

    /**
     * Checks this object before saving + adds some default values
     */
    protected function checkBeforeSave(): bool
    {
        $translator = $this->getTranslator();

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
            $this->addError($translator->trans('ReferenceObjectShouldNotBeEmpty', [], Manager::CONTEXT));
        }
        else
        {
            $ref_content_object = DataManager::retrieve_by_id(ContentObject::class, $this->get_ref());
            if (!$ref_content_object)
            {
                $this->addError($translator->trans('ReferenceObjectDoesNotExist', [], Manager::CONTEXT));
            }
        }

        if (StringUtilities::getInstance()->isNullOrEmpty($this->get_parent()))
        {
            $this->addError($translator->trans('ReferenceObjectShouldNotBeEmpty', [], Manager::CONTEXT));
        }
        else
        {
            $parent_content_object = DataManager::retrieve_by_id(ContentObject::class, $this->get_parent());
            if (!$parent_content_object)
            {
                $this->addError($translator->trans('ParentObjectDoesNotExist', [], Manager::CONTEXT));
            }
        }

        return parent::checkBeforeSave();
    }

    public static function getCompositeDataClassName(): string
    {
        return ComplexContentObjectItem::class;
    }

    /**
     * Get the default property names
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_REF,
                self::PROPERTY_PARENT,
                self::PROPERTY_USER_ID,
                self::PROPERTY_DISPLAY_ORDER,
                self::PROPERTY_ADD_DATE
            ]
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[]
     */
    public function getDisplayOrderContextProperties(): array
    {
        return [new PropertyConditionVariable(ComplexContentObjectItem::class, self::PROPERTY_PARENT)];
    }

    public function getDisplayOrderProperty(): PropertyConditionVariable
    {
        return new PropertyConditionVariable(ComplexContentObjectItem::class, self::PROPERTY_DISPLAY_ORDER);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_complex_content_object_item';
    }

    public function get_add_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_ADD_DATE);
    }

    /**
     * Retrieves the allowed types to add to this complex learning object item
     *
     * @return string[]
     */
    public function get_allowed_types(): array
    {
        return [];
    }

    public function get_display_order()
    {
        return $this->getDefaultProperty(self::PROPERTY_DISPLAY_ORDER);
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
        if (!isset($this->referenceObject))
        {
            $this->referenceObject = DataManager::retrieve_by_id(
                ContentObject::class, $this->get_ref()
            );
        }

        return $this->referenceObject;
    }

    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function is_complex(): bool
    {
        return count($this->get_allowed_types()) > 0;
    }

    public function set_add_date($add_date): static
    {
        return $this->setDefaultProperty(self::PROPERTY_ADD_DATE, $add_date);
    }

    public function set_display_order($display_order): static
    {
        return $this->setDefaultProperty(self::PROPERTY_DISPLAY_ORDER, $display_order);
    }

    public function set_parent($parent): static
    {
        return $this->setDefaultProperty(self::PROPERTY_PARENT, $parent);
    }

    public function set_ref($ref): static
    {
        return $this->setDefaultProperty(self::PROPERTY_REF, $ref);
    }

    public function set_ref_object(ContentObject $reference_object): static
    {
        $this->referenceObject = $reference_object;

        return $this;
    }

    public function set_user_id($user_id): static
    {
        return $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }
}
