<?php
namespace Chamilo\Core\Lynx\Component;

use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;

class ApplicationUpgraderComponent extends UpgraderComponent implements NoAuthenticationSupport
{

    public function initialize()
    {
        $application_registrations = DataManager :: get_registrations_by_type('application');

        foreach ($application_registrations as $application_registration)
        {
            $this->add_package($application_registration->get_context());
        }
    }

    public function upgrade_successfull()
    {
        $image = Theme :: getInstance()->getImagePath('Chamilo\Core\Lynx', 'PackageAction/Finished');
        $title = Translation :: get('PlatformUpgraded');

        $result = Translation :: get(
            'ApplicationsUpgraded',
            array('URL' => Redirect :: get_link(array(), array(self :: PARAM_CONTEXT))));

        return $this->render_upgrade_step($image, $title, $result);
    }
}
