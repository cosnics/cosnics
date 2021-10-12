<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Presence\Service;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\Repository\PublicationRepository;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Presence\Service
 *
 * @author Stefan Gabriels - Hogeschool Gent
 */
class PublicationService
{
    /**
     * @var PublicationRepository
     */
    protected $publicationRepository;

    /**
     *
     * @param PublicationRepository $publicationRepository
     */
    public function __construct(PublicationRepository $publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\DataClass\Publication|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
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
    public function getTargetUserIds(ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters = null): array
    {
        return $this->publicationRepository->getTargetUserIds($contentObjectPublication, $filterParameters);
    }
}