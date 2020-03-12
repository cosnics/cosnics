<?php
namespace Chamilo\Libraries\Architecture;

use Chamilo\Libraries\Utilities\StringUtilities;
use ReflectionClass;

/**
 *
 * @package Chamilo\Libraries\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ClassnameUtilities
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    protected static $instance = null;

    /**
     *
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     *
     * @var string[]
     */
    private $namespaceIdMap = array();

    /**
     *
     * @var string[]
     */
    private $namespaceFromIdMap = array();

    /**
     *
     * @var string[]
     */
    private $namespaceMap = array();

    /**
     *
     * @var string[]
     */
    private $classnameMap = array();

    /**
     *
     * @var string[]
     */
    private $packageNamespaceMap = array();

    /**
     *
     * @var string[]
     */
    private $namespaceParentMap = array();

    /**
     *
     * @var string[]
     */
    private $namespaceChildMap = array();

    /**
     *
     * @var string[]
     */
    private $namespacePathMap = array();

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function __construct(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     * Get the unqualified classname from the fully qualified classname, excluding the namespace
     *
     * @param string $fullyQualifiedClassname
     * @param boolean $convertToUnderscores
     *
     * @return string
     */
    public function getClassnameFromNamespace($fullyQualifiedClassname, $convertToUnderscores = false)
    {
        $convertToUnderscores = (int) $convertToUnderscores;

        if (!isset($this->classnameMap[$fullyQualifiedClassname]) ||
            !isset($this->classnameMap[$fullyQualifiedClassname][$convertToUnderscores]))
        {
            $reflectionClass = new ReflectionClass($fullyQualifiedClassname);
            $classname = $reflectionClass->getShortName();

            if ($convertToUnderscores)
            {
                $classname = $this->stringUtilities->createString($classname)->underscored()->__toString();
            }

            $this->classnameMap[$fullyQualifiedClassname][$convertToUnderscores] = $classname;
        }

        return $this->classnameMap[$fullyQualifiedClassname][$convertToUnderscores];
    }

    /**
     * Get the unqualified classname from an object instance, excluding the namespace
     *
     * @param $object
     * @param boolean $convertToUnderscores
     *
     * @return string
     */
    public function getClassnameFromObject($object, $convertToUnderscores = false)
    {
        return $this->getClassnameFromNamespace((string) get_class($object), $convertToUnderscores);
    }

    /**
     * Return 'this' as singleton
     *
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    static public function getInstance()
    {
        if (is_null(static::$instance))
        {
            self::$instance = new static(new StringUtilities('UTF-8'));
        }

        return static::$instance;
    }

    /**
     * Get the child namespace, e.g.
     * Chamilo\Core\Repository > Core\Repository
     *
     * @param string $namespace
     *
     * @return string
     */
    public function getNamespaceChild($namespace, $levels = 1)
    {
        if (!isset($this->namespaceChildMap[$namespace]) || !isset($this->namespaceChildMap[$namespace][$levels]))
        {
            $namespaceParts = explode('\\', $namespace);
            $namespaceParts = array_slice($namespaceParts, $levels);
            $this->namespaceChildMap[$namespace][$levels] = implode('\\', $namespaceParts);
        }

        return $this->namespaceChildMap[$namespace][$levels];
    }

    /**
     * Get the namespace from a fully qualified classname
     *
     * @param string $fullyQualifiedClassname
     *
     * @return string
     */
    public function getNamespaceFromClassname($fullyQualifiedClassname)
    {
        if (!isset($this->namespaceMap[$fullyQualifiedClassname]))
        {
            $reflectionClass = new ReflectionClass($fullyQualifiedClassname);
            $this->namespaceMap[$fullyQualifiedClassname] = $reflectionClass->getNamespaceName();
        }

        return $this->namespaceMap[$fullyQualifiedClassname];
    }

    /**
     * Convert the namespace string to an id
     *
     * @param string $namespace
     *
     * @return string
     */
    public function getNamespaceFromId($namespaceIdentifier)
    {
        return $this->namespaceFromIdMap[$namespaceIdentifier] = strtr($namespaceIdentifier, '-', '\\');
    }

    /**
     * Get the namespace from an object instance, excluding the unqualified classname
     *
     * @param \stdClass $object
     *
     * @return string
     */
    public function getNamespaceFromObject($object)
    {
        return $this->getNamespaceFromClassname((string) get_class($object));
    }

    /**
     * Convert the namespace string to an id
     *
     * @param string $namespace
     *
     * @return string
     */
    public function getNamespaceId($namespace)
    {
        return $this->namespaceIdMap[$namespace] = strtr($namespace, '\\', '-');
    }

    /**
     * Get the parent namespace, e.g.
     * Chamilo\Core\Repository > Chamilo\Core
     *
     * @param string $namespace
     *
     * @return string
     */
    public function getNamespaceParent($namespace, $levels = 1)
    {
        if (!isset($this->namespaceParentMap[$namespace]) || !isset($this->namespaceParentMap[$namespace][$levels]))
        {
            $namespaceParts = explode('\\', $namespace);
            $namespaceParts = array_slice($namespaceParts, 0, - $levels);
            $this->namespaceParentMap[$namespace][$levels] = implode('\\', $namespaceParts);
        }

        return $this->namespaceParentMap[$namespace][$levels];
    }

    /**
     *
     * @param string $namespace
     *
     * @return string[]
     */
    public function getNamespacePath($namespace, $includeSelf = false, $reverseOrder = false)
    {
        $namespacePath = array();
        $namespaceParts = explode('\\', $namespace);

        if ($includeSelf)
        {
            $namespacePath[] = $namespace;
        }

        while (count($namespaceParts) > 1)
        {
            array_pop($namespaceParts);
            $namespacePath[] = implode('\\', $namespaceParts);
        }

        $namespacePath[] = '__ROOT__';

        return $reverseOrder ? array_reverse($namespacePath) : $namespacePath;
    }

    /**
     * Get the package name from a namespace
     *
     * @param string $namespace
     * @param boolean $convertToCamelCase
     *
     * @return string
     */
    public function getPackageNameFromNamespace($namespace, $convertToCamelCase = false)
    {
        $convertToCamelCase = (int) $convertToCamelCase;

        if (!isset($this->packageNamespaceMap[$namespace]) ||
            !isset($this->packageNamespaceMap[$namespace][$convertToCamelCase]))
        {
            $packageName = explode('\\', $namespace);
            $packageName = array_pop($packageName);

            if ($convertToCamelCase)
            {
                $packageName = $this->stringUtilities->createString($packageName)->camelize()->__toString();
            }

            $this->packageNamespaceMap[$namespace][$convertToCamelCase] = $packageName;
        }

        return $this->packageNamespaceMap[$namespace][$convertToCamelCase];
    }

    /**
     * Get the package name for a given object
     *
     * @param \stdClass Object
     * @param boolean $convertToCamelcase
     *
     * @return string
     */
    public function getPackageNameFromObject($object, $convertToCamelcase = false)
    {
        return $this->getPackageNameFromNamespace($this->getNamespaceFromObject($object), $convertToCamelcase);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     *
     * @return string
     */
    public function namespaceToPath($namespace, $web = false)
    {
        return $this->namespacePathMap[$namespace][(string) $web] = strtr(
            $namespace, '\\', ($web ? '/' : DIRECTORY_SEPARATOR)
        );
    }
}