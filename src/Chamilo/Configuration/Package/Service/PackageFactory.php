<?php
namespace Chamilo\Configuration\Package\Service;

use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Package\Properties\Authors\Author;
use Chamilo\Configuration\Package\Properties\Dependencies\Dependencies;
use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;

/**
 *
 * @package Chamilo\Configuration\Package\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PackageFactory
{
    const PACKAGE_DESCRIPTOR = 'composer.json';
    const LEGACY_PACKAGE_DESCRIPTOR = 'package.info';

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
     * @param \Chamilo\Libraries\Platform\Translation $translation
     */
    public function __construct(PathBuilder $pathBuilder, Translation $translation)
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
     * @param \Chamilo\Libraries\Platform\Translation $translation
     */
    public function setTranslation(Translation $translation)
    {
        $this->translation = $translation;
    }

    /**
     *
     * @param string $context
     * @return string|boolean
     */
    public function packageExists($context)
    {
        $legacyPackageExists = $this->legacyPackageExists($context);

        if ($legacyPackageExists)
        {
            return $legacyPackageExists;
        }
        else
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
    }

    /**
     *
     * @param unknown $context
     * @return string|boolean
     */
    public function legacyPackageExists($context)
    {
        $legacyPackagePath = $this->getLegacyPackagePath($context);

        if (file_exists($legacyPackagePath))
        {
            return $legacyPackagePath;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @param string $context
     * @return string
     */
    public function getPackagePath($context)
    {
        return $this->getPathBuilder()->namespaceToFullPath($context) . self::PACKAGE_DESCRIPTOR;
    }

    /**
     *
     * @param string $context
     * @return string
     * @deprecated This shouldn't be used anymore for new packages
     */
    public function getLegacyPackagePath($context)
    {
        return $this->getPathBuilder()->namespaceToFullPath($context) . self::LEGACY_PACKAGE_DESCRIPTOR;
    }

    /**
     *
     * @param string $context
     * @throws Exception
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package
     */
    public function getPackage($context)
    {
        $path = $this->packageExists($context);

        if (! $path)
        {
            throw new \Exception(Translation::get('InvalidPackageContext', array('CONTEXT' => $context)));
        }

        if (strpos($path, self::LEGACY_PACKAGE_DESCRIPTOR) === false)
        {
            return $this->parseComposerJsonPath($path);
        }
        else
        {
            return $this->parsePackageInfoPath($path);
        }
    }

    /**
     *
     * @param string $path
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package
     */
    public function parseComposerJsonPath($path)
    {
        return $this->parseComposerJson(json_decode(file_get_contents($path)));
    }

    /**
     *
     * @param \stdClass $jsonPackageObject
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package
     */
    public function parseComposerJson(\stdClass $jsonPackageObject)
    {
        $cosnicsProperties = $jsonPackageObject->extra->cosnics;

        $package = new Package();

        $package->set_context($cosnicsProperties->context);
        $package->set_name($cosnicsProperties->name);
        $package->set_type($cosnicsProperties->type);
        $package->set_category($cosnicsProperties->category);
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

    /**
     *
     * @param string $path
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package
     */
    public function parsePackageInfoPath($path)
    {
        $domDocument = new \DOMDocument('1.0', 'UTF-8');
        $domDocument->load($path);
        $domXpath = new \DOMXPath($domDocument);

        $packageNodeList = $domXpath->query('/packages/package');

        if ($packageNodeList->length > 1)
        {
            throw new \Exception(Translation::get('MultipackageFileNotAllowed', array('CONTEXT' => $context)));
        }

        return $this->parsePackageInfo($domXpath, $packageNodeList->item(0));
    }

    /**
     *
     * @param \DOMXPath $domXpath
     * @param \DOMElement $packageNode
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package
     */
    public function parsePackageInfo(\DOMXPath $domXpath, \DOMElement $packageNode)
    {
        $package = new Package();

        // Simple properties, containing a singular string or integer
        $simpleProperties = array(
            Package::PROPERTY_CONTEXT,
            Package::PROPERTY_NAME,
            Package::PROPERTY_TYPE,
            Package::PROPERTY_CATEGORY,
            Package::PROPERTY_VERSION,
            Package::PROPERTY_DESCRIPTION);

        foreach ($simpleProperties as $simpleProperty)
        {
            $node = $domXpath->query($simpleProperty, $packageNode)->item(0);

            if ($node instanceof \DOMNode && $node->hasChildNodes())
            {
                $package->set_default_property($simpleProperty, trim($node->nodeValue));
            }
            else
            {
                $package->set_default_property($simpleProperty, null);
            }
        }

        $extra = $domXpath->query('extra/*', $packageNode);

        $extras = new \stdClass();

        foreach ($extra as $extra_node)
        {
            $nodeName = $extra_node->nodeName;
            if (! (in_array($nodeName, array('core-install', 'default-install'))))
            {
                $extras->$nodeName = $extra_node->nodeValue;
            }
        }

        // Catch tools course section property
        $node = $domXpath->query('course_section', $packageNode)->item(0);
        if ($node instanceof \DOMNode && $node->hasChildNodes())
        {
            $extras->course_section = trim($node->nodeValue);
        }

        $package->set_extra($extras);

        $coreInstallNode = $domXpath->query('extra/core-install', $packageNode)->item(0);

        if ($coreInstallNode instanceof \DOMNode && $node->hasChildNodes())
        {
            $package->setCoreInstall(trim($coreInstallNode->nodeValue));
        }
        else
        {
            $package->setCoreInstall(0);
        }

        $defaultInstallNode = $domXpath->query('extra/default-install', $packageNode)->item(0);

        if ($defaultInstallNode instanceof \DOMNode && $node->hasChildNodes())
        {
            $package->setDefaultInstall(trim($defaultInstallNode->nodeValue));
        }
        else
        {
            $package->setDefaultInstall(0);
        }

        // Authors
        $author_nodes = $domXpath->query('authors/author', $packageNode);
        foreach ($author_nodes as $author_node)
        {
            $name = $domXpath->query('name', $author_node)->item(0);
            $email = $domXpath->query('email', $author_node)->item(0);

            $package->add_author(
                new Author(
                    $name instanceof \DOMNode && $name->hasChildNodes() ? $name->nodeValue : null,
                    $email instanceof \DOMNode && $email->hasChildNodes() ? $email->nodeValue : null));
        }

        // Dependencies
        $dependenciesNode = $domXpath->query('pre-depends/dependencies | pre-depends/dependency', $packageNode)->item(0);

        if (! is_null($dependenciesNode))
        {
            $dependencies = new Dependencies();
            $this->parsePackageInfoDependencies($dependencies, $domXpath, $dependenciesNode);
        }
        else
        {
            $dependencies = null;
        }

        $package->set_dependencies($dependencies);

        return $package;
    }

    /**
     *
     * @param \Chamilo\Configuration\Package\Properties\Dependencies\Dependencies $dependencies
     * @param \DOMXPath $domXpath
     * @param \DOMElement $domNode
     */
    protected function parsePackageInfoDependencies(Dependencies $dependencies, \DOMXPath $dom_xpath,
        \DOMElement $dom_node = null)
    {
        if (is_null($dom_node))
        {
            return null;
        }

        if ($dom_node->tagName == 'dependencies')
        {
            $child_nodes = $dom_xpath->query('dependencies | dependency', $dom_node);

            foreach ($child_nodes as $child_node)
            {
                $this->parsePackageInfoDependencies($dependencies, $dom_xpath, $child_node);
            }

            return $dependencies;
        }
        elseif ($dom_node->tagName == 'dependency')
        {
            if ($dom_node->getAttribute('type') == 'registration')
            {
                $dependencies->add_dependency($this->parsePackageInfoDependency($dom_xpath, $dom_node));
            }
        }
    }

    /**
     *
     * @param \DOMXPath $domXpath
     * @param \DOMElement $domNode
     * @return \Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency
     */
    protected function parsePackageInfoDependency(\DOMXPath $domXpath, \DOMElement $domNode)
    {
        $dependency = new Dependency();

        $dependency->set_id(trim($domXpath->query('id', $domNode)->item(0)->nodeValue));
        $dependency->set_version('>=1.0.0');

        return $dependency;
    }
}