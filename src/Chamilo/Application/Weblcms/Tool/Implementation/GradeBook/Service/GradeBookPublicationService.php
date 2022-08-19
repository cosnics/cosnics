<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Service;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Storage\Repository\PublicationRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Service
 *
 * @author Stefan Gabriels - Hogeschool Gent
 */
class GradeBookPublicationService
{
    /**
     * @var PublicationRepository
     */
    protected $publicationRepository;

    /**
     * @param PublicationRepository $publicationRepository
     */
    public function __construct(PublicationRepository $publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Storage\DataClass\Publication|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findPublicationByContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        return $this->publicationRepository->findPublicationByContentObjectPublication($contentObjectPublication);
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param FilterParameters|null $filterParameters
     *
     * @return array
     */
    public function getTargetUsers(ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters = null): array
    {
        return $this->publicationRepository->getTargetUsers($contentObjectPublication, $filterParameters);
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param FilterParameters|null $filterParameters
     *
     * @return array
     */
    public function getTargetUserIds(ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters = null): array
    {
        return $this->publicationRepository->getTargetUserIds($contentObjectPublication, $filterParameters);
    }

    /**
     * @param User $user
     * @param ContentObjectPublication $contentObjectPublication
     * @param array|null $targetUserIds
     *
     * @return bool
     */
    public function isUserSubscribedToPublication(User $user, ContentObjectPublication $contentObjectPublication, array $targetUserIds = null): bool
    {
        if (is_null($targetUserIds))
        {
            $targetUserIds = $this->getTargetUserIds($contentObjectPublication);
        }
        return in_array($user->getId(), $targetUserIds);
    }
}