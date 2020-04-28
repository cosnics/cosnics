<?php
namespace Chamilo\Configuration\Package\Storage\DataClass;

use Chamilo\Configuration\Package\Properties\Dependencies\Dependencies;
use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use DOMElement;
use DOMXPath;
use Exception;

/**
 *
 * @package Chamilo\Configuration\Package\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Package extends DataClass
{

    /**
     * Package properties
     */
    const PROPERTY_CONTEXT = 'context';
    const PROPERTY_NAME = 'name';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_CATEGORY = 'category';
    const PROPERTY_AUTHORS = 'authors';
    const PROPERTY_VERSION = 'version';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_CORE_INSTALL = 'core_install';
    const PROPERTY_DEFAULT_INSTALL = 'default_install';
    const PROPERTY_EXTRA = 'extra';
    const PROPERTY_DEPENDENCIES = 'dependencies';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_CONTEXT;
        $extended_property_names[] = self::PROPERTY_NAME;
        $extended_property_names[] = self::PROPERTY_TYPE;
        $extended_property_names[] = self::PROPERTY_CATEGORY;
        $extended_property_names[] = self::PROPERTY_AUTHORS;
        $extended_property_names[] = self::PROPERTY_VERSION;
        $extended_property_names[] = self::PROPERTY_DESCRIPTION;
        $extended_property_names[] = self::PROPERTY_CORE_INSTALL;
        $extended_property_names[] = self::PROPERTY_DEFAULT_INSTALL;
        $extended_property_names[] = self::PROPERTY_DEPENDENCIES;
        $extended_property_names[] = self::PROPERTY_EXTRA;

        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * Returns the extra of this Package.
     *
     * @return the code.
     */
    public function get_extra()
    {
        return $this->get_default_property(self::PROPERTY_EXTRA);
    }

    /**
     * Sets the extra of this Package.
     *
     * @param extra
     */
    public function set_extra($extra)
    {
        $this->set_default_property(self::PROPERTY_EXTRA, $extra);
    }

    /**
     * Returns the context of this Package.
     *
     * @return the context.
     */
    public function get_context()
    {
        return $this->get_default_property(self::PROPERTY_CONTEXT);
    }

    /**
     * Sets the context of this Package.
     *
     * @param context
     */
    public function set_context($context)
    {
        $this->set_default_property(self::PROPERTY_CONTEXT, $context);
    }

    /**
     * Returns the name of this Package.
     *
     * @return the name.
     */
    public function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    /**
     * Sets the name of this Package.
     *
     * @param name
     */
    public function set_name($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    /**
     * Returns the type of this Package.
     *
     * @return the type.
     */
    public function get_type()
    {
        return $this->get_default_property(self::PROPERTY_TYPE);
    }

    /**
     * Sets the type of this Package.
     *
     * @param type
     */
    public function set_type($type)
    {
        $this->set_default_property(self::PROPERTY_TYPE, $type);
    }

    /**
     *
     * @return string
     */
    public function get_category()
    {
        return $this->get_default_property(self::PROPERTY_CATEGORY);
    }

    /**
     *
     * @param string $category
     */
    public function set_category($category)
    {
        $this->set_default_property(self::PROPERTY_CATEGORY, $category);
    }

    /**
     * Returns the authors of this Package.
     *
     * @return the authors.
     */
    public function get_authors()
    {
        return unserialize($this->get_default_property(self::PROPERTY_AUTHORS));
    }

    /**
     * Sets the authors of this Package.
     *
     * @param authors
     */
    public function set_authors($authors)
    {
        $this->set_default_property(self::PROPERTY_AUTHORS, serialize($authors));
    }

    public function add_author($author)
    {
        $authors = $this->get_authors();
        $authors[] = $author;

        $this->set_authors($authors);
    }

    /**
     * Returns the version of this Package.
     *
     * @return the version.
     */
    public function get_version()
    {
        return $this->get_default_property(self::PROPERTY_VERSION);
    }

    /**
     * Sets the version of this Package.
     *
     * @param version
     */
    public function set_version($version)
    {
        $this->set_default_property(self::PROPERTY_VERSION, $version);
    }

    /**
     * Returns the description of this Package.
     *
     * @return the description.
     */
    public function get_description()
    {
        return $this->get_default_property(self::PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this Package.
     *
     * @param description
     */
    public function set_description($description)
    {
        $this->set_default_property(self::PROPERTY_DESCRIPTION, $description);
    }

    /**
     *
     * @return integer
     */
    public function getCoreInstall()
    {
        return $this->get_default_property(self::PROPERTY_CORE_INSTALL);
    }

    /**
     *
     * @param integer $coreInstall
     */
    public function setCoreInstall($coreInstall)
    {
        $this->set_default_property(self::PROPERTY_CORE_INSTALL, $coreInstall);
    }

    /**
     *
     * @return integer
     */
    public function getDefaultInstall()
    {
        return $this->get_default_property(self::PROPERTY_DEFAULT_INSTALL);
    }

    /**
     *
     * @param integer $defaultInstall
     */
    public function setDefaultInstall($defaultInstall)
    {
        $this->set_default_property(self::PROPERTY_DEFAULT_INSTALL, $defaultInstall);
    }

    /**
     *
     * @return Dependencies Dependency
     */
    public function get_dependencies()
    {
        return unserialize($this->get_default_property(self::PROPERTY_DEPENDENCIES));
    }

    /**
     *
     * @param $dependencies Dependencies|Dependency
     */
    public function set_dependencies($dependencies)
    {
        $this->set_default_property(self::PROPERTY_DEPENDENCIES, serialize($dependencies));
    }

    public function has_dependencies()
    {
        return (! is_null($this->get_dependencies()));
    }

    /**
     *
     * @param string $context
     * @return boolean
     * @deprecated Use PackageFactory->packageExists($context) now
     */
    public static function exists($context)
    {
        $packageFactory = new PackageFactory(
            new PathBuilder(ClassnameUtilities::getInstance()),
            Translation::getInstance());

        return $packageFactory->packageExists($context);
    }

    /**
     *
     * @param string $context
     * @throws Exception
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package
     * @deprecated Use PackageFactory->getPackage($context) now
     */
    public static function get($context)
    {
        $packageFactory = new PackageFactory(
            new PathBuilder(ClassnameUtilities::getInstance()),
            Translation::getInstance());

        return $packageFactory->getPackage($context);
    }

    /**
     *
     * @param \DOMXPath $dom_xpath
     * @param \DOMElement $package_node
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package
     * @deprecated Use PackageFactory->parsePackageFromDom($domXpath, $packageNode) now, or better even don't use this
     *             anymore at all and use PackageFactory->getPackage($context) instead
     */
    public static function parse_package(DOMXPath $domXpath, DOMElement $packageNode)
    {
        $packageFactory = new PackageFactory(
            new PathBuilder(ClassnameUtilities::getInstance()),
            Translation::getInstance());

        return $packageFactory->parsePackageFromDom($domXpath, $packageNode);
    }
}
