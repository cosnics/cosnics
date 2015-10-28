<?php
namespace Chamilo\Core\Home\Integration\Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Home\Integration\Chamilo\Core\Home\Connector;
use Chamilo\Core\Home\Integration\Chamilo\Core\Home\Type\Banner;

class BannerForm extends ConfigurationForm
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
            Banner :: CONFIGURATION_OBJECT_ID,
            Translation :: get('UseObject'),
            $connector->get_static_objects());
    }

    public function setDefaults()
    {
        $defaults = array();

        $defaults[Banner :: CONFIGURATION_OBJECT_ID] = $this->getBlock()->getSetting(
            Banner :: CONFIGURATION_OBJECT_ID,
            0);

        parent :: setDefaults($defaults);
    }
}