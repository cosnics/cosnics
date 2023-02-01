<?php
namespace Chamilo\Core\Metadata\Entity;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\DataClass\DataClass;

class DataClassEntityFactory
{

    private static ?DataClassEntityFactory $instance = null;

    private ClassnameUtilities $classNameUtilities;

    private array $entityClassNameCache;

    public function __construct(ClassnameUtilities $classNameUtilities)
    {
        $this->classNameUtilities = $classNameUtilities;
    }

    private function determineEntityClassName(string $dataClassName): string
    {
        if (!isset($this->entityClassNameCache[$dataClassName]))
        {
            $dataClassBaseName = $this->getClassNameUtilities()->getPackageNameFromNamespace($dataClassName);
            $dataClassPackage = $this->getClassNameUtilities()->getNamespaceParent($dataClassName, 3);

            $this->entityClassNameCache[$dataClassName] =
                $dataClassPackage . '\Integration\\' . __NAMESPACE__ . '\\' . $dataClassBaseName . 'Entity';
        }

        return $this->entityClassNameCache[$dataClassName];
    }

    public function getClassNameUtilities(): ClassnameUtilities
    {
        return $this->classNameUtilities;
    }

    public function getEntity(string $dataClassName, ?string $dataClassIdentifier = null, ?DataClass $dataClass = null
    ): EntityInterface
    {
        $entityClassName = $this->determineEntityClassName($dataClassName);

        return new $entityClassName($dataClassName, $dataClassIdentifier, $dataClass);
    }

    public function getEntityFromDataClass(DataClass $dataClass): DataClassEntity
    {
        return $this->getEntity(get_class($dataClass), $dataClass->getId(), $dataClass);
    }

    public function getEntityFromDataClassName(string $dataClassName): EntityInterface
    {
        return $this->getEntity($dataClassName, (string) DataClassEntity::INSTANCE_IDENTIFIER);
    }

    public function getEntityFromDataClassNameAndDataClassIdentifier(string $dataClassName, string $dataClassIdentifier
    ): EntityInterface
    {
        return $this->getEntity($dataClassName, $dataClassIdentifier);
    }

    public static function getInstance(): DataClassEntityFactory
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self(ClassnameUtilities::getInstance());
        }

        return self::$instance;
    }

    public function setClassNameUtilities(ClassnameUtilities $classNameUtilities)
    {
        $this->classNameUtilities = $classNameUtilities;
    }
}