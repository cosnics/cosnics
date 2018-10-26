<?php
namespace Chamilo\Application\Portfolio\Service;

use Chamilo\Application\Portfolio\Storage\Repository\PublicationRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Application\Portfolio\Storage\DataClass\Publication;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Application\Portfolio\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationService
{

    /**
     *
     * @var \Chamilo\Application\Portfolio\Storage\Repository\PublicationRepository
     */
    private $publicationRepository;

    /**
     *
     * @var \Chamilo\Application\Portfolio\Service\RightsService
     */
    private $rightsService;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\Repository\PublicationRepository $publicationRepository
     * @param \Chamilo\Application\Portfolio\Service\RightsService $rightsService
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(PublicationRepository $publicationRepository, RightsService $rightsService,
        Translator $translator)
    {
        $this->publicationRepository = $publicationRepository;
        $this->rightsService = $rightsService;
        $this->translator = $translator;
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Storage\Repository\PublicationRepository
     */
    public function getPublicationRepository()
    {
        return $this->publicationRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\Repository\PublicationRepository $publicationRepository
     */
    public function setPublicationRepository(PublicationRepository $publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Service\RightsService
     */
    public function getRightsService()
    {
        return $this->rightsService;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Service\RightsService $rightsService
     */
    public function setRightsService(RightsService $rightsService)
    {
        $this->rightsService = $rightsService;
    }

    /**
     *
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     *
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     *
     * @param integer $userIdentifier
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Publication
     */
    public function getPublicationForUserIdentifier($userIdentifier)
    {
        return $this->getPublicationRepository()->getPublicationForUserIdentifier($userIdentifier);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @throws \NotAllowedException
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Publication
     */
    public function createRootPortfolioAndPublicationForUser(User $user)
    {
        $portfolio = $this->createRootPortfolioForUser($user);
        $publication = $this->getPublicationInstanceForPortfolioAndUser($portfolio, $user);

        if (! $this->createPublication($publication))
        {
            throw new NotAllowedException();
        }

        $this->getRightsService()->createRightsForEveryUserAtPortfolioRoot($publication->getId(), $portfolio);

        return $publication;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio
     */
    public function createRootPortfolioForUser(User $user)
    {
        $templateRegistration = \Chamilo\Core\Repository\Configuration::registration_default_by_type(
            Portfolio::package());

        $portfolio = new Portfolio();
        $portfolio->set_title($user->get_fullname());
        $portfolio->set_description(
            $this->translator->trans('NoInstructionYetDescription', [], 'Chamilo\Application\Portfolio'));
        $portfolio->set_owner_id($user->getId());

        $portfolio->set_template_registration_id($templateRegistration->getId());
        $portfolio->create();

        return $portfolio;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio $portfolio
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Publication
     */
    public function getPublicationInstanceForPortfolioAndUser(Portfolio $portfolio, User $user)
    {
        return $this->getPublicationInstanceForParameters($portfolio->getId(), $user->getId(), time(), time());
    }

    /**
     *
     * @param integer $contentObjectIdentifier
     * @param integer $publisherIdentifier
     * @param integer $published
     * @param integer $modified
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Publication
     */
    public function getPublicationInstanceForParameters($contentObjectIdentifier, $publisherIdentifier, $published,
        $modified)
    {
        $publication = new Publication();

        $publication->set_content_object_id($contentObjectIdentifier);
        $publication->set_publisher_id($publisherIdentifier);
        $publication->set_published($published);
        $publication->set_modified($modified);

        return $publication;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\Publication $publication
     */
    public function createPublication(Publication $publication)
    {
        return $this->getPublicationRepository()->createPublication($publication);
    }
}

