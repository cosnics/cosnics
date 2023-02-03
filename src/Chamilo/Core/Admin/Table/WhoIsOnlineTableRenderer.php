<?php
namespace Chamilo\Core\Admin\Table;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Symfony\Component\Translation\Translator;

/**
 * @package Ehb\Application\TimeEdit\User
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WhoIsOnlineTableRenderer extends DataClassListTableRenderer
{
    protected ConfigurationConsulter $configurationConsulter;

    protected User $user;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, User $user, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->user = $user;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    protected function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_OFFICIAL_CODE));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));

        $showEmail = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\User', 'show_email_addresses']);

        if ($showEmail)
        {
            $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_EMAIL));
        }

        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_STATUS));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_PICTURE_URI));
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \ReflectionException
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $user): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        switch ($column->get_name())
        {
            case User::PROPERTY_STATUS :
                if ($user->get_platformadmin() == '1')
                {
                    return $translator->trans('PlatformAdministrator', [], Manager::context());
                }
                if ($user->get_status() == '1')
                {
                    return $translator->trans('CourseAdmin', [], Manager::context());
                }
                else
                {
                    return $translator->trans('Student', [], Manager::context());
                }
            case User::PROPERTY_PLATFORMADMIN :
                if ($user->get_platformadmin() == '1')
                {
                    return $translator->trans('PlatformAdministrator', [], Manager::context());
                }
                else
                {
                    return '';
                }
            case User::PROPERTY_PICTURE_URI :
                if ($this->getUser()->is_platform_admin())
                {
                    $profilePhotoUrl = $urlGenerator->fromParameters(
                        [
                            Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                            Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                            Manager::PARAM_USER_USER_ID => $user->getId()
                        ]
                    );

                    $profileUrl = $this->getUrlGenerator()->fromParameters([
                        Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                        Application::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_WHOIS_ONLINE,
                        \Chamilo\Core\Admin\Manager::PARAM_USER_ID => $user->getId()
                    ]);

                    return '<a href="' . $profileUrl . '">' .
                        '<img style="max-width: 100px; max-height: 100px;" src="' . $profilePhotoUrl . '" alt="' .
                        $translator->trans('UserPicture', [], Manager::context()) . '" /></a>';
                }

                return '';
        }

        return parent::renderCell($column, $resultPosition, $user);
    }
}
