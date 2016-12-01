<?php
namespace Chamilo\Core\Repository\ContentObject\Office365Video\Integration\Chamilo\Core\Metadata\Package;

use Chamilo\Core\Repository\ContentObject\Office365Video\Integration\Chamilo\Core\Metadata\PropertyProvider\ContentObjectPropertyProvider;

class Installer extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Action\Installer
{

    public function getPropertyProviderTypes()
    {
        return array(ContentObjectPropertyProvider::class_name());
    }
}