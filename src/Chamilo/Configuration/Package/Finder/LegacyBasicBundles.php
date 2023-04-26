<?php
namespace Chamilo\Configuration\Package\Finder;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;

/**
 * @package Chamilo\Configuration\Package\Finder
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LegacyBasicBundles extends BasicBundles
{

    /**
     * @param string $folderNamespace
     *
     * @return bool
     */
    protected function verifyPackage($folderNamespace)
    {
        $pathBuilder = new SystemPathBuilder(ClassnameUtilities::getInstance());
        $packageInfoPath = $pathBuilder->namespaceToFullPath($folderNamespace) . '/package.info';

        return file_exists($packageInfoPath);
    }
}
