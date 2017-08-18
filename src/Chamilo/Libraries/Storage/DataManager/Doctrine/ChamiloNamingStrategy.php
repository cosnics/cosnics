<?php

namespace Chamilo\Libraries\Storage\DataManager\Doctrine;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;

/**
 * The chamilo naming strategy, defining table prefixes by the use of a const or the package name
 *
 * @package application\countries
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ChamiloNamingStrategy extends DefaultNamingStrategy
{
    /**
     * {@inheritdoc}
     */
    public function classToTableName($className)
    {
        $classNameUtilities = ClassnameUtilities::getInstance();
        $classNameString = StringUtilities::getInstance()->createString(
            $classNameUtilities->getClassnameFromNamespace($className)
        );

        $table_name = $classNameString->underscored();

        if(strpos($table_name, '_entity') !== false)
        {
            $table_name = substr($table_name, 0, -7);
        }

        $prefix = null;

        if(class_exists($className))
        {
            $class = new \ReflectionClass($className);
            if ($class->hasConstant('TABLE_PREFIX'))
            {
                $prefix = $class->getConstant('TABLE_PREFIX');
            }
        }

        if(!$prefix)
        {
            $namespace = $classNameUtilities->getNamespaceFromClassname($className);

            $context = strpos('Domain\Entity', $namespace) === false ?
                $classNameUtilities->getNamespaceParent($namespace) :
                $classNameUtilities->getNamespaceParent(
                    $classNameUtilities->getNamespaceParent($namespace)
                );

            $prefix = StringUtilities::getInstance()->createString(
                $classNameUtilities->getPackageNameFromNamespace($context)
            )->underscored();
        }

        return $prefix . '_' . $table_name;
    }
}