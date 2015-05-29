<?php
namespace Chamilo\Core\MetadataOld\Package;

use Chamilo\Core\MetadataOld\DublinCoreDefaultsInstaller;

/**
 * This installer can be used to create the storage structure for the metadata application.
 * 
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     * Extra installer functionality
     */
    public function extra()
    {
        $dublin_core_defaults_installer = new DublinCoreDefaultsInstaller($this);
        return $dublin_core_defaults_installer->install_dublin_core();
    }
}