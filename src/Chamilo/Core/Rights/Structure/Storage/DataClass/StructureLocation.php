<?php
namespace Chamilo\Core\Rights\Structure\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Defines a structure location
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class StructureLocation extends DataClass
{
    const PROPERTY_CONTEXT = 'context';
    const PROPERTY_ACTION = 'action';

    /**
     * Get the default properties of all data classes.
     * 
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_CONTEXT;
        $extendedPropertyNames[] = self::PROPERTY_ACTION;
        
        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     *
     * @return string
     */
    public function getContext()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTEXT);
    }

    /**
     *
     * @param string $context
     *
     * @return $this
     */
    public function setContext($context)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTEXT, $context);
        
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getDefaultProperty(self::PROPERTY_ACTION);
    }

    /**
     *
     * @param string $action
     *
     * @return $this
     */
    public function setAction($action)
    {
        $this->setDefaultProperty(self::PROPERTY_ACTION, $action);
        
        return $this;
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'rights_structure_location';
    }
}