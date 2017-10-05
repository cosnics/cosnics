<?php
namespace Chamilo\Configuration\Package\Storage\DataClass;

use Chamilo\Configuration\Package\Properties\Authors\Author;
use Chamilo\Configuration\Package\Properties\Dependencies\Dependencies;
use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Exception;

/**
 *
 * @author Hans De Bisschop
 */
class Package extends DataClass
{

    /**
     * Package properties
     */
    const PROPERTY_CONTEXT = 'context';
    const PROPERTY_NAME = 'name';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_AUTHORS = 'authors';
    const PROPERTY_VERSION = 'version';
    const PROPERTY_DESCRIPTION = 'description';
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
        $extended_property_names[] = self::PROPERTY_AUTHORS;
        $extended_property_names[] = self::PROPERTY_VERSION;
        $extended_property_names[] = self::PROPERTY_DESCRIPTION;
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
     */
    public static function exists($context)
    {
        $path = Path::getInstance()->namespaceToFullPath($context) . 'composer.json';

        if (file_exists($path))
        {
            return $path;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @param string $context
     * @throws Exception
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package
     */
    public static function get($context)
    {
        $path = self::exists($context);

        if (! $path)
        {
            throw new Exception(Translation::get('InvalidPackageContext', array('CONTEXT' => $context)));
        }

        return self::parse_package(json_decode(file_get_contents($path)));
    }

    /**
     *
     * @param \DOMXPath $dom_xpath
     * @param \DOMElement $package_node
     * @return \configuration\package\storage\data_class\Package
     */
    public static function parse_package(\stdClass $jsonPackageObject)
    {
        $cosnicsProperties = $jsonPackageObject->extra->cosnics;

        $package = new Package();

        $package->set_context($cosnicsProperties->context);
        $package->set_name($cosnicsProperties->name);
        $package->set_type($cosnicsProperties->type);
        $package->set_version($jsonPackageObject->version);
        $package->set_description($jsonPackageObject->description);

        if (! isset($cosnicsProperties->extra))
        {
            $extra = array();
        }
        else
        {
            $extra = $cosnicsProperties->extra;
        }

        $extra['core-install'] = $cosnicsProperties->install->core;
        $extra['default-install'] = $cosnicsProperties->install->default;

        $package->set_extra($extra);

        foreach ($jsonPackageObject->authors as $author)
        {
            $package->add_author(new Author($author->name, $author->email));
        }

        if (isset($cosnicsProperties->dependencies) && count($cosnicsProperties->dependencies) > 0)
        {
            $dependencies = new Dependencies();
            foreach ($cosnicsProperties->dependencies as $cosnicsDependency)
            {
                $dependency = new Dependency();
                $dependency->set_id($cosnicsDependency->id);
                $dependency->set_version($cosnicsDependency->version);

                $dependencies->add_dependency($dependency);
            }

            $package->set_dependencies($dependencies);
        }
        else
        {
            $package->set_dependencies(null);
        }

        return $package;
    }
}
