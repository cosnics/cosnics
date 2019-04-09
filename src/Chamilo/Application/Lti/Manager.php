<?php
namespace Chamilo\Application\Lti;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * @package Chamilo\Application\Plagiarism
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    const ACTION_LAUNCH = 'Launcher';
    const ACTION_BASIC_OUTCOMES = 'BasicOutcomes';
    const ACTION_RETURN = 'Return';
    const ACTION_CREATE_PROVIDER = 'CreateProvider';
    const ACTION_UPDATE_PROVIDER = 'UpdateProvider';
    const ACTION_MANAGE_PROVIDERS = 'ManageProviders';
    const ACTION_DELETE_PROVIDER = 'DeleteProvider';

    const PARAM_PROVIDER_ID = 'ProviderId';
    const PARAM_UUID = 'uuid';

    const DEFAULT_ACTION = self::ACTION_LAUNCH;

    /**
     * Manager constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        if ($this->getUser() instanceof User)
        {
            $this->checkAuthorization(Manager::context());
        }
    }
}
