<?php
namespace Chamilo\Application\Lti;

use Chamilo\Application\Lti\Service\ProviderService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;

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

    const DEFAULT_ACTION = self::ACTION_MANAGE_PROVIDERS;

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

    /**
     * @return \Chamilo\Application\Lti\Storage\Entity\Provider
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function getProviderFromRequest(): \Chamilo\Application\Lti\Storage\Entity\Provider
    {
        $providerId = $this->getRequest()->getFromUrl(self::PARAM_PROVIDER_ID);

        try
        {
            $provider = $this->getProviderService()->getProviderById($providerId);
        }
        catch (\Exception $ex)
        {
            throw new ObjectNotExistException($this->getTranslator()->trans('Provider', [], Manager::context()), $providerId);
        }

        return $provider;
    }

    /**
     * @return \Chamilo\Application\Lti\Service\ProviderService
     */
    protected function getProviderService()
    {
        return $this->getService(ProviderService::class);
    }
}
