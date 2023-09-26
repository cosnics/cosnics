<?php
namespace Chamilo\Libraries\Architecture;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Architecture
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ClassnameUtilities
{

    protected static ?ClassnameUtilities $instance = null;

    /**
     * @var string[]
     */
    private array $classnameMap = [];

    /**
     * @var string[]
     */
    private array $namespaceChildMap = [];

    /**
     * @var string[]
     */
    private array $namespaceFromIdMap = [];

    /**
     * @var string[]
     */
    private array $namespaceIdMap = [];

    /**
     * @var string[]
     */
    private array $namespaceMap = [];

    /**
     * @var string[]
     */
    private array $namespaceParentMap = [];

    /**
     * @var string[]
     */
    private array $packageNamespaceMap = [];

    private StringUtilities $stringUtilities;

    public function __construct(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    public function getClassnameFromNamespace(string $fullyQualifiedClassname, bool $convertToUnderscores = false
    ): string
    {
        $convertToUnderscores = (int) $convertToUnderscores;

        if (!isset($this->classnameMap[$fullyQualifiedClassname]) ||
            !isset($this->classnameMap[$fullyQualifiedClassname][$convertToUnderscores]))
        {
            $classname = $this->stringUtilities->createString($fullyQualifiedClassname)->afterLast('\\');

            if ($convertToUnderscores)
            {
                $classname = $classname->underscored();
            }

            $this->classnameMap[$fullyQualifiedClassname][$convertToUnderscores] = $classname->toString();
        }

        return $this->classnameMap[$fullyQualifiedClassname][$convertToUnderscores];
    }

    public function getClassnameFromObject(object $object, bool $convertToUnderscores = false): string
    {
        return $this->getClassnameFromNamespace($object::class, $convertToUnderscores);
    }

    public static function getInstance(): ClassnameUtilities
    {
        if (is_null(static::$instance))
        {
            self::$instance = new static(new StringUtilities('UTF-8'));
        }

        return static::$instance;
    }

    /**
     * Get the child namespace, e.g.  Chamilo\Core\Repository > Core\Repository
     */
    public function getNamespaceChild(string $namespace, $levels = 1): string
    {
        if (!isset($this->namespaceChildMap[$namespace]) || !isset($this->namespaceChildMap[$namespace][$levels]))
        {
            $namespaceParts = explode('\\', $namespace);
            $namespaceParts = array_slice($namespaceParts, $levels);
            $this->namespaceChildMap[$namespace][$levels] = implode('\\', $namespaceParts);
        }

        return $this->namespaceChildMap[$namespace][$levels];
    }

    public function getNamespaceFromClassname(string $fullyQualifiedClassname): string
    {
        if (!isset($this->namespaceMap[$fullyQualifiedClassname]))
        {
            $this->namespaceMap[$fullyQualifiedClassname] =
                $this->stringUtilities->createString($fullyQualifiedClassname)->beforeLast('\\');
        }

        return $this->namespaceMap[$fullyQualifiedClassname];
    }

    public function getNamespaceFromId(string $namespaceIdentifier): string
    {
        return $this->namespaceFromIdMap[$namespaceIdentifier] = strtr($namespaceIdentifier, '-', '\\');
    }

    public function getNamespaceFromObject(object $object): string
    {
        return $this->getNamespaceFromClassname($object::class);
    }

    public function getNamespaceId(string $namespace): string
    {
        return $this->namespaceIdMap[$namespace] = strtr($namespace, '\\', '-');
    }

    /**
     * @description Get the parent namespace, e.g. Chamilo\Core\Repository > Chamilo\Core
     */
    public function getNamespaceParent(string $namespace, $levels = 1): string
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
     * @return string[]
     */
    public function getNamespacePath(string $namespace, bool $includeSelf = false, bool $reverseOrder = false): array
    {
        $namespacePath = [];
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

    public function getPackageNameFromNamespace(string $namespace, bool $convertToCamelCase = false): string
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

    public function getPackageNameFromObject(object $object, bool $convertToCamelcase = false): string
    {
        return $this->getPackageNameFromNamespace($this->getNamespaceFromObject($object), $convertToCamelcase);
    }
}