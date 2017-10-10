<?php
namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Configuration\Package\Finder\LegacyBasicBundles;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Console\Output\OutputInterface;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Configuration\Package\Storage\DataClass\Package;

/**
 * Processes resources from one or multiple packages
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PackageDescriber
{

    /**
     *
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    protected $pathBuilder;

    /**
     *
     * @var \Chamilo\Configuration\Package\Service\PackageFactory
     */
    protected $packageFactory;

    /**
     *
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    protected $stringUtilities;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    protected $classnameUtilities;

    /**
     *
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     * @param \Chamilo\Configuration\Package\Service\PackageFactory $packageFactory
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function __construct(PathBuilder $pathBuilder, PackageFactory $packageFactory,
        StringUtilities $stringUtilities, ClassnameUtilities $classnameUtilities)
    {
        $this->pathBuilder = $pathBuilder;
        $this->packageFactory = $packageFactory;
        $this->stringUtilities = $stringUtilities;
        $this->classnameUtilities = $classnameUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\File\PathBuilder
     */
    protected function getPathBuilder()
    {
        return $this->pathBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     */
    protected function setPathBuilder(PathBuilder $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    /**
     *
     * @return \Chamilo\Configuration\Package\Service\PackageFactory
     */
    protected function getPackageFactory()
    {
        return $this->packageFactory;
    }

    /**
     *
     * @param \Chamilo\Configuration\Package\Service\PackageFactory $packageFactory
     */
    protected function setPackageFactory(PackageFactory $packageFactory)
    {
        $this->packageFactory = $packageFactory;
    }

    /**
     *
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    protected function getStringUtilities()
    {
        return $this->stringUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    protected function setStringUtilities(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    protected function getClassnameUtilities()
    {
        return $this->classnameUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    protected function setClassnameUtilities(ClassnameUtilities $classnameUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;
    }

    /**
     * Processes the resources for all (or a given set) of packages
     *
     * @param array $packageNamespaces
     * @param OutputInterface $output
     */
    public function processPackages($packageNamespaces = array(), OutputInterface $output)
    {
        if (empty($packageNamespaces))
        {
            $packageNamespaces = $this->getDefaultNamespaces();
        }

        foreach ($packageNamespaces as $packageNamespace)
        {
            $legacyPackagePath = $this->getPackageFactory()->getLegacyPackagePath($packageNamespace);
            $packagePath = $this->getPackageFactory()->getPackagePath($packageNamespace);

            if (! file_exists($legacyPackagePath) && ! file_exists($packagePath))
            {
                Filesystem::write_to_file(
                    $packagePath,
                    json_encode(
                        $this->getComposerJsonInstanceForNamespace($packageNamespace),
                        JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

                $output->writeln('Package description created for: ' . $packageNamespace);
            }
            elseif (file_exists($legacyPackagePath) && ! file_exists($packagePath))
            {
                $composerJsonInstance = $this->getComposerJsonInstanceForNamespace($packageNamespace);
                $legacyPackage = $this->getPackageFactory()->parsePackageInfoPath($legacyPackagePath);

                $this->updateComposerJsonInstanceFromPackage($composerJsonInstance, $legacyPackage);

                Filesystem::write_to_file(
                    $packagePath,
                    json_encode($composerJsonInstance, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

                Filesystem::move_file($legacyPackagePath, $legacyPackagePath . '.backup');

                $output->writeln('Package description based on package.info created for: ' . $packageNamespace);
            }
            elseif (file_exists($legacyPackagePath) && file_exists($packagePath))
            {
                $composerJsonInstance = json_decode(file_get_contents($packagePath));
                $legacyPackage = $this->getPackageFactory()->parsePackageInfoPath($legacyPackagePath);

                $this->updateComposerJsonInstanceFromPackage($composerJsonInstance, $legacyPackage);

                Filesystem::write_to_file(
                    $packagePath,
                    json_encode($composerJsonInstance, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

                Filesystem::move_file($legacyPackagePath, $legacyPackagePath . '.backup');

                $output->writeln(
                    'Existing package description updated with package.info data for: ' . $packageNamespace);
            }
            else
            {
                $output->writeln('No package description to process for: ' . $packageNamespace);
            }
        }
    }

    /**
     *
     * @param \stdClass $composerJsonInstance
     * @param \Chamilo\Configuration\Package\Storage\DataClass\Package $legacyPackage
     */
    protected function updateComposerJsonInstanceFromPackage(\stdClass $composerJsonInstance, Package $legacyPackage)
    {
        // General properties
        $composerJsonInstance->extra->cosnics->name = $legacyPackage->get_name();
        $composerJsonInstance->extra->cosnics->context = $legacyPackage->get_context();
        $composerJsonInstance->extra->cosnics->type = $legacyPackage->get_type();

        $packageExtras = $legacyPackage->get_extra();

        if (key_exists('default-install', $packageExtras))
        {
            $composerJsonInstance->extra->cosnics->install->default = 1;
        }
        else
        {
            $composerJsonInstance->extra->cosnics->install->default = 0;
        }

        if (key_exists('core-install', $packageExtras))
        {
            $composerJsonInstance->extra->cosnics->install->core = 1;
        }
        else
        {
            $composerJsonInstance->extra->cosnics->install->core = 0;
        }

        // Authors
        $this->processAuthors($composerJsonInstance, $legacyPackage->get_authors());
    }

    /**
     *
     * @param \stdClass $composerJsonInstance
     * @param \Chamilo\Configuration\Package\Properties\Authors\Author[] $authors
     */
    protected function processAuthors($composerJsonInstance, $authors)
    {
        $composerAuthors = array();

        if (isset($composerJsonInstance->authors))
        {
            foreach ($composerJsonInstance->authors as $author)
            {
                $composerAuthors[] = $author->email;
            }
        }
        else
        {
            $composerJsonInstance->authors = array();
        }

        foreach ($authors as $author)
        {
            if (! in_array($author->get_email(), $composerAuthors))
            {
                $extraAuthor = new \stdClass();
                $extraAuthor->name = $author->get_name();
                $extraAuthor->email = $author->get_email();

                $composerJsonInstance->authors[] = $extraAuthor;
            }
        }
    }

    /**
     *
     * @return \stdClass
     */
    protected function getComposerJsonInstanceForNamespace($packageNamespace)
    {
        $composerJsonInstance = json_decode(
            '{
            "name": "",
            "version": "1.0.0",
            "description": "",
            "keywords": [],
            "homepage": "http://www.cosnics.org",
            "license": "GPLv3",
            "authors": [
            {
                "name": "Sven Vanpoucke",
                "email": "sven.vanpoucke@hogent.be"
            },
            {
                "name": "Hans De Bisschop",
                "email": "hans.de.bisschop@ehb.be"
            }
            ],
            "require": {
            "php": ">=7.0.0"
            }
        }');

        $composerJsonInstance->name = $this->getComposerName($packageNamespace);
        $composerJsonInstance->description = $this->getDescription($packageNamespace);
        $composerJsonInstance->keywords = $this->getKeywords($packageNamespace);

        return $composerJsonInstance;
    }

    /**
     *
     * @param string $context
     * @return string
     */
    protected function getComposerName($context)
    {
        return $this->getVendorName($context) . '/' . $this->getPackageName($context);
    }

    /**
     *
     * @param string $context
     * @return string
     */
    protected function getVendorName($context)
    {
        $vendorName = $this->getStringUtilities()->createString(
            $this->getClassnameUtilities()->getNamespaceParent($context, - 1))->toLowerCase()->__toString();

        if ($vendorName == 'chamilo')
        {
            return 'cosnics';
        }

        return $vendorName;
    }

    /**
     *
     * @param string $context
     * @return string
     */
    protected function getPackageName($context)
    {
        return $this->getStringUtilities()->createString($this->getClassnameUtilities()->getNamespaceChild($context, 1))->replace(
            '\\',
            '-')->dasherize()->__toString();
    }

    /**
     *
     * @param string $context
     * @return string
     */
    protected function getDescription($context)
    {
        return $this->getStringUtilities()->createString($this->getClassnameUtilities()->getNamespaceChild($context, 1))->replace(
            '\\',
            ' ')->__toString();
    }

    /**
     *
     * @param string $context
     * @return string
     */
    protected function getKeywords($context)
    {
        return explode(
            '\\',
            $this->getStringUtilities()->createString($this->getClassnameUtilities()->getNamespaceChild($context, 1))->__toString());
    }

    /**
     * Returns an array of the default namespaces
     *
     * @return string[]
     */
    protected function getDefaultNamespaces()
    {
        $resourceBundles = new LegacyBasicBundles();
        return $resourceBundles->getPackageNamespaces();
    }
}