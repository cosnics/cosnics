<?php
namespace Chamilo\Core\Repository\ContentObject\Link\Integration\Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\ContentObject\Link\Integration\Chamilo\Core\Home\Connector;
use Chamilo\Core\Repository\ContentObject\Link\Integration\Chamilo\Core\Home\Type\Linker;

class LinkerForm extends ConfigurationForm
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
            Linker :: CONFIGURATION_OBJECT_ID,
            Translation :: get('UseObject'),
            $connector->get_link_objects());
    }

    public function setDefaults()
    {
        $defaults = array();

        $defaults[Linker :: CONFIGURATION_OBJECT_ID] = $this->getBlock()->getSetting(
            Linker :: CONFIGURATION_OBJECT_ID,
            0);

        parent :: setDefaults($defaults);
    }
}