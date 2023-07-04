<?php
namespace Chamilo\Configuration\Package;

use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 * Class to store a recursive structure of package types, associated packages and possible subpackages
 *
 * @package Chamilo\Configuration\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard
 */
class PackageList
{
    public const MODE_ALL = 1;
    public const MODE_AVAILABLE = 3;
    public const MODE_INSTALLED = 2;

    public const ROOT = '__ROOT__';

    private array $allPackages;

    private array $list;

    /**
     * @var \Chamilo\Configuration\Package\PackageList[]
     */
    private array $packageLists;

    /**
     * @var \Chamilo\Configuration\Package\Storage\DataClass\Package[]
     */
    private array $packages;

    /**
     * The type of the PackageList
     *
     * @var string
     */
    private string $type;

    private ?InlineGlyph $typeInlineGlyph;

    /**
     * The type name of the PackageList
     *
     * @var string
     */
    private string $typeName;

    /**
     * @var string[][]
     */
    private array $types;

    /**
     * @param string $type
     * @param string $typeName
     * @param ?\Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $typeInlineGlyph
     * @param \Chamilo\Configuration\Package\Storage\DataClass\Package[] $packages
     * @param \Chamilo\Configuration\Package\PackageList[] $packageLists
     */
    public function __construct(
        string $type, string $typeName, ?InlineGlyph $typeInlineGlyph = null, array $packages = [],
        array $packageLists = []
    )
    {
        $this->type = $type;
        $this->typeName = $typeName;
        $this->typeInlineGlyph = $typeInlineGlyph;
        $this->packages = $packages;
        $this->packageLists = $packageLists;
    }

    public function addPackage(Package $package): PackageList
    {
        $this->packages[$package->get_context()] = $package;

        return $this;
    }

    public function addPackageList(PackageList $child): PackageList
    {
        $this->packageLists[$child->getType()] = $child;

        return $this;
    }

    public function getAllPackages($recursive = true): array
    {
        if (!isset($this->allPackages[$recursive]))
        {
            $this->allPackages[$recursive] = [];
            $this->allPackages[$recursive][$this->getType()] = [];

            if (count($this->getPackages()) > 0)
            {
                $this->allPackages[$recursive][$this->getType()] = $this->getPackages();
            }

            foreach ($this->getPackageLists() as $child)
            {
                if ($recursive)
                {
                    $child_packages = $child->getAllPackages($recursive);

                    if (count($child_packages) > 0)
                    {
                        $this->allPackages[$recursive] = array_merge($this->allPackages[$recursive], $child_packages);
                    }
                }
                elseif (count($child->getPackages()) > 0)
                {
                    $this->allPackages[$recursive][$child->getType()] = $child->getPackages();
                }
            }
        }

        return $this->allPackages[$recursive];
    }

    /**
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package[]
     */
    public function getList(bool $recursive = true): array
    {
        if (!isset($this->list[$recursive]))
        {
            $this->list[$recursive] = [];

            if (count($this->getPackages()) > 0)
            {
                $this->list[$recursive] = $this->getPackages();
            }

            foreach ($this->getPackageLists() as $child)
            {
                if ($recursive)
                {
                    $child_packages = $child->getList($recursive);

                    if (count($child_packages) > 0)
                    {
                        $this->list[$recursive] = array_merge($this->list[$recursive], $child_packages);
                    }
                }
                elseif (count($child->getPackages()) > 0)
                {
                    $this->list[$recursive] = array_merge($this->list[$recursive], $child->getPackages());
                }
            }
        }

        return $this->list[$recursive];
    }

    /**
     * @return \Chamilo\Configuration\Package\PackageList[]
     */
    public function getPackageLists(): array
    {
        return $this->packageLists;
    }

    /**
     * @return string[]
     */
    public function getPackages(): array
    {
        return $this->packages;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTypeInlineGlyph(): InlineGlyph
    {
        return $this->typeInlineGlyph;
    }

    public function getTypeName(): string
    {
        return $this->typeName;
    }

    /**
     * Get all distinct types defined in the PackageList and - if requested - it's children
     *
     * @return string[]
     */
    public function getTypes(bool $recursive = true): array
    {
        if (!isset($this->types[$recursive]))
        {
            $this->types[$recursive] = [];

            if (count($this->getPackages()) > 0)
            {
                $this->types[$recursive][] = $this->getType();
            }

            foreach ($this->getPackageLists() as $child)
            {
                if ($recursive)
                {
                    $this->types[$recursive] = array_merge($this->types[$recursive], $child->getTypes($recursive));
                }
                else
                {
                    $this->types[$recursive][] = $child->getType();
                }
            }
        }

        return $this->types[$recursive];
    }

    public function hasPackage(string $packageName): bool
    {
        return array_key_exists($packageName, $this->packages);
    }

    public function hasPackageList(string $packageListName): bool
    {
        return array_key_exists($packageListName, $this->packageLists);
    }

    public function hasPackageLists(): bool
    {
        return count($this->getPackageLists()) > 0;
    }

    public function hasPackages(): bool
    {
        return count($this->getPackages()) > 0;
    }

    /**
     * @param \Chamilo\Configuration\Package\PackageList[] $packageLists
     */
    public function setPackageLists(array $packageLists): PackageList
    {
        $this->packageLists = $packageLists;

        return $this;
    }

    /**
     * @param \Chamilo\Configuration\Package\Storage\DataClass\Package[] $packages
     */
    public function setPackages(array $packages): PackageList
    {
        $this->packages = $packages;

        return $this;
    }

    public function setType(string $type): PackageList
    {
        $this->type = $type;

        return $this;
    }

    public function setTypeInlineGlyph(InlineGlyph $typeInlineGlyph): PackageList
    {
        $this->typeInlineGlyph = $typeInlineGlyph;

        return $this;
    }

    public function setTypeName(string $typeName): PackageList
    {
        $this->typeName = $typeName;

        return $this;
    }
}
