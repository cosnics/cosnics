<?php
namespace Chamilo\Application\Calendar\Extension\Personal;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Structure\ActionBar\BootstrapGlyph;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Actions implements \Chamilo\Application\Calendar\ActionsInterface
{

    public function get(Application $application)
    {
        $parameters = array();
        $parameters[Application :: PARAM_CONTEXT] = __NAMESPACE__;
        $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_CREATE;

        $redirect = new Redirect($parameters);
        $link = $redirect->getUrl();

        $dropdownButton = new SplitDropdownButton(Translation :: get('AddEvent'), new BootstrapGlyph('plus'), $link);
        $dropdownButton->setDropdownClasses('dropdown-menu-right');

        $parameters = array();
        $parameters[Application :: PARAM_CONTEXT] = __NAMESPACE__;
        $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_IMPORT;

        $redirect = new Redirect($parameters);
        $link = $redirect->getUrl();

        $dropdownButton->addSubButton(new SubButton(Translation :: get('ImportIcal'), new BootstrapGlyph('import'), $link));

        return array($dropdownButton);
    }
}