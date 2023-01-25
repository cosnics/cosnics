<?php
namespace Chamilo\Core\Admin\Table;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;

/**
 * @package Ehb\Application\TimeEdit\User
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WhoIsOnlineTableRenderer extends DataClassListTableRenderer
{
    protected function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_OFFICIAL_CODE));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));

        $showEmail = Configuration::getInstance()->get_setting(['Chamilo\Core\User', 'show_email_addresses']);

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
    protected function renderCell(TableColumn $column, $user): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        switch ($column->get_name())
        {
            case User::PROPERTY_OFFICIAL_CODE :
                return $user->get_official_code();
            // Exceptions that need post-processing go here ...
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
                if ($this->get_component()->get_user()->is_platform_admin())
                {
                    $profilePhotoUrl = $urlGenerator->fromParameters(
                        [
                            Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                            Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                            Manager::PARAM_USER_USER_ID => $user->getId()
                        ]
                    );

                    return '<a href="' . $this->get_component()->get_url(['uid' => $user->getId()]) . '">' .
                        '<img style="max-width: 100px; max-height: 100px;" src="' . $profilePhotoUrl . '" alt="' .
                        $translator->trans('UserPicture', [], Manager::context()) . '" /></a>';
                }

                return '';
        }

        return parent::renderCell($column, $user);
    }
}
