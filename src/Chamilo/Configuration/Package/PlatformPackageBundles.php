<?php
namespace Chamilo\Configuration\Package;

use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;

/**
 *
 * @package Chamilo\Configuration\Package
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PlatformPackageBundles
{
    const MODE_ALL = 1;
    const MODE_AVAILABLE = 3;
    const MODE_INSTALLED = 2;

    /**
     *
     * @var \Chamilo\Configuration\Package\PlatformPackageBundles
     */
    private static $instance;

    /**
     *
     * @var int
     */
    private $mode;

    /**
     *
     * @var PackageList
     */
    private $package_list;

    /**
     *
     * @var string[]
     */
    private $types;

    /**
     *
     * @var string[]
     */
    private $packages;

    /**
     * A list of packages grouped by package type
     *
     * @var string[][]
     */
    private $type_packages;

    /**
     *
     * @param int $mode
     */
    public function __construct($mode = self::MODE_ALL)
    {
        $this->mode = $mode;
        $this->initialize();
    }

    /**
     *
     * @param int $mode
     *
     * @return \Chamilo\Configuration\Package\PlatformPackageBundles
     */
    public static function getInstance($mode = self::MODE_ALL)
    {
        if (!isset(self::$instance[$mode]))
        {
            self::$instance[$mode] = new self($mode);
        }

        return self::$instance[$mode];
    }

    private function getPackageBundlesCacheService()
    {
        if (!isset($this->packageBundlesCacheService))
        {
            $configurablePathBuilder = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
                ConfigurablePathBuilder::class
            );

            $this->packageBundlesCacheService = new PackageBundlesCacheService($configurablePathBuilder);
        }

        return $this->packageBundlesCacheService;
    }

    /**
     *
     * @return int
     */
    public function get_mode()
    {
        return $this->mode;
    }

    /**
     *
     * @return int[]
     */
    public static function get_modes()
    {
        return array(self::MODE_ALL, self::MODE_AVAILABLE, self::MODE_INSTALLED);
    }

    /**
     *
     * @return PackageList
     */
    public function get_package_list()
    {
        return $this->package_list;
    }

    public function get_packages()
    {
        if (!isset($this->packages))
        {
            $this->packages = $this->package_list->get_list();
        }

        return $this->packages;
    }

    public function get_packages_contexts()
    {
        return array_keys($this->get_packages());
    }

    /**
     *
     * @param boolean $recursive
     */
    public function get_type_packages()
    {
        if (!isset($this->type_packages))
        {
            $this->type_packages = $this->package_list->get_all_packages();
        }

        return $this->type_packages;
    }

    public function get_types()
    {
        if (!isset($this->types))
        {
            $this->types = array_keys($this->get_type_packages());
        }

        return $this->types;
    }

    /**
     *
     * @param boolean $include_installed
     * @param boolean $reset
     *
     * @return \Chamilo\Configuration\Package\PackageList
     */
    public function initialize()
    {
        $this->package_list = $this->getPackageBundlesCacheService()->getForIdentifier($this->mode);

        return $this->package_list;
    }

    public function reset()
    {
        $this->reset_mode($this->mode);
    }

    public function reset_all()
    {
        $this->getPackageBundlesCacheService()->clear();
    }

    public function reset_mode($mode = self::MODE_ALL)
    {
        $this->getPackageBundlesCacheService()->clearForIdentifier($this->mode);
    }
}
