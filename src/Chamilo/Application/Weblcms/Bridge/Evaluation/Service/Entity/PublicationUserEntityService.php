<?php
namespace Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
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
     * @param ContentObjectPublication $contentObjectPublication
     * @param User $currentUser
     *
     * @return int[]
     */
    public function getAvailableEntityIdentifiersForUser(
        ContentObjectPublication $contentObjectPublication, User $currentUser
    )
    {
        return [$currentUser->getId()];
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

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return $this->translator->trans(
            'UserEntity', [],
            'Chamilo\Application\Weblcms\Tool\Implementation\Evaluation'
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     *
     * @return String
     */
    public function renderEntityName(DataClass $entity)
    {
        if (!$entity instanceof User)
        {
            throw new \InvalidArgumentException('The given entity must be of the type ' . User::class);
        }

        return $entity->get_fullname();
    }

    /**
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameById(int $entityId): string
    {
        $entity = DataManager::retrieve_by_id(User::class, $entityId);
        if (!$entity instanceof User)
        {
            throw new \InvalidArgumentException('The given user with id ' . $entityId . ' does not exist');
        }

        return $this->renderEntityName($entity);
    }
}