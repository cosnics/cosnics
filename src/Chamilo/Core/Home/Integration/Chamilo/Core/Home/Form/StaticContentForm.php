<?php
namespace Chamilo\Core\Home\Integration\Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Core\Home\Integration\Chamilo\Core\Home\Connector;
use Chamilo\Core\Home\Integration\Chamilo\Core\Home\Type\StaticContent;
use Chamilo\Libraries\Platform\Translation;

class StaticContentForm extends ConfigurationForm
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
            StaticContent :: CONFIGURATION_OBJECT_ID,
            Translation :: get('UseObject'),
            $connector->get_static_objects());
    }

    public function setDefaults()
    {
        $defaults = array();

        $defaults[StaticContent :: CONFIGURATION_OBJECT_ID] = $this->getBlock()->getSetting(
            StaticContent :: CONFIGURATION_OBJECT_ID,
            0);

        parent :: setDefaults($defaults);
    }
}