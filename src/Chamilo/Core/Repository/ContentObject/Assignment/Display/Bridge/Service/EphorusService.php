<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\Repository\EphorusRepository;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class EphorusService
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\Repository\EphorusRepository
     */
    protected $ephorusRepository;

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\Repository\EphorusRepository $ephorusRepository
     */
    public function __construct(EphorusRepository $ephorusRepository)
    {
        $this->ephorusRepository = $ephorusRepository;
    }
}