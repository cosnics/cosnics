<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Form;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type\WeblcmsBookmarkDisplay;
use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Libraries\Platform\Translation;

class WeblcmsBookmarkDisplayForm extends ConfigurationForm
{

    /**
     *
     * @see \Chamilo\Core\Home\Form\ConfigurationForm::addSettings()
     */
    public function addSettings()
    {
        $this->addElement(
            'checkbox',
            WeblcmsBookmarkDisplay :: CONFIGURATION_SHOW_EMPTY,
            Translation :: get('ShowWhenEmpty'));
    }

    public function setDefaults()
    {
        $defaults = array();

        $defaults[WeblcmsBookmarkDisplay :: CONFIGURATION_SHOW_EMPTY] = $this->getBlock()->getSetting(
            WeblcmsBookmarkDisplay :: CONFIGURATION_SHOW_EMPTY,
            true);

        parent :: setDefaults($defaults);
    }
}