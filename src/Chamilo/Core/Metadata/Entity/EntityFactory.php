<?php
namespace Chamilo\Core\Metadata\Entity;

use Chamilo\Libraries\Architecture\ClassnameUtilities;

class EntityFactory
{

    /**
     *
     * @var \Chamilo\Core\Metadata\Entity\EntityFactory
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
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface
     */
    public function getEntity($dataClassName, $dataClassIdentifier = 0)
    {
        $entityClassName = $this->determineEntityClassName($dataClassName);
        return new $entityClassName($dataClassName, $dataClassIdentifier);
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
     * @return \Chamilo\Core\Metadata\Entity\EntityFactory
     */
    public static function getInstance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self(ClassnameUtilities :: getInstance());
        }
        return self :: $instance;
    }
}