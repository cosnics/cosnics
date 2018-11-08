<?php
namespace Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Chamilo\Core\Repository\Publication\Storage\Repository\PublicationTargetRepository;

/**
 * @package Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetService
{
    /**
     * @var \Chamilo\Core\Repository\Publication\Storage\Repository\PublicationTargetRepository
     */
    private $publicationTargetRepository;

    /**
     * @param \Chamilo\Core\Repository\Publication\Storage\Repository\PublicationTargetRepository $publicationTargetRepository
     */
    public function __construct(PublicationTargetRepository $publicationTargetRepository)
    {
        $this->publicationTargetRepository = $publicationTargetRepository;
    }

    /**
     * @return \Chamilo\Core\Repository\Publication\Storage\Repository\PublicationTargetRepository
     */
    public function getPublicationTargetRepository(): PublicationTargetRepository
    {
        return $this->publicationTargetRepository;
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Storage\Repository\PublicationTargetRepository $publicationTargetRepository
     */
    public function setPublicationTargetRepository(PublicationTargetRepository $publicationTargetRepository): void
    {
        $this->publicationTargetRepository = $publicationTargetRepository;
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationTarget $publicationTarget
     *
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function addPublicationTarget(PublicationTarget $publicationTarget)
    {
        return $this->getPublicationTargetRepository()->addPublicationTarget(
            $this->getPublicationTargetKey(), $publicationTarget
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationTarget $publicationTarget
     *
     * @return string
     */
    protected function getPublicationTargetKey(PublicationTarget $publicationTarget)
    {
        return md5(serialize($publicationTarget));
    }

    /**
     * @param string $key
     *
     * @return \Chamilo\Core\Repository\Publication\Domain\PublicationTarget
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getPublicationTarget(string $key)
    {
        return $this->getPublicationTargetRepository()->getPublicationTarget($key);
    }

}