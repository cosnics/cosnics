<?php
namespace Chamilo\Application\Plagiarism;

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
    const ACTION_TURNITIN_WEBHOOK = 'TurnitinWebhook';
    const ACTION_TURNITIN_EULA = 'TurnitinEula';
    const ACTION_TURNITIN_MANAGE_WEBHOOK = 'TurnitinWebhookManager';

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
