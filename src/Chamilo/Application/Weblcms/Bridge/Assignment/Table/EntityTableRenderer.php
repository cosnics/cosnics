<?php
namespace Chamilo\Application\Weblcms\Bridge\Assignment\Table;

use Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\RightsService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class EntityTableRenderer
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\EntityTableRenderer
{
    public const TABLE_IDENTIFIER = DataClass::PROPERTY_ID;

    protected EntityServiceInterface $entityService;

    public function __construct(
        EntityServiceInterface $entityService, AssignmentDataProvider $assignmentDataProvider,
        DatetimeUtilities $datetimeUtilities, RightsService $rightsService, User $user, Translator $translator,
        UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->entityService = $entityService;

        parent::__construct(
            $assignmentDataProvider, $datetimeUtilities, $rightsService, $user, $translator, $urlGenerator,
            $htmlTableRenderer, $pager
        );
    }

    public function getEntityService(): EntityServiceInterface
    {
        return $this->entityService;
    }

    protected function isEntity($entityId, $userId): bool
    {
        $user = new User();
        $user->setId($userId);

        return $this->getEntityService()->isUserPartOfEntity(
            $user, $this->application->getContentObjectPublication(), $entityId
        );
    }
}
