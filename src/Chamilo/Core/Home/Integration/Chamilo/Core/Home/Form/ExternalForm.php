<?php
namespace Chamilo\Core\Home\Integration\Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Home\Integration\Chamilo\Core\Home\Connector;
use Chamilo\Core\Home\Integration\Chamilo\Core\Home\Type\External;

class ExternalForm extends ConfigurationForm
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
            External :: CONFIGURATION_OBJECT_ID,
            Translation :: get('UseObject'),
            $connector->get_external_objects());

        $scrollOptions = array();
        $scrollOptions['no'] = Translation :: get('No');
        $scrollOptions['yes'] = Translation :: get('Yes');
        $scrollOptions['auto'] = Translation :: get('Auto');

        $this->addElement(
            'select',
            External :: CONFIGURATION_SCROLLING,
            Translation :: get('Scrolling'),
            $scrollOptions);

        $this->add_textfield(External :: CONFIGURATION_HEIGHT, Translation :: get('Height'), true);
    }

    public function setDefaults()
    {
        $defaults = array();

        $defaults[External :: CONFIGURATION_OBJECT_ID] = $this->getBlock()->getSetting(
            External :: CONFIGURATION_OBJECT_ID,
            0);
        $defaults[External :: CONFIGURATION_SCROLLING] = $this->getBlock()->getSetting(
            External :: CONFIGURATION_SCROLLING,
            'no');
        $defaults[External :: CONFIGURATION_HEIGHT] = $this->getBlock()->getSetting(
            External :: CONFIGURATION_HEIGHT,
            300);

        parent :: setDefaults($defaults);
    }
}