<?php
namespace Chamilo\Core\Admin\UserInterface\Table;

use Chamilo\Core\Admin\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\Entity\User;
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
    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer,
        PageNavigationCalculator $pager, DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory,
        ClassnameUtilities $classnameUtilities, protected ?User $currentUser = null
    )
    {
        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory,
            $classnameUtilities
        );
    }

    protected function initializeColumns(): void
    {
        $this->addColumn(
            $this->dataClassPropertyTableColumnFactory->getColumn(User::class, User::PROPERTY_OFFICIAL_CODE)
        );
        $this->addColumn(
            $this->dataClassPropertyTableColumnFactory->getColumn(User::class, User::PROPERTY_SURNAME)
        );
        $this->addColumn(
            $this->dataClassPropertyTableColumnFactory->getColumn(User::class, User::PROPERTY_GIVEN_NAME)
        );

        $this->addColumn(
            $this->dataClassPropertyTableColumnFactory->getColumn(User::class, User::PROPERTY_PICTURE_URI)
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\Entity\User $result
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, mixed $result): string
    {
        switch ($column->getName()) {
            case User::PROPERTY_PLATFORM_ADMINISTRATOR :
                if ($result->getPlatformAdmin() == '1') {
                    return $this->translator->trans('PlatformAdministrator', [], Manager::CONTEXT);
                }
                else {
                    return '';
                }
            case User::PROPERTY_PICTURE_URI :
                if ($this->currentUser instanceof User && $this->currentUser->isPlatformAdministrator()) {
                    $profilePhotoUrl = $this->urlGenerator->fromParameters(
                        [
                            ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                            ApplicationInterface::PARAM_ACTION => \Chamilo\Core\User\Architecture\Enum\ActionEnum::DOWNLOAD_USER_PICTURE->value,
                            Manager::PARAM_USER_ID => $result->getId()
                        ]
                    );

                    $profileUrl = $this->urlGenerator->fromParameters([
                        ApplicationInterface::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::CONTEXT,
                        ApplicationInterface::PARAM_ACTION => ActionEnum::VIEW_ONLINE->value,
                        \Chamilo\Core\Admin\Manager::PARAM_USER_ID => $result->getId()
                    ]);

                    return '<a href="' . $profileUrl . '">' .
                        '<img style="max-width: 100px; max-height: 100px;" src="' . $profilePhotoUrl . '" alt="' .
                        $this->translator->trans('UserPicture', [], Manager::CONTEXT) . '" /></a>';
                }

                return '';
        }

        return parent::renderCell($column, $resultPosition, $result);
    }
}
