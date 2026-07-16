<?php
namespace Chamilo\Core\Admin\UserInterface\Table;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
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
        ClassnameUtilities $classnameUtilities, protected UserPictureProviderInterface $userPictureProvider,
        protected ?User $currentUser = null
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
                    $profileUrl = $this->urlGenerator->fromParameters([
                        ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                        ApplicationInterface::PARAM_ACTION => ActionEnum::VIEW->value,
                        Manager::PARAM_USER_ID => $result->getIdentifier()->toString()
                    ]);

                    $userPicture = $this->userPictureProvider->getUserPictureAsBase64String($result);

                    return '<a href="' . $profileUrl . '">' .
                        '<img class="img-profile img-thumbnail object-fit-cover" style="max-width: 100px; max-height: 100px;" src="' .
                        $userPicture . '" alt="' . $this->translator->trans('UserPicture', [], Manager::CONTEXT) .
                        '" /></a>';
                }

                return '';
        }

        return parent::renderCell($column, $resultPosition, $result);
    }
}
