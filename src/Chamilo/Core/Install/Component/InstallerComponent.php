<?php
namespace Chamilo\Core\Install\Component;

use Chamilo\Core\Install\Manager;
use Chamilo\Core\Install\Wizard\InstallWizard;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;

/**
 * $Id: installer.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 *
 * @package install.lib.installmanager.component
 * @author Hans De Bisschop
 */

/**
 * Installer install manager component which allows the user to install the platform
 */
class InstallerComponent extends Manager implements NoAuthenticationSupport
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        BreadcrumbTrail :: get_instance()->truncate();

        if (\Chamilo\Configuration\Configuration :: available() &&
             \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'installation_blocked'))
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = Display :: error_message(Translation :: get('InstallationBlockedByAdministrator'));
            $html[] = $this->render_footer();

            return implode("\n", $html);
        }
        else
        {
            $wizard = new InstallWizard($this);
            return $wizard->run();
        }
    }
}
