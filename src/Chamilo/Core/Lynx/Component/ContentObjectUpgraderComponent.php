<?php
namespace Chamilo\Core\Lynx\Component;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class ContentObjectUpgraderComponent extends UpgraderComponent implements NoAuthenticationSupport
{

    public function initialize()
    {
        $content_object_registrations = \Chamilo\Configuration\Configuration :: registrations_by_type(
            'Chamilo\Core\Repository\ContentObject');

        foreach ($content_object_registrations as $content_object_registration)
        {
            $this->add_package($content_object_registration[Registration :: PROPERTY_CONTEXT]);
        }
    }

    public function upgrade_successfull()
    {
        $image = Theme :: getInstance()->getImagePath('Chamilo\Core\Lynx', 'PackageAction/Finished');
        $title = Translation :: get('PlatformUpgraded');
        $result = Translation :: get(
            'ContentObjectsUpgraded',
            array('URL' => $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_APPLICATION_UPGRADE))));

        return $this->render_upgrade_step($image, $title, $result);
    }
}
