<?php
namespace Chamilo\Configuration\Package\Finder;

use Chamilo\Libraries\File\Path;

/**
 *
 * @package Chamilo\Configuration\Package\Builder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ResourceBundles extends AbstractBundles
{

    /**
     *
     * @return string[]
     */
    protected function getBlacklistedFolders()
    {
        return array('.hg', 'build', 'Build', 'plugin', 'Test');
    }

    /**
     *
     * @param string $folderNamespace
     * @return boolean
     */
    protected function verifyPackage($folderNamespace)
    {
        $resourcePath = Path :: getInstance()->getResourcesPath($folderNamespace);
        return file_exists($resourcePath) && is_dir($resourcePath);
    }
}
