<?php
namespace Chamilo\Core\Metadata\Storage\DataClass;

use Chamilo\Core\Metadata\Interfaces\EntityTranslationInterface;
use Chamilo\Core\Metadata\Storage\DataClass\ElementInstance;
use Chamilo\Core\Metadata\Storage\DataClass\EntityTranslation;
use Chamilo\Core\Metadata\Traits\EntityTranslationTrait;
use Chamilo\Core\Metadata\Vocabulary\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class describes a metadata vocabulary
 * 
 * @package Chamilo\Core\Metadata\Vocabulary\Storage\DataClass
 * @author Jens Vanderheyden
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Vocabulary extends DataClass implements EntityTranslationInterface
{
    use EntityTranslationTrait;

    /**
     *
     * @var boolean
     */
    private $isDefault;
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_ELEMENT_ID = 'element_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_DEFAULT_VALUE = 'default_value';
    const PROPERTY_VALUE = 'value';

    /**
     * **************************************************************************************************************
     * Extended functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Get the default properties
     * 
     * @param string[] $extendedPropertyNames
     *
     * @return string[] The property names.
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_ELEMENT_ID;
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_DEFAULT_VALUE;
        $extendedPropertyNames[] = self::PROPERTY_VALUE;
        
        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     *
     * @return integer
     */
    public function get_element_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ELEMENT_ID);
    }

    /**
     *
     * @param integer
     */
    public function set_element_id($element_id)
    {
        $this->setDefaultProperty(self::PROPERTY_ELEMENT_ID, $element_id);
    }

    /**
     *
     * @return integer
     */
    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     *
     * @param integer
     */
    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }

    public function isForEveryone()
    {
        return $this->get_user_id() == 0;
    }

    public function getUser()
    {
        if ($this->isForEveryone())
        {
            return null;
        }
        
        return DataManager::retrieve_by_id(User::class, $this->get_user_id());
    }

    /**
     *
     * @return integer
     */
    public function get_default_value()
    {
        return $this->getDefaultProperty(self::PROPERTY_DEFAULT_VALUE);
    }

    /**
     *
     * @param integer
     */
    public function set_default_value($default_value)
    {
        $this->setDefaultProperty(self::PROPERTY_DEFAULT_VALUE, $default_value);
    }

    public function isDefault()
    {
        return (bool) $this->get_default_value();
    }

    /**
     *
     * @return string
     */
    public function get_value()
    {
        return $this->getDefaultProperty(self::PROPERTY_VALUE);
    }

    /**
     *
     * @param string
     */
    public function set_value($value)
    {
        $this->setDefaultProperty(self::PROPERTY_VALUE, $value);
    }

    /**
     * Returns the dependencies for this dataclass
     * 
     * @return string[string]
     */
    protected function get_dependencies($dependencies = [])
    {
        $dependencies = [];
        
        $dependencies[EntityTranslation::class] = new AndCondition(
            array(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        EntityTranslation::class,
                        EntityTranslation::PROPERTY_ENTITY_TYPE), 
                    new StaticConditionVariable(static::class)),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        EntityTranslation::class,
                        EntityTranslation::PROPERTY_ENTITY_ID), 
                    new StaticConditionVariable($this->get_id()))));
        
        $dependencies[ElementInstance::class] = new EqualityCondition(
            new PropertyConditionVariable(ElementInstance::class, ElementInstance::PROPERTY_VOCABULARY_ID),
            new StaticConditionVariable($this->get_id()));
        
        return $dependencies;
    }

    /**
     *
     * @return string
     */
    public function getTranslationFallback()
    {
        return $this->get_value();
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'metadata_vocabulary';
    }
}