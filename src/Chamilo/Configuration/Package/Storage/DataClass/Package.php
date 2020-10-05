<?php
namespace Chamilo\Configuration\Package\Storage\DataClass;

use Chamilo\Configuration\Package\Properties\Dependencies\Dependencies;
use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Translation\Translation;
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

    const PROPERTY_AUTHORS = 'authors';

    const PROPERTY_CATEGORY = 'category';

    /**
     * Package properties
     */
    const PROPERTY_CONTEXT = 'context';

    const PROPERTY_CORE_INSTALL = 'core_install';

    const PROPERTY_CSS = 'css';

    const PROPERTY_DEFAULT_INSTALL = 'default_install';

    const PROPERTY_DEPENDENCIES = 'dependencies';

    const PROPERTY_DESCRIPTION = 'description';

    const PROPERTY_EXTRA = 'extra';

    const PROPERTY_NAME = 'name';

    const PROPERTY_TYPE = 'type';

    const PROPERTY_VERSION = 'version';

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
     * @deprecated Use PackageFactory->packageExists($context) now
     */
    public static function exists($context)
    {
        $packageFactory = new PackageFactory(
            new PathBuilder(ClassnameUtilities::getInstance()), Translation::getInstance()
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
            new PathBuilder(ClassnameUtilities::getInstance()), Translation::getInstance()
        );

        return $packageFactory->getPackage($context);
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
     * Returns the extra of this Package.
     *
     * @return string[][]
     */
    public function getCss()
    {
        return unserialize($this->getDefaultProperty(self::PROPERTY_CSS));
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
     * Returns the authors of this Package.
     *
     * @return the authors.
     */
    public function get_authors()
    {
        return unserialize($this->get_default_property(self::PROPERTY_AUTHORS));
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
     * Returns the context of this Package.
     *
     * @return the context.
     */
    public function get_context()
    {
        return $this->get_default_property(self::PROPERTY_CONTEXT);
    }

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
        $extended_property_names[] = self::PROPERTY_CSS;

        return parent::get_default_property_names($extended_property_names);
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
     * Returns the description of this Package.
     *
     * @return the description.
     */
    public function get_description()
    {
        return $this->get_default_property(self::PROPERTY_DESCRIPTION);
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
     * Returns the name of this Package.
     *
     * @return the name.
     */
    public function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
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
     * Returns the version of this Package.
     *
     * @return the version.
     */
    public function get_version()
    {
        return $this->get_default_property(self::PROPERTY_VERSION);
    }

    public function has_dependencies()
    {
        return (!is_null($this->get_dependencies()));
    }

    /**
     *
     * @param \DOMXPath $dom_xpath
     * @param \DOMElement $package_node
     *
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package
     * @deprecated Use PackageFactory->parsePackageFromDom($domXpath, $packageNode) now, or better even don't use this
     *             anymore at all and use PackageFactory->getPackage($context) instead
     */
    public static function parse_package(DOMXPath $domXpath, DOMElement $packageNode)
    {
        $packageFactory = new PackageFactory(
            new PathBuilder(ClassnameUtilities::getInstance()), Translation::getInstance()
        );

        return $packageFactory->parsePackageFromDom($domXpath, $packageNode);
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
     * Sets the extra of this Package.
     *
     * @param string[][] $css
     *
     * @throws \Exception
     */
    public function setCss($css)
    {
        $this->setDefaultProperty(self::PROPERTY_CSS, serialize($css));
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
     * Sets the authors of this Package.
     *
     * @param authors
     */
    public function set_authors($authors)
    {
        $this->set_default_property(self::PROPERTY_AUTHORS, serialize($authors));
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
     * Sets the context of this Package.
     *
     * @param context
     */
    public function set_context($context)
    {
        $this->set_default_property(self::PROPERTY_CONTEXT, $context);
    }

    /**
     *
     * @param $dependencies Dependencies|Dependency
     */
    public function set_dependencies($dependencies)
    {
        $this->set_default_property(self::PROPERTY_DEPENDENCIES, serialize($dependencies));
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
     * Sets the extra of this Package.
     *
     * @param extra
     */
    public function set_extra($extra)
    {
        $this->set_default_property(self::PROPERTY_EXTRA, $extra);
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
     * Sets the type of this Package.
     *
     * @param type
     */
    public function set_type($type)
    {
        $this->set_default_property(self::PROPERTY_TYPE, $type);
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
}
