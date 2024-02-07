<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Service;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class EphorusService
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\EphorusService
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\EphorusRepository
     */
    protected $ephorusRepository;
}