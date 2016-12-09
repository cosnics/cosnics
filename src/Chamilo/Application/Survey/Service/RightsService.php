<?php
namespace Chamilo\Application\Survey\Service;

use Chamilo\Application\Survey\Repository\EntityRelationRepository;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Application\Survey\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RightsService
{
    // Rights on publications
    const RIGHT_VIEW = 1;
    const RIGHT_TAKE = 2;
    const RIGHT_MAIL = 4;
    const RIGHT_REPORT = 8;
    const RIGHT_MANAGE = 16;
    
    // Rights on application
    const RIGHT_PUBLISH = 32;
    // const RIGHT_USE = 16;
    // const RIGHT_COPY = 32;
    //
    const APPLICATION_RIGHTS = 1;
    const PUBLICATION_RIGHTS = 2;

    /**
     *
     * @var \Chamilo\Application\Survey\Service\RightsService
     */
    private static $instance;

    /**
     *
     * @var \Chamilo\Application\Survey\Service\EntityRelationService
     */
    private $entityRelationService;

    /**
     *
     * @var \Chamilo\Application\Survey\Service\EntityService
     */
    private $entityService;

    /**
     *
     * @param \Chamilo\Application\Survey\Service\ContentObjectRelationService $contentObjectRelationService
     * @param \Chamilo\Application\Survey\Service\EntityRelationService $entityRelationService
     * @param \Chamilo\Application\Survey\Service\EntityService $entityService
     */
    public function __construct(EntityRelationService $entityRelationService, EntityService $entityService)
    {
        $this->entityRelationService = $entityRelationService;
        $this->entityService = $entityService;
    }

    /**
     *
     * @return \Chamilo\Application\Survey\Service\EntityRelationService
     */
    public function getEntityRelationService()
    {
        return $this->entityRelationService;
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Service\EntityRelationService $entityRelationService
     */
    public function setEntityRelationService($entityRelationService)
    {
        $this->entityRelationService = $entityRelationService;
    }

    /**
     *
     * @return \Chamilo\Application\Survey\Service\EntityService
     */
    public function getEntityService()
    {
        return $this->entityService;
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Service\EntityService $entityService
     */
    public function setEntityService($entityService)
    {
        $this->entityService = $entityService;
    }

    /**
     *
     * @param integer $right
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param Chamilo\Application\Survey\Storage\DataClass\Publication $Publication
     *
     * @return boolean
     */
    private function hasRightForPublication($right, User $user, Publication $Publication)
    {
        if ($right != self::RIGHT_TAKE)
        {
            
            if ($this->hasPublicationCreatorRights($user, $Publication))
            {
                return true;
            }
        }
        
        return $this->getEntityRelationService()->hasRight(
            $this->getEntityService()->getEntitiesForUser($user), 
            $right, 
            $Publication);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Survey\Storage\DataClass\Publication $Publication
     * @return boolean
     */
    public function isPublicationCreator(User $user, Publication $Publication)
    {
        return $user->getId() == $Publication->getPublisherId();
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Survey\Storage\DataClass\Publication $Publication
     * @return boolean
     */
    public function hasPublicationCreatorRights(User $user, Publication $Publication)
    {
        // Check if the user is a platform administrator
        if ($user->is_platform_admin())
        {
            return true;
        }
        
        if ($this->isPublicationCreator($user, $Publication))
        {
            return true;
        }
        
        return $this->getEntityRelationService()->hasRight(
            $this->getEntityService()->getEntitiesForUser($user), 
            self::RIGHT_MANAGE, 
            $Publication);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Survey\Storage\DataClass\Publication $Publication
     *
     * @return boolean
     */
    public function canTakeSurvey(User $user, Publication $Publication)
    {
        return $this->hasRightForPublication(self::RIGHT_TAKE, $user, $Publication);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Survey\Storage\DataClass\Publication $Publication
     *
     * @return boolean
     */
    public function canViewAndExportResults(User $user, Publication $Publication)
    {
        return $this->hasRightForPublication(self::RIGHT_REPORT, $user, $Publication);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Survey\Storage\DataClass\Publication $Publication
     *
     * @return boolean
     */
    public function canManagePublication(User $user, Publication $Publication)
    {
        return $this->hasRightForPublication(self::RIGHT_MANAGE, $user, $Publication);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Survey\Storage\DataClass\Publication $Publication
     *
     * @return boolean
     */
    public function canMail(User $user, Publication $Publication)
    {
        return $this->hasRightForPublication(self::RIGHT_MAIL, $user, $Publication);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Survey\Storage\DataClass\Publication $Publication
     *
     * @return boolean
     */
    public function canPublish(User $user, Publication $Publication)
    {
        return $this->hasRightForPublication(self::RIGHT_PUBLISH, $user, $Publication);
    }

    /**
     *
     * @param integer $viewRight
     * @param integer $useRight
     * @param integer $copyRight
     * @return integer
     */
    public function getAggregatedRight($takeRight, $mailRight, $reportRight, $manageRight, $publishRight)
    {
        $right = self::RIGHT_VIEW;
        
        if ($takeRight)
        {
            $right = $right | $takeRight;
        }
        
        if ($mailRight)
        {
            $right = $right | $mailRight;
        }
        
        if ($reportRight)
        {
            $right = $right | $reportRight;
        }
        
        if ($manageRight)
        {
            $right = $right | $manageRight;
        }
        
        if ($publishRight)
        {
            $right = $publishRight;
        }
        
        return $right;
    }

    /**
     *
     * @return \Chamilo\Application\Survey\Service\RightsService
     */
    static public function getInstance()
    {
        if (is_null(static::$instance))
        {
            $entityRelationService = new EntityRelationService(new EntityRelationRepository());
            $entityService = new EntityService();
            
            self::$instance = new static($entityRelationService, $entityService);
        }
        
        return static::$instance;
    }
}