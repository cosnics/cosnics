<?php
namespace Chamilo\Configuration\Package\Storage\DataClass;

use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 *
 * @package Chamilo\Configuration\Package\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Package extends DataClass
{

    const PROPERTY_ADDITIONAL = 'additional';
    const PROPERTY_AUTHORS = 'authors';
    const PROPERTY_CATEGORY = 'category';
    const PROPERTY_CONTEXT = 'context';
    const PROPERTY_CORE_INSTALL = 'core_install';
    const PROPERTY_DEFAULT_INSTALL = 'default_install';
    const PROPERTY_DEPENDENCIES = 'dependencies';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_EXTRA = 'extra';
    const PROPERTY_NAME = 'name';
    const PROPERTY_RESOURCES = 'resources';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_VERSION = 'version';

    /**
     * @param \stdClass $author
     *
     * @throws \Exception
     */
    public function add_author($author)
    {
        $authors = $this->get_authors();
        $authors[] = $author;

        $this->set_authors($authors);
    }

    /**
     *
     * @param string $context
     *
     * @return boolean
     * @throws \Exception
     * @deprecated Use PackageFactory->packageExists($context) now
     */
    public static function exists($context)
    {
        $packageFactory = new PackageFactory(
            new PathBuilder(ClassnameUtilities::getInstance(), ChamiloRequest::createFromGlobals()), Translation::getInstance()
        );

        return $packageFactory->packageExists($context);
    }

    /**
     *
     * @param string $context
     *
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package
     * @throws Exception
     * @deprecated Use PackageFactory->getPackage($context) now
     */
    public static function get($context)
    {
        $packageFactory = new PackageFactory(
            new PathBuilder(ClassnameUtilities::getInstance(), ChamiloRequest::createFromGlobals()), Translation::getInstance()
        );

        return $packageFactory->getPackage($context);
    }

    /**
     *
     * @return string[]
     */
    public function getAdditional()
    {
        return unserialize($this->getDefaultProperty(self::PROPERTY_ADDITIONAL));
    }

    /**
     *
     * @return integer
     */
    public function getCoreInstall()
    {
        return $this->getDefaultProperty(self::PROPERTY_CORE_INSTALL);
    }

    /**
     *
     * @return integer
     */
    public function getDefaultInstall()
    {
        return $this->getDefaultProperty(self::PROPERTY_DEFAULT_INSTALL);
    }

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return mixed
     */
    public static function getDefaultProperty_names($extendedPropertyNames = [])
    {
        $extendedPropertyNames[] = self::PROPERTY_CONTEXT;
        $extendedPropertyNames[] = self::PROPERTY_NAME;
        $extendedPropertyNames[] = self::PROPERTY_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_CATEGORY;
        $extendedPropertyNames[] = self::PROPERTY_AUTHORS;
        $extendedPropertyNames[] = self::PROPERTY_VERSION;
        $extendedPropertyNames[] = self::PROPERTY_DESCRIPTION;
        $extendedPropertyNames[] = self::PROPERTY_CORE_INSTALL;
        $extendedPropertyNames[] = self::PROPERTY_DEFAULT_INSTALL;
        $extendedPropertyNames[] = self::PROPERTY_DEPENDENCIES;
        $extendedPropertyNames[] = self::PROPERTY_EXTRA;
        $extendedPropertyNames[] = self::PROPERTY_RESOURCES;
        $extendedPropertyNames[] = self::PROPERTY_ADDITIONAL;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * Returns the dependencies for this dataclass
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition[] $dependencies
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     * @throws \Exception
     */
    public function getDependencies($dependencies = []): array
    {
        return unserialize($this->getDefaultProperty(self::PROPERTY_DEPENDENCIES));
    }

    /**
     * Returns the extra of this Package.
     *
     * @return \stdClass[]
     */
    public function getResources()
    {
        return unserialize($this->getDefaultProperty(self::PROPERTY_RESOURCES));
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'configuration_package';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
    }

    /**
     * @return \stdClass[]
     */
    public function get_authors()
    {
        return unserialize($this->getDefaultProperty(self::PROPERTY_AUTHORS));
    }

    /**
     *
     * @return string
     */
    public function get_category()
    {
        return $this->getDefaultProperty(self::PROPERTY_CATEGORY);
    }

    /**
     * @return string
     */
    public function get_context()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTEXT);
    }

    /**
     * @return string
     */
    public function get_description()
    {
        return $this->getDefaultProperty(self::PROPERTY_DESCRIPTION);
    }

    /**
     * @return string[]
     */
    public function get_extra()
    {
        return $this->getDefaultProperty(self::PROPERTY_EXTRA);
    }

    /**
     * @return string
     */
    public function get_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    /**
     * @deprecated Use Package::getType() now
     */
    public function get_type()
    {
        return $this->getType();
    }

    /**
     * @return string
     */
    public function get_version()
    {
        return $this->getDefaultProperty(self::PROPERTY_VERSION);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function has_dependencies()
    {
        return (!is_null($this->getDependencies()));
    }

    /**
     *
     * @param string[] $additional
     *
     * @throws \Exception
     */
    public function setAdditional($additional)
    {
        $this->setDefaultProperty(self::PROPERTY_ADDITIONAL, serialize($additional));
    }

    /**
     * @param integer $coreInstall
     *
     * @throws \Exception
     */
    public function setCoreInstall($coreInstall)
    {
        $this->setDefaultProperty(self::PROPERTY_CORE_INSTALL, $coreInstall);
    }

    /**
     * @param integer $defaultInstall
     *
     * @throws \Exception
     */
    public function setDefaultInstall($defaultInstall)
    {
        $this->setDefaultProperty(self::PROPERTY_DEFAULT_INSTALL, $defaultInstall);
    }

    /**
     * Sets the extra of this Package.
     *
     * @param string[][] $resources
     *
     * @throws \Exception
     */
    public function setResources($resources)
    {
        $this->setDefaultProperty(self::PROPERTY_RESOURCES, serialize($resources));
    }

    /**
     * @param string $type
     *
     * @throws \Exception
     */
    public function setType($type)
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);
    }

    /**
     * @param \stdClass[] $authors
     *
     * @throws \Exception
     */
    public function set_authors($authors)
    {
        $this->setDefaultProperty(self::PROPERTY_AUTHORS, serialize($authors));
    }

    /**
     * @param string $category
     *
     * @throws \Exception
     */
    public function set_category($category)
    {
        $this->setDefaultProperty(self::PROPERTY_CATEGORY, $category);
    }

    /**
     * @param string $context
     *
     * @throws \Exception
     */
    public function set_context($context)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTEXT, $context);
    }

    /**
     * @param $dependencies
     *
     * @throws \Exception
     */
    public function set_dependencies($dependencies)
    {
        $this->setDefaultProperty(self::PROPERTY_DEPENDENCIES, serialize($dependencies));
    }

    /**
     * @param string $description
     *
     * @throws \Exception
     */
    public function set_description($description)
    {
        $this->setDefaultProperty(self::PROPERTY_DESCRIPTION, $description);
    }

    /**
     * @param string[] $extra
     *
     * @throws \Exception
     */
    public function set_extra($extra)
    {
        $this->setDefaultProperty(self::PROPERTY_EXTRA, $extra);
    }

    /**
     * @param string $name
     *
     * @throws \Exception
     */
    public function set_name($name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }

    /**
     * @deprecated Use Package::setType() now
     */
    public function set_type($type)
    {
        $this->setType($type);
    }

    /**
     * @param string $version
     *
     * @throws \Exception
     */
    public function set_version($version)
    {
        $this->setDefaultProperty(self::PROPERTY_VERSION, $version);
    }
}
