<?php
namespace Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
interface PublicationEntityServiceInterface
{
    /**
     * @return array
     */
    public function getTargetEntityIds(): array;

    /**
     * @param int $entityId
     * @return array
     */
    public function getUsersForEntity(int $entityId): array;

    /**
     * @param User $user
     * @param int $entityId
     * @return bool
     */
    public function isUserPartOfEntity(User $user, int $entityId): bool;


    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param User $currentUser
     *
     * @return int[]
     */
    public function getAvailableEntityIdentifiersForUser(ContentObjectPublication $contentObjectPublication, User $currentUser);

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     *
     * @return String
     */
    public function renderEntityName(DataClass $entity);


    /**
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameById(int $entityId): String;

    /**
     * @param User $currentUser
     * @return int|null
     */
    public function getCurrentEntityIdentifier(User $currentUser): ?int;

    /**
     * @param int $entityId
     * @return string
     */
    public function getEntityDisplayName(int $entityId): string;

    /**
     * @return string
     */
    public function getPluralEntityName(): string;

    /**
     * @return string
     */
    public function getEntityName(): string;
}