<?php
namespace Chamilo\Application\Portfolio\Service;

use Chamilo\Application\Portfolio\Storage\DataClass\Notification;
use Chamilo\Application\Portfolio\Storage\DataClass\Publication;
use Chamilo\Application\Portfolio\Storage\Repository\NotificationRepository;
use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Application\Portfolio\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NotificationService
{

    /**
     *
     * @var \Chamilo\Application\Portfolio\Storage\Repository\NotificationRepository
     */
    private $notificationRepository;

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\Repository\NotificationRepository $notificationRepository
     */
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Storage\Repository\NotificationRepository
     */
    public function getNotificationRepository()
    {
        return $this->notificationRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\Repository\NotificationRepository $notificationRepository
     */
    public function setNotificationRepository(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\Publication $publication
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Feedback
     */
    public function getNotificationInstanceForPublication(Publication $publication)
    {
        $notification = new Notification();
        $notification->set_publication_id($publication->getId());

        return $notification;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\Publication $publication
     * @param \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode $node
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Notification[]
     */
    public function findPortfolioNotificationsForPublicationAndNode(Publication $publication,
        \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode $node)
    {
        return $this->findPortfolioNotificationsForPublicationIdentifierAndComplexContentObjectIdentifier(
            $publication->getId(),
            $this->getComplexContentObjectIdentifierForNode($node));
    }

    /**
     *
     * @param integer $publicationIdentifier
     * @param integer $complexContentObjectIdentifier
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Notification[]
     */
    public function findPortfolioNotificationsForPublicationIdentifierAndComplexContentObjectIdentifier(
        $publicationIdentifier, $complexContentObjectIdentifier)
    {
        return $this->getNotificationRepository()->findPortfolioNotificationsForPublicationIdentifierAndComplexContentObjectIdentifier(
            $publicationIdentifier,
            $complexContentObjectIdentifier);
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\Publication $publication
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode $node
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Notification
     */
    public function findPortfolioNotificationForPublicationUserAndNode(Publication $publication, User $user,
        ComplexContentObjectPathNode $node)
    {
        return $this->findPortfolioNotificationForPublicationIdentifierUserIdentifierAndComplexContentObjectIdentifier(
            $publication->getId(),
            $user->getId(),
            $this->getComplexContentObjectIdentifierForNode($node));
    }

    /**
     *
     * @param integer $publicationIdentifier
     * @param integer $userIdentifier
     * @param integer $complexContentObjectIdentifier
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Notification
     */
    public function findPortfolioNotificationForPublicationIdentifierUserIdentifierAndComplexContentObjectIdentifier(
        $publicationIdentifier, $userIdentifier, $complexContentObjectIdentifier)
    {
        return $this->getNotificationRepository()->findPortfolioNotificationForPublicationIdentifierUserIdentifierAndComplexContentObjectIdentifier(
            $publicationIdentifier,
            $userIdentifier,
            $complexContentObjectIdentifier);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode $node
     * @return integer
     */
    private function getComplexContentObjectIdentifierForNode(ComplexContentObjectPathNode $node)
    {
        return $node->get_complex_content_object_item() ? $node->get_complex_content_object_item()->getId() : 0;
    }
}