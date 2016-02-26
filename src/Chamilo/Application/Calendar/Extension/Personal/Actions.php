<?php
namespace Chamilo\Application\Calendar\Extension\Personal;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class Actions extends \Chamilo\Application\Calendar\Actions
{

    /**
     *
     * @see \application\calendar\ActionRenderer::get()
     */
    public function get()
    {
        $buttonGroup = new ButtonGroup();

        $parameters = array();
        $parameters[Application :: PARAM_CONTEXT] = __NAMESPACE__;
        $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_CREATE;

        $redirect = new Redirect($parameters);
        $link = $redirect->getUrl();

        $buttonGroup->addButton(
            new Button(
                Translation :: get('AddEvent'),
                Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/Publish'),
                $link,
                Button :: DISPLAY_ICON));

        $parameters = array();
        $parameters[Application :: PARAM_CONTEXT] = __NAMESPACE__;
        $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_IMPORT;

        $redirect = new Redirect($parameters);
        $link = $redirect->getUrl();

        $buttonGroup->addButton(
            new Button(
                Translation :: get('ImportIcal'),
                Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/ImportIcal'),
                $link,
                Button :: DISPLAY_ICON));

        return array($buttonGroup);
    }
}