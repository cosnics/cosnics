<?php
namespace Chamilo\Configuration\Package;

use Chamilo\Configuration\Package\Storage\DataClass\Package;

/**
 * Class to store a recursive structure of package types, associated packages and possible subpackages
 *
 * @author Hans De Bisschop
 * @author Magali Gillard
 */
class PackageList
{
    // A constant to represent the root namespace
    const MODE_ALL = 1;

    // Different modes of interpreting a PackageList's available packages

    const MODE_AVAILABLE = 3;

    const MODE_INSTALLED = 2;

    const ROOT = '__ROOT__';

    /**
     * The type of the PackageList
     *
     * @var string
     */
    private $type;

    /**
     * The type name of the PackageList
     *
     * @var string
     */
    private $type_name;

    /**
     * The type icon of the PackageList
     *
     * @var string
     */
    private $type_icon;

    /**
     * The packages of this specific type
     *
     * @var string[]
     */
    private $packages;

    /**
     * The list of PackageList objects for the sub-types of this type
     *
     * @var \Chamilo\Configuration\Package\PackageList[]
     */
    private $children;

    /**
     * Property to cache the available types
     *
     * @var boolean[]:string
     */
    private $types;

    private $list;

    private $all_packages;

    /**
     * @param string $type
     * @param string $type_name
     * @param \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $type_icon
     * @param array $packages
     * @param \Chamilo\Configuration\Package\PackageList[] $children
     */
    public function __construct(
        $type, $type_name, $type_icon = null, array $packages = [], array $children = []
    )
    {
        $this->type = $type;
        $this->type_name = $type_name;
        $this->type_icon = $type_icon;
        $this->packages = $packages;
        $this->children = $children;
    }

    /**
     * Add a child to the list of subtypes
     *
     * @param $child PackageList
     */
    public function add_child(PackageList $child)
    {
        $this->children[$child->get_type()] = $child;
    }

    /**
     * Add a package to the list of packages
     *
     * @param $package string
     */
    public function add_package(Package $package)
    {
        $this->packages[$package->get_context()] = $package;
    }

    public function get_all_packages($recursive = true)
    {
        if (!isset($this->all_packages[$recursive]))
        {
            $this->all_packages[$recursive] = [];

            if (count($this->get_packages()) > 0)
            {
                $this->all_packages[$recursive][$this->get_type()] = $this->get_packages();
            }

            foreach ($this->get_children() as $child)
            {
                if ($recursive)
                {
                    $child_packages = $child->get_all_packages($recursive);

                    if (count($child_packages) > 0)
                    {

                        $this->all_packages[$recursive] = array_merge($this->all_packages[$recursive], $child_packages);
                    }
                }
                else
                {
                    if (count($child->get_packages($recursive)) > 0)
                    {
                        $this->all_packages[$recursive][$child->get_type()] = $child->get_packages($recursive);
                    }
                }
            }
        }

        return $this->all_packages[$recursive];
    }

    /**
     * Get the list of PackageList objects for the sub-types of this type
     *
     * @return \Chamilo\Configuration\Package\PackageList[]
     */
    public function get_children()
    {
        return $this->children;
    }

    /**
     * Set the list of PackageList objects for the sub-types of this type
     *
     * @param $children \Chamilo\Configuration\Package\PackageList[]
     */
    public function set_children($children)
    {
        $this->children = $children;
    }

    /**
     * @param bool $recursive
     *
     * @return Package[]
     */
    public function get_list($recursive = true)
    {
        if (!isset($this->list[$recursive]))
        {
            $this->list[$recursive] = [];

            if (count($this->get_packages()) > 0)
            {
                $this->list[$recursive] = $this->get_packages();
            }

            foreach ($this->get_children() as $child)
            {
                if ($recursive)
                {
                    $child_packages = $child->get_list($recursive);

                    if (count($child_packages) > 0)
                    {

                        $this->list[$recursive] = array_merge($this->list[$recursive], $child_packages);
                    }
                }
                else
                {
                    if (count($child->get_packages($recursive)) > 0)
                    {
                        $this->list[$recursive] = $child->get_packages($recursive);
                    }
                }
            }
        }

        return $this->list[$recursive];
    }

    /**
     * Get the type packages
     *
     * @return string[]
     */
    public function get_packages()
    {
        return $this->packages;
    }

    /**
     * Set the type packages
     *
     * @param $packages string[]
     */
    public function set_packages($packages)
    {
        $this->packages = $packages;
    }

    /**
     * Get the type
     *
     * @return string
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * Set the type
     *
     * @param $type string
     */
    public function set_type($type)
    {
        $this->type = $type;
    }

    /**
     * Get the type icon
     *
     * @return \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph
     */
    public function get_type_icon()
    {
        return $this->type_icon;
    }

    /**
     * Set the type icon
     *
     * @param $type_icon string
     */
    public function set_type_icon($type_icon)
    {
        $this->type_icon = $type_icon;
    }

    /**
     * Get the type name
     *
     * @return string
     */
    public function get_type_name()
    {
        return $this->type_name;
    }

    /**
     * Set the type name
     *
     * @param $type_name string
     */
    public function set_type_name($type_name)
    {
        $this->type_name = $type_name;
    }

    /**
     * Get all distinct types defined in the PackageList and - if requested - it's children
     *
     * @param $recursive boolean
     */
    public function get_types($recursive = true)
    {
        if (!isset($this->types[$recursive]))
        {
            $this->types[$recursive] = [];

            if (count($this->get_packages()) > 0)
            {
                $this->types[$recursive][] = $this->get_type();
            }

            foreach ($this->get_children() as $child)
            {
                if ($recursive)
                {
                    $this->types[$recursive] = array_merge($this->types[$recursive], $child->get_types($recursive));
                }
                else
                {
                    $this->types[$recursive][] = $child->get_type();
                }
            }
        }

        return $this->types[$recursive];
    }

    /**
     *
     * @param string $child
     *
     * @return boolean
     */
    public function has_child($child)
    {
        return array_key_exists($child, $this->children);
    }

    /**
     * Returns whether the type has children
     *
     * @return boolean
     */
    public function has_children()
    {
        return count($this->get_children()) > 0;
    }

    /**
     *
     * @param string $package
     *
     * @return boolean
     */
    public function has_package($package)
    {
        return array_key_exists($package, $this->packages);
    }

    /**
     * Returns whether the type has packages
     *
     * @return boolean
     */
    public function has_packages()
    {
        return count($this->get_packages()) > 0;
    }
}
