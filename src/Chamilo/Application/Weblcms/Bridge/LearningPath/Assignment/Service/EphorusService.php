<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\Repository\EphorusRepository;

/**
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EphorusService extends \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Service\EphorusService
{
    /**
     * @var EphorusRepository
     */
    protected $ephorusRepository;

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\Repository\EphorusRepository $ephorusRepository
     */
    public function __construct(EphorusRepository $ephorusRepository)
    {
        parent::__construct($ephorusRepository);
    }

}