<?php
namespace Chamilo\Core\Repository\Implementation\Office365\Component;

use Chamilo\Core\Repository\External\Infrastructure\Service\MicrosoftClientSettingsProvider;
use Chamilo\Core\Repository\Implementation\Office365\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class LogoutComponent extends Manager
{

    /**
     *  Delete session token created by MicrosoftClientSettingsProvider.
     */
    public function run()
    {
        $settingsProvider = 
            new MicrosoftClientSettingsProvider($this->get_external_repository(), $this->get_user(), array('https://graph.microsoft.com/Files.Read'));

        if ($settingsProvider->removeUserSetting('session_token'))
        {
            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_ACTION] = self :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->redirect(
                Translation :: get('LogoutSuccessful', null, Utilities :: COMMON_LIBRARIES),
                false,
                $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_ACTION] = self :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->redirect(Translation :: get('LogoutFailed', null, Utilities :: COMMON_LIBRARIES), true, $parameters);
        }
    }
}
