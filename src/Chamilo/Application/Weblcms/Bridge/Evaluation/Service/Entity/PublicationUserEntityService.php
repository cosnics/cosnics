<?php
namespace Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PublicationUserEntityService implements PublicationEntityServiceInterface
{
    /**
     * @var PublicationEntityServiceManager
     */
    protected $publicationEntityServiceManager;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var Translator
     */
    protected $translator;

    public function __construct(PublicationEntityServiceManager $publicationEntityServiceManager, UserService $userService, Translator $translator)
    {
        $this->publicationEntityServiceManager = $publicationEntityServiceManager;
        $this->userService = $userService;
        $this->translator = $translator;
    }

    /**
     * @return ContentObjectPublication
     */
    public function getContentObjectPublication(): ContentObjectPublication
    {
        return $this->publicationEntityServiceManager->getContentObjectPublication();
    }

    /**
     * @return int[]
     */
    public function getTargetEntityIds(): array
    {
        $contentObjectPublication = $this->getContentObjectPublication();
        return DataManager::getPublicationTargetUserIds($contentObjectPublication->getId(), $contentObjectPublication->get_course_id());
    }

    /**
     * @param int $entityId
     *
     * @return User[]
     */
    public function getUsersForEntity(int $entityId): array
    {
        return $this->userService->findUsersByIdentifiers([$entityId]);
    }

    /**
     * @param User $user
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(User $user, int $entityId): bool
    {
        return $user->getId() == $entityId;
    }

    /**
     * @param User $currentUser
     *
     * @return int|null
     */
    public function getCurrentEntityIdentifier(User $currentUser): ?int
    {
        return $currentUser->getId();
    }

    /**
     * @param int $entityId
     * @return string
     */
    public function getEntityDisplayName(int $entityId): string
    {
        $user = $this->getUsersForEntity($entityId)[0];
        return $user->get_fullname();
    }

    /**
     * @return string
     */
    public function getPluralEntityName(): string
    {
        return $this->translator->trans(
            'UsersEntity', [],
            'Chamilo\Application\Weblcms\Tool\Implementation\Evaluation'
        );
    }
}