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

    /**
     * @var \Chamilo\Configuration\Package\Storage\DataClass\Package[][]
     */
    protected array $nestedPackages;

    /**
     * @var \Chamilo\Configuration\Package\Storage\DataClass\Package[][][]
     */
    protected array $nestedTypedPackages;

    /**
     * @var \Chamilo\Configuration\Package\PackageList[]
     */
    protected array $packageLists;

    /**
     * @var \Chamilo\Configuration\Package\Storage\DataClass\Package[]
     */
    protected array $packages;

    protected string $type;

    protected ?InlineGlyph $typeInlineGlyph;

    protected string $typeName;

    /**
     * @var string[][]
     */
    private array $nestedPackageListTypes;

    /**
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

    /**
     * Get all distinct types defined in the PackageList and - if requested - it's children
     *
     * @return string[]
     */
    public function getNestedPackageListTypes(bool $recursive = true): array
    {
        if (!isset($this->nestedPackageListTypes[$recursive]))
        {
            $this->nestedPackageListTypes[$recursive] = [];

            if (count($this->getPackages()) > 0)
            {
                $this->nestedPackageListTypes[$recursive][] = $this->getType();
            }

            foreach ($this->getPackageLists() as $packageList)
            {
                if ($recursive)
                {
                    $packageListTypes = $packageList->getNestedPackageListTypes($recursive);

                    foreach ($packageListTypes as $packageListType)
                    {
                        $this->nestedPackageListTypes[$recursive][] = $packageListType;
                    }
                }
                else
                {
                    $this->nestedPackageListTypes[$recursive][] = $packageList->getType();
                }
            }
        }

        return $this->nestedPackageListTypes[$recursive];
    }

    /**
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package[]
     */
    public function getNestedPackages(bool $recursive = true): array
    {
        if (!isset($this->nestedPackages[$recursive]))
        {
            $this->nestedPackages[$recursive] = [];

            foreach ($this->getPackages() as $package)
            {
                $this->nestedPackages[$recursive][$package->get_context()] = $package;
            }

            foreach ($this->getPackageLists() as $packageList)
            {
                if ($recursive)
                {
                    $packageListpackages = $packageList->getNestedPackages($recursive);

                    foreach ($packageListpackages as $packageListPackage)
                    {
                        $this->nestedPackages[$recursive][$packageListPackage->get_context()] = $packageListPackage;
                    }
                }
                elseif (count($packageList->getPackages()) > 0)
                {
                    foreach ($packageList->getPackages() as $packageListPackage)
                    {
                        $this->nestedPackages[$recursive][$packageListPackage->get_context()] = $packageListPackage;
                    }
                }
            }
        }

        return $this->nestedPackages[$recursive];
    }

    /**
     * @param bool $recursive
     *
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package[][]
     */
    public function getNestedTypedPackages(bool $recursive = true): array
    {
        if (!isset($this->nestedTypedPackages[$recursive]))
        {
            $this->nestedTypedPackages[$recursive] = [];
            $this->nestedTypedPackages[$recursive][$this->getType()] = [];

            foreach ($this->getPackages() as $package)
            {
                $this->nestedTypedPackages[$recursive][$this->getType()][$package->get_context()] = $package;
            }

            foreach ($this->getPackageLists() as $packageList)
            {
                if ($recursive)
                {
                    $typedPackageListPackages = $packageList->getNestedTypedPackages($recursive);

                    foreach ($typedPackageListPackages as $type => $packageListPackages)
                    {
                        foreach ($packageListPackages as $packageListPackage)
                        {
                            $this->nestedTypedPackages[$recursive][$type][$packageListPackage->get_context()] =
                                $packageListPackage;
                        }
                    }
                }
                elseif (count($packageList->getPackages()) > 0)
                {
                    if (!isset($this->nestedTypedPackages[$recursive][$packageList->getType()]))
                    {
                        $this->nestedTypedPackages[$recursive][$packageList->getType()] = [];
                    }

                    foreach ($packageList->getPackages() as $packageListPackage)
                    {
                        $this->nestedTypedPackages[$recursive][$packageList->getType(
                        )][$packageListPackage->get_context()] = $packageListPackage;
                    }
                }
            }
        }

        return $this->nestedTypedPackages[$recursive];
    }

    /**
     * @return \Chamilo\Configuration\Package\PackageList[]
     */
    public function getPackageLists(): array
    {
        return $this->packageLists;
    }

    /**
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package[]
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
