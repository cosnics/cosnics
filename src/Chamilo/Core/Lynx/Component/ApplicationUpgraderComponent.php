<?php
namespace Chamilo\Core\Lynx\Component;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class ApplicationUpgraderComponent extends UpgraderComponent implements NoAuthenticationSupport
{

    public function initialize()
    {
        $application_registrations = \Chamilo\Configuration\Configuration :: registrations_by_type(
            'Chamilo\Application');

        foreach ($application_registrations as $application_registration)
        {
            $this->add_package($application_registration[Registration :: PROPERTY_CONTEXT]);
        }
    }

    public function upgrade_successfull()
    {
        $image = Theme :: getInstance()->getImagePath('Chamilo\Core\Lynx', 'PackageAction/Finished');
        $title = Translation :: get('PlatformUpgraded');

        $redirect = new Redirect(array(self :: PARAM_CONTEXT => null));

        $result = Translation :: get('ApplicationsUpgraded', array('URL' => $redirect->getUrl()));

        return $this->render_upgrade_step($image, $title, $result);
    }
}
