<?php
namespace Chamilo\Core\Admin\Service;

use Chamilo\Core\Admin\Storage\DataClass\Package;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use OutOfBoundsException;
use stdClass;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @package Chamilo\Core\Admin\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PackageFactory
{
    public const string PACKAGE_DESCRIPTOR = 'composer.json';

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

        $package->setContext($cosnicsProperties->context);
        $package->setName($cosnicsProperties->name);
        $package->setType($cosnicsProperties->type);
        $package->setVersion($jsonPackageObject->version);
        $package->setResources($cosnicsProperties->resources ?? []);
        $package->setComposerJsonObject($jsonPackageObject);

        return $package;
    }

    public function parseComposerJsonPath(string $path): Package
    {
        return $this->parseComposerJson(json_decode(file_get_contents($path)));
    }
}