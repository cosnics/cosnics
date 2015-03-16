<?php
namespace Chamilo\Core\Repository\ContentObject\File\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;

/**
 * $Id: document_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.install
 */
class Installer extends ContentObjectInstaller
{

    public static function get_additional_packages()
    {
        return array('Chamilo\Core\Repository\ContentObject\File\MetadataPropertyLinker');
    }
}
