<?php
namespace Chamilo\Core\Admin\UserInterface\Table;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Admin\UserInterface\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class OnlineTableRenderer extends DataClassListTableRenderer
{
    protected ConfigurationConsulter $configurationConsulter;

    protected User $user;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, User $user, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory, ClassnameUtilities $classnameUtilities
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->user = $user;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory,
            $classnameUtilities
        );
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    protected function initializeColumns(): void
    {
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_OFFICIAL_CODE)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_SURNAME)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_GIVEN_NAME)
        );

        $showEmail = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\User', 'show_email_addresses']);

        if ($showEmail)
        {
            $this->addColumn(
                $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_EMAIL)
            );
        }

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_STATUS)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_PICTURE_URI)
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $result
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, mixed $result): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        switch ($column->getName())
        {
            case User::PROPERTY_STATUS :
                if ($result->getPlatformAdmin() == '1')
                {
                    return $translator->trans('PlatformAdministrator', [], Manager::CONTEXT);
                }
                if ($result->getStatus() == '1')
                {
                    return $translator->trans('CourseAdmin', [], Manager::CONTEXT);
                }
                else
                {
                    return $translator->trans('Student', [], Manager::CONTEXT);
                }
            case User::PROPERTY_PLATFORM_ADMINISTRATOR :
                if ($result->getPlatformAdmin() == '1')
                {
                    return $translator->trans('PlatformAdministrator', [], Manager::CONTEXT);
                }
                else
                {
                    return '';
                }
            case User::PROPERTY_PICTURE_URI :
                if ($this->getUser()->isPlatformAdministrator())
                {
                    $profilePhotoUrl = $urlGenerator->fromParameters(
                        [
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            Application::PARAM_ACTION => Manager::ACTION_USER_PICTURE,
                            Manager::PARAM_USER_ID => $result->getId()
                        ]
                    );

                    $profileUrl = $this->getUrlGenerator()->fromParameters([
                        Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::CONTEXT,
                        Application::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_ONLINE,
                        \Chamilo\Core\Admin\Manager::PARAM_USER_ID => $result->getId()
                    ]);

                    return '<a href="' . $profileUrl . '">' .
                        '<img style="max-width: 100px; max-height: 100px;" src="' . $profilePhotoUrl . '" alt="' .
                        $translator->trans('UserPicture', [], Manager::CONTEXT) . '" /></a>';
                }

                return '';
        }

        return parent::renderCell($column, $resultPosition, $result);
    }
}
