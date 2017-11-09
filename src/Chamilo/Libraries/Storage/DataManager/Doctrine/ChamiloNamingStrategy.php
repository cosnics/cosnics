<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;

/**
 * The chamilo naming strategy, defining table prefixes by the use of a const or the package name
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ChamiloNamingStrategy extends DefaultNamingStrategy
{

    /**
     *
     * @see \Doctrine\ORM\Mapping\DefaultNamingStrategy::classToTableName()
     */
    public function classToTableName($className)
    {
        $classNameUtilities = ClassnameUtilities::getInstance();
        $classNameString = StringUtilities::getInstance()->createString(
            $classNameUtilities->getClassnameFromNamespace($className));

        $tableName = $classNameString->underscored();

        if (strpos($tableName, '_entity') !== false)
        {
            $tableName = substr($tableName, 0, - 7);
        }

        $prefix = null;

        if (class_exists($className))
        {
            $class = new \ReflectionClass($className);
            if ($class->hasConstant('TABLE_PREFIX'))
            {
                $prefix = $class->getConstant('TABLE_PREFIX');
            }
        }

        if (! $prefix)
        {
            $namespace = $classNameUtilities->getNamespaceFromClassname($className);

            $context = strpos('Domain\Entity', $namespace) === false ? $classNameUtilities->getNamespaceParent(
                $namespace) : $classNameUtilities->getNamespaceParent(
                $classNameUtilities->getNamespaceParent($namespace));

            $prefix = StringUtilities::getInstance()->createString(
                $classNameUtilities->getPackageNameFromNamespace($context))->underscored();
        }

        return $prefix . '_' . $tableName;
    }
}