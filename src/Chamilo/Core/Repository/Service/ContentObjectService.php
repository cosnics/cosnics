<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Repository\Storage\Repository\ContentObjectRepository;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectService
{
    /**
     * @var \Chamilo\Core\Repository\Storage\Repository\ContentObjectRepository
     */
    private $contentObjectRepository;

    /**
     * @param \Chamilo\Core\Repository\Storage\Repository\ContentObjectRepository $contentObjectRepository
     */
    public function __construct(ContentObjectRepository $contentObjectRepository)
    {
        $this->contentObjectRepository = $contentObjectRepository;
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\Repository\ContentObjectRepository
     */
    public function getContentObjectRepository(): ContentObjectRepository
    {
        return $this->contentObjectRepository;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\Repository\ContentObjectRepository $contentObjectRepository
     */
    public function setContentObjectRepository(ContentObjectRepository $contentObjectRepository): void
    {
        $this->contentObjectRepository = $contentObjectRepository;
    }

    /**
     * @see \Chamilo\Core\Repository\Storage\DataManager::get_used_disk_space()
     * @todo Implement this
     */
    public function getUsedStorageSpace()
    {

    }

    /**
     * @see \Chamilo\Core\Repository\Storage\DataManager::get_used_disk_space()
     * @todo Implement this
     */
    public function getUsedStorageSpaceForUser(User $user)
    {

    }

}