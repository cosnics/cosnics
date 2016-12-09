<?php
namespace Chamilo\Core\Metadata\Entity;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\DataClass\DataClass;

class DataClassEntityFactory
{

    /**
     *
     * @var \Chamilo\Core\Metadata\Entity\DataClassEntityFactory
     */
    private static $instance;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    private $classNameUtilities;

    /**
     *
     * @var string[]
     */
    private $entityClassNameCache;

    /**
     *
     * @param string $dataClassName
     * @param integer $dataClassIdentifier
     */
    public function __construct(ClassnameUtilities $classNameUtilities)
    {
        $this->classNameUtilities = $classNameUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    public function getClassNameUtilities()
    {
        return $this->classNameUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classNameUtilities
     */
    public function setClassNameUtilities($classNameUtilities)
    {
        $this->classNameUtilities = $classNameUtilities;
    }

    /**
     *
     * @param string $dataClassName
     * @param integer $dataClassIdentifier
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface
     */
    public function getEntity($dataClassName, $dataClassIdentifier = null, DataClass $dataClass = null)
    {
        $entityClassName = $this->determineEntityClassName($dataClassName);
        return new $entityClassName($dataClassName, $dataClassIdentifier, $dataClass);
    }

    /**
     *
     * @param string $dataClassName
     * @param integer $dataClassIdentifier
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface
     */
    public function getEntityFromDataClassNameAndDataClassIdentifier($dataClassName, $dataClassIdentifier)
    {
        return $this->getEntity($dataClassName, $dataClassIdentifier);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface
     */
    public function getEntityFromDataClass(DataClass $dataClass)
    {
        return $this->getEntity($dataClass->class_name(), $dataClass->get_id(), $dataClass);
    }

    /**
     *
     * @param string $dataClassName
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface
     */
    public function getEntityFromDataClassName($dataClassName)
    {
        return $this->getEntity($dataClassName, DataClassEntity::INSTANCE_IDENTIFIER);
    }

    /**
     *
     * @param string $dataClassName
     * @return string
     */
    private function determineEntityClassName($dataClassName)
    {
        if (! isset($this->entityClassNameCache[$dataClassName]))
        {
            $dataClassBaseName = $this->getClassNameUtilities()->getPackageNameFromNamespace($dataClassName);
            $dataClassPackage = $this->getClassNameUtilities()->getNamespaceParent($dataClassName, 3);
            
            $this->entityClassNameCache[$dataClassName] = $dataClassPackage . '\Integration\\' . __NAMESPACE__ . '\\' .
                 $dataClassBaseName . 'Entity';
        }
        
        return $this->entityClassNameCache[$dataClassName];
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Entity\DataClassEntityFactory
     */
    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new self(ClassnameUtilities::getInstance());
        }
        return self::$instance;
    }
}