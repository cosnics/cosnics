<?php
namespace Chamilo\Core\Install\Component;

use Chamilo\Core\Install\Manager;
use Chamilo\Core\Install\Wizard\InstallWizard;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Install\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
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

            return implode(PHP_EOL, $html);
        }
        else
        {
            $wizard = new InstallWizard($this);
            return $wizard->run();
        }
    }
}
