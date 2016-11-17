<?php
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Configuration\Package\PlatformPackageBundles;
require __DIR__ . '/../../Architecture/Bootstrap.php';

Chamilo\Libraries\Architecture\Bootstrap::getInstance();

$packageList = PlatformPackageBundles::getInstance()->get_package_list()->get_all_packages();
$availablePackages = $packageList['Chamilo\Core\Repository\ContentObject'];

$classNameUtilities = ClassnameUtilities::getInstance();
$pathUtilities = Path::getInstance();

foreach ($availablePackages as $availablePackage)
{
    $contentObjectPackage = $availablePackage->get_context();
    $contentObjectName = $classNameUtilities->getPackageNameFromNamespace($contentObjectPackage);
    $contentObjectClass = $contentObjectPackage . '\Storage\DataClass\\' . $contentObjectName;
    
    if ($contentObjectName == 'File')
    {
        continue;
    }
    
    $contentObjectPackagePath = $pathUtilities->namespaceToFullPath($contentObjectPackage);
    
    // Root integration package
    $contentObjectIntegrationNamespace = $contentObjectPackage . '\Integration\Chamilo\Core\Metadata';
    $contentObjectIntegrationPath = $pathUtilities->namespaceToFullPath($contentObjectIntegrationNamespace);
    Filesystem::create_dir($contentObjectIntegrationPath);
    
    // Entity
    $entityNamespace = $contentObjectIntegrationNamespace . '\\Entity';
    $entityPath = $pathUtilities->namespaceToFullPath($entityNamespace);
    Filesystem::create_dir($entityPath);
    $entityFilePath = $entityPath . $contentObjectName . 'Entity.php';
    
    $entityFileContent = <<<EOT
<?php
namespace $entityNamespace;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Entity\ContentObjectEntity;

/**
 *
 * @package $entityNamespace
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class $contentObjectName.Entity extends ContentObjectEntity
{
}
EOT;
    
    Filesystem::write_to_file($entityFilePath, $entityFileContent);
    // var_dump($entityFileContent);
    
    // Package
    $packageNamespace = $contentObjectIntegrationNamespace . '\Package';
    $packagePath = $pathUtilities->namespaceToFullPath($packageNamespace);
    Filesystem::create_dir($packagePath);
    $packageInstallerPath = $packagePath . 'Installer.php';
    
    $packageInstallerContent = <<<EOT
<?php
namespace $packageNamespace;

use $contentObjectIntegrationNamespace\PropertyProvider\ContentObjectPropertyProvider;

/**
 *
 * @package $packageNamespace
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Action\Installer
{

    public function getPropertyProviderTypes()
    {
        return array(ContentObjectPropertyProvider :: class_name());
    }
}
EOT;
    
    Filesystem::write_to_file($packageInstallerPath, $packageInstallerContent);
    // var_dump($packageInstallerContent);
    
    // PropertyProvider
    $propertyProviderNamespace = $contentObjectIntegrationNamespace . '\PropertyProvider';
    $propertyProviderPath = $pathUtilities->namespaceToFullPath($propertyProviderNamespace);
    Filesystem::create_dir($propertyProviderPath);
    $propertyProvideFilePath = $propertyProviderPath . 'ContentObjectPropertyProvider.php';
    
    $propertyProvideFileContent = <<<EOT
<?php
namespace $propertyProviderNamespace;

use $contentObjectClass;

/**
 *
 * @package $propertyProviderNamespace
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ContentObjectPropertyProvider extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\PropertyProvider\ContentObjectPropertyProvider
{
    /**
     *
     * @see \Chamilo\Core\Metadata\Provider\PropertyProviderInterface::getEntityType()
     */
    public function getEntityType()
    {
        return $contentObjectName :: class_name();
    }
}
EOT;
    
    Filesystem::write_to_file($propertyProvideFilePath, $propertyProvideFileContent);
    // var_dump($propertyProvideFileContent);
    
    // Package.info
    $packageInfoPath = $contentObjectIntegrationPath . 'package.info';
    
    $packageInfoContent = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<packages>
	<package>
		<name>Metadata</name>
		<code>Metadata</code>
		<context>$contentObjectIntegrationNamespace</context>
		<type>$contentObjectPackage\Integration</type>
		<category />
		<authors />
		<version>5.0.0</version>
		<description />
		<pre-depends>
			<dependencies operator="1">
				<dependency type="registration">
					<id>$contentObjectPackage</id>
					<version operator="4">5.0.0</version>
				</dependency>
				<dependency type="registration">
					<id>Chamilo\Core\Metadata</id>
					<version operator="4">5.0.0</version>
				</dependency>
			</dependencies>
		</pre-depends>
	</package>
</packages>
EOT;
    
    Filesystem::write_to_file($packageInfoPath, $packageInfoContent);
    // var_dump($packageInfoContent);
}