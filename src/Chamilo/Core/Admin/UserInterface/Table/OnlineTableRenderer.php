<?php
namespace Chamilo\Core\Admin\UserInterface\Table;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableResultPosition;
use Chamilo\Libraries\UserInterface\Table\Factory\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\UserInterface\Table\Service\DataClassListTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\ListHtmlTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\PageNavigationCalculator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Admin\UserInterface\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class OnlineTableRenderer extends DataClassListTableRenderer
{
    protected User $user;

    public function __construct(
        User $user, Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer,
        PageNavigationCalculator $pager, DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory,
        ClassnameUtilities $classnameUtilities
    )
    {
        $this->user = $user;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory,
            $classnameUtilities
        );
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

        switch ($column->getName()) {
            case User::PROPERTY_PLATFORM_ADMINISTRATOR :
                if ($result->getPlatformAdmin() == '1') {
                    return $translator->trans('PlatformAdministrator', [], Manager::CONTEXT);
                }
                else {
                    return '';
                }
            case User::PROPERTY_PICTURE_URI :
                if ($this->getUser()->isPlatformAdministrator()) {
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
