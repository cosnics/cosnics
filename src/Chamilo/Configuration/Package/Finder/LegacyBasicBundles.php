<?php
namespace Chamilo\Configuration\Package\Finder;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 *
 * @package Chamilo\Configuration\Package\Finder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LegacyBasicBundles extends BasicBundles
{

    /**
     *
     * @param string $folderNamespace
     *
     * @return boolean
     */
    protected function verifyPackage($folderNamespace)
    {
        $pathBuilder = new PathBuilder(ClassnameUtilities::getInstance(), ChamiloRequest::createFromGlobals());
        $packageInfoPath = $pathBuilder->namespaceToFullPath($folderNamespace) . '/package.info';

        return file_exists($packageInfoPath);
    }
}
