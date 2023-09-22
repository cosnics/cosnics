<?php
namespace Chamilo\Core\Lynx;

use Chamilo\Configuration\Package\Action\PackageActionFactory;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * @package Chamilo\Core\Lynx
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_ACTIVATE = 'Activator';
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_DEACTIVATE = 'Deactivator';
    public const ACTION_INSTALL = 'Installer';
    public const ACTION_REMOVE = 'Remover';
    public const ACTION_VIEW = 'Viewer';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public const PARAM_ACTION = 'manager_action';
    public const PARAM_ACTIVATE_SELECTED = 'activate';
    public const PARAM_CONTEXT = 'context';
    public const PARAM_DEACTIVATE_SELECTED = 'deactivate';
    public const PARAM_INSTALL_SELECTED = 'install';
    public const PARAM_INSTALL_TYPE = 'type';
    public const PARAM_PACKAGE = 'package';
    public const PARAM_REGISTRATION = 'registration';
    public const PARAM_REGISTRATION_TYPE = 'type';
    public const PARAM_SECTION = 'section';

    protected ?string $currentContext;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->checkAuthorization(Manager::CONTEXT);
    }

    public function getCurrentContext(): string
    {
        return $this->getRequest()->query->get(self::PARAM_CONTEXT);
    }

    public function getPackageActionFactory(): PackageActionFactory
    {
        return $this->getService(PackageActionFactory::class);
    }
}