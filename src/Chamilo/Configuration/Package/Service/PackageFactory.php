<?php
namespace Chamilo\Configuration\Package\Service;

use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Package\Properties\Authors\Author;
use Chamilo\Configuration\Package\Properties\Dependencies\Dependencies;
use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;
use Exception;
use stdClass;

/**
 *
 * @package Chamilo\Configuration\Package\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PackageFactory
{
    const PACKAGE_DESCRIPTOR = 'composer.json';

    /**
     *
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    private $pathBuilder;

    /**
     *
     * @var \Chamilo\Libraries\Platform\Translation
     */
    private $translation;

    /**
     *
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     * @param \Chamilo\Libraries\Translation\Translation $translation
     */
    public function __construct(PathBuilder $pathBuilder, Translation $translation = null)
    {
        $this->pathBuilder = $pathBuilder;
        $this->translation = $translation;
    }

    /**
     *
     * @return \Chamilo\Libraries\File\PathBuilder
     */
    public function getPathBuilder()
    {
        return $this->pathBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     */
    public function setPathBuilder(PathBuilder $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Translation
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     *
     * @param \Chamilo\Libraries\Translation\Translation $translation
     */
    public function setTranslation(Translation $translation)
    {
        $this->translation = $translation;
    }

    /**
     *
     * @param string $context
     *
     * @return string|boolean
     */
    public function packageExists($context)
    {
        $packagePath = $this->getPackagePath($context);

        if (file_exists($packagePath))
        {
            return $packagePath;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @param string $context
     *
     * @return string
     */
    public function getPackagePath($context)
    {
        return $this->getPathBuilder()->namespaceToFullPath($context) . self::PACKAGE_DESCRIPTOR;
    }

    /**
     *
     * @param string $context
     *
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package
     * @throws \Exception
     */
    public function getPackage($context)
    {
        $path = $this->packageExists($context);

        if (!$path)
        {
            throw new Exception(Translation::get('InvalidPackageContext', array('CONTEXT' => $context)));
        }

        return $this->parseComposerJsonPath($path);
    }

    /**
     *
     * @param string $path
     *
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package
     */
    public function parseComposerJsonPath($path)
    {
        return $this->parseComposerJson(json_decode(file_get_contents($path)));
    }

    /**
     *
     * @param \stdClass $jsonPackageObject
     *
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package
     */
    public function parseComposerJson(stdClass $jsonPackageObject)
    {
        $cosnicsProperties = $jsonPackageObject->extra->cosnics;

        $package = new Package();

        $package->set_context($cosnicsProperties->context);
        $package->set_name($cosnicsProperties->name);
        $package->set_type($cosnicsProperties->type);
        $package->set_category($cosnicsProperties->category);
        $package->set_version($jsonPackageObject->version);
        $package->set_description($jsonPackageObject->description);

        if (!isset($cosnicsProperties->extra))
        {
            $extra = array();
        }
        else
        {
            $extra = $cosnicsProperties->extra;
        }

        $package->set_extra($extra);

        $package->setCoreInstall($cosnicsProperties->install->core ? $cosnicsProperties->install->core : 0);
        $package->setDefaultInstall($cosnicsProperties->install->default ? $cosnicsProperties->install->default : 0);

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