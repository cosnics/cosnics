<?php
namespace Chamilo\Configuration\Package\Service;

use Chamilo\Configuration\Package\Properties\Authors\Author;
use Chamilo\Configuration\Package\Properties\Dependencies\Dependencies;
use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Libraries\File\SystemPathBuilder;
use OutOfBoundsException;
use stdClass;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @package Chamilo\Configuration\Package\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PackageFactory
{
    public const PACKAGE_DESCRIPTOR = 'composer.json';

    protected Filesystem $filesystem;

    private SystemPathBuilder $systemPathBuilder;

    public function __construct(SystemPathBuilder $systemPathBuilder, Filesystem $filesystem)
    {
        $this->systemPathBuilder = $systemPathBuilder;
        $this->filesystem = $filesystem;
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * @param string $context
     *
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package
     * @throws \OutOfBoundsException
     */
    public function getPackage(string $context): Package
    {
        if (!$this->packageExists($context))
        {
            throw new OutOfBoundsException('Invalid package context: ' . $context);
        }

        return $this->parseComposerJsonPath($this->getPackagePath($context));
    }

    public function getPackagePath(string $context): string
    {
        return $this->getSystemPathBuilder()->namespaceToFullPath($context) . self::PACKAGE_DESCRIPTOR;
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    public function packageExists(string $context): bool
    {
        return $this->getFilesystem()->exists($this->getPackagePath($context));
    }

    public function parseComposerJson(stdClass $jsonPackageObject): Package
    {
        $cosnicsProperties = $jsonPackageObject->extra->cosnics;

        $package = new Package();

        $package->set_context($cosnicsProperties->context);
        $package->set_name($cosnicsProperties->name);
        $package->setType($cosnicsProperties->type);
        $package->set_category($cosnicsProperties->category);
        $package->set_version($jsonPackageObject->version);
        $package->set_description($jsonPackageObject->description);

        if (!isset($cosnicsProperties->extra))
        {
            $extra = [];
        }
        else
        {
            $extra = $cosnicsProperties->extra;
        }

        $package->setResources($cosnicsProperties->resources);

        $package->set_extra($extra);

        $package->setCoreInstall($cosnicsProperties->install->core ?: 0);
        $package->setDefaultInstall($cosnicsProperties->install->default ?: 0);

        foreach ($jsonPackageObject->authors as $author)
        {
            $package->add_author(new Author($author->name, $author->email));
        }

        if (isset($cosnicsProperties->dependencies) && count($cosnicsProperties->dependencies) > 0)
        {
            $dependencies = new Dependencies();

            foreach ($cosnicsProperties->dependencies as $cosnicsDependency)
            {
                $dependencies->add_dependency(new Dependency($cosnicsDependency->id, $cosnicsDependency->version));
            }

            $package->set_dependencies($dependencies);
        }
        else
        {
            $package->set_dependencies(null);
        }

        return $package;
    }

    public function parseComposerJsonPath(string $path): Package
    {
        return $this->parseComposerJson(json_decode(file_get_contents($path)));
    }
}