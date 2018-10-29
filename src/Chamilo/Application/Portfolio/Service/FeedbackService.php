<?php
namespace Chamilo\Application\Portfolio\Service;

use Chamilo\Application\Portfolio\Storage\Repository\FeedbackRepository;
use Chamilo\Application\Portfolio\Storage\DataClass\Publication;
use Chamilo\Application\Portfolio\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Application\Portfolio\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FeedbackService
{

    /**
     *
     * @var \Chamilo\Application\Portfolio\Storage\Repository\FeedbackRepository
     */
    private $feedbackRepository;

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\Repository\FeedbackRepository $feedbackRepository
     */
    public function __construct(FeedbackRepository $feedbackRepository)
    {
        $this->feedbackRepository = $feedbackRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Storage\Repository\FeedbackRepository
     */
    public function getFeedbackRepository()
    {
        return $this->feedbackRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\Repository\FeedbackRepository $feedbackRepository
     */
    public function setFeedbackRepository(FeedbackRepository $feedbackRepository)
    {
        $this->feedbackRepository = $feedbackRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\Publication $publication
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Feedback
     */
    public function getFeedbackInstanceForPublication(Publication $publication)
    {
        $feedback = new Feedback();
        $feedback->set_publication_id($publication->getId());

        return $feedback;
    }

    /**
     *
     * @param integer $identifier
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Feedback
     */
    public function findFeedbackByIdentfier(int $identifier)
    {
        return $this->getFeedbackRepository()->findFeedbackByIdentfier($identifier);
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\Publication $publication
     * @param \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode $node
     * @param integer $userIdentifier
     * @param integer $count
     * @param integer $offset
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Feedback[]
     */
    public function findFeedbackForPublicationNodeUserIdentifierCountAndOffset(Publication $publication,
        ComplexContentObjectPathNode $node, int $userIdentifier = null, int $count = null, int $offset = null)
    {
        return $this->findFeedbackForPublicationComplexContentObjectUserIdentifiersCountAndOffset(
            $publication->getId(),
            $this->getComplexContentObjectIdentifierForNode($node),
            $userIdentifier,
            $count,
            $offset);
    }

    /**
     *
     * @param integer $publicationIdentifier
     * @param integer $complexContentObjectIdentifier
     * @param integer $userIdentifier
     * @param integer $count
     * @param integer $offset
     */
    public function findFeedbackForPublicationComplexContentObjectUserIdentifiersCountAndOffset(
        int $publicationIdentifier, int $complexContentObjectIdentifier = null, int $userIdentifier = null, int $count = null,
        int $offset = null)
    {
        return $this->getFeedbackRepository()->findFeedbackForPublicationComplexContentObjectUserIdentifiersCountAndOffset(
            $publicationIdentifier,
            $complexContentObjectIdentifier,
            $userIdentifier,
            $count,
            $offset);
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\Publication $publication
     * @param \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode $node
     * @param integer $userIdentifier
     * @return integer
     */
    public function countFeedbackForPublicationNodeAndUserIdentifier(Publication $publication, ComplexContentObjectPathNode $node,
        int $userIdentifier = null)
    {
        return $this->countFeedbackForPublicationComplexContentObjectAndUserIdentifiers(
            $publication->getId(),
            $this->getComplexContentObjectIdentifierForNode($node),
            $userIdentifier);
    }

    /**
     *
     * @param integer $publicationIdentifier
     * @param integer $complexContentObjectIdentifier
     * @param integer $userIdentifier
     * @return integer
     */
    public function countFeedbackForPublicationComplexContentObjectAndUserIdentifiers(int $publicationIdentifier,
        int $complexContentObjectIdentifier = null, int $userIdentifier = null)
    {
        return $this->getFeedbackRepository()->countFeedbackForPublicationComplexContentObjectAndUserIdentifiers(
            $publicationIdentifier,
            $complexContentObjectIdentifier,
            $userIdentifier);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode $node
     * @return integer
     */
    private function getComplexContentObjectIdentifierForNode(ComplexContentObjectPathNode $node)
    {
        return $node->get_complex_content_object_item() ? $node->get_complex_content_object_item()->getId() : null;
    }
}