<?php
namespace Chamilo\Core\Admin\UserInterface\Table;

use Chamilo\Core\Admin\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
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
    protected ?User $currentUser;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer,
        PageNavigationCalculator $pager, DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory,
        ClassnameUtilities $classnameUtilities, ?User $currentUser = null
    )
    {
        $this->currentUser = $currentUser;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory,
            $classnameUtilities
        );
    }

    public function getCurrentUser(): ?User
    {
        return $this->currentUser;
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
                $user = $this->getCurrentUser();
                if ($user instanceof User && $user->isPlatformAdministrator()) {
                    $profilePhotoUrl = $urlGenerator->fromParameters(
                        [
                            ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                            ApplicationInterface::PARAM_ACTION => \Chamilo\Core\User\Architecture\Enum\ActionEnum::DOWNLOAD_USER_PICTURE->value,
                            Manager::PARAM_USER_ID => $result->getId()
                        ]
                    );

                    $profileUrl = $this->getUrlGenerator()->fromParameters([
                        ApplicationInterface::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::CONTEXT,
                        ApplicationInterface::PARAM_ACTION => ActionEnum::VIEW_ONLINE->value,
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
