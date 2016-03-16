<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Connector;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Type\Displayer;

class DisplayerForm extends ConfigurationForm
{

    /**
     *
     * @see \Chamilo\Core\Home\Form\ConfigurationForm::addSettings()
     */
    public function addSettings()
    {
        $connector = new Connector();

        $this->addElement(
            'select',
            Displayer :: CONFIGURATION_OBJECT_ID,
            Translation :: get('UseObject'),
            $connector->getDisplayerObjects());
    }

    public function setDefaults()
    {
        $defaults = array();

        $defaults[Displayer :: CONFIGURATION_OBJECT_ID] = $this->getBlock()->getSetting(
            Displayer :: CONFIGURATION_OBJECT_ID,
            0);

        parent :: setDefaults($defaults);
    }
}