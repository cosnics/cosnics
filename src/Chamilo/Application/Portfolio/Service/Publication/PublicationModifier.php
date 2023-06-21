<?php
namespace Chamilo\Application\Portfolio\Service\Publication;

use Chamilo\Application\Portfolio\Manager;
use Chamilo\Application\Portfolio\Service\PublicationService;
use Chamilo\Application\Portfolio\Storage\DataClass\Publication;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager as PortfolioDisplayManager;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\ComplexPortfolio;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\ComplexPortfolioItem;
use Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\PortfolioItem;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Publication\Domain\PublicationResult;
use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Chamilo\Core\Repository\Publication\Service\PublicationModifierInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Form\FormValidator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationModifier implements PublicationModifierInterface
{

    protected UrlGenerator $urlGenerator;

    private PublicationAttributesGenerator $publicationAttributesGenerator;

    private PublicationService $publicationService;

    private Translator $translator;

    private UserService $userService;

    public function __construct(
        PublicationService $publicationService, PublicationAttributesGenerator $publicationAttributesGenerator,
        Translator $translator, UserService $userService, UrlGenerator $urlGenerator
    )
    {
        $this->publicationService = $publicationService;
        $this->publicationAttributesGenerator = $publicationAttributesGenerator;
        $this->translator = $translator;
        $this->userService = $userService;
        $this->urlGenerator = $urlGenerator;
    }

    public function addContentObjectPublicationAttributesElementsToForm(FormValidator $formValidator)
    {
    }

    public function deleteContentObjectPublication(int $publicationIdentifier): bool
    {
        return $this->getPublicationService()->deletePublicationByIdentifier($publicationIdentifier);
    }

    /**
     * @throws \Exception
     */
    public function getContentObjectPublicationAttributes(int $publicationIdentifier): Attributes
    {
        return $this->getPublicationAttributesGenerator()->createAttributesFromRecord(
            $this->getPublicationService()->findPublicationRecordByIdentifier($publicationIdentifier)
        );
    }

    public function getPublicationAttributesGenerator(): PublicationAttributesGenerator
    {
        return $this->publicationAttributesGenerator;
    }

    public function getPublicationService(): PublicationService
    {
        return $this->publicationService;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function publishContentObject(
        ContentObject $contentObject, PublicationTarget $publicationTarget, array $options = []
    ): PublicationResult
    {
        $publication =
            $this->getPublicationService()->findPublicationByIdentifier($publicationTarget->getPublicationIdentifier());
        $user = $this->getUserService()->findUserByIdentifier($publicationTarget->getUserIdentifier());

        $isPublication = $publication instanceof Publication;
        $isPublisher = $publication->get_publisher_id() == $publicationTarget->getUserIdentifier();

        $failureMessage = $this->getTranslator()->trans(
            'PublicationFailure', [
            '%CONTENT_OBJECT%' => $contentObject->get_title(),
            '%USER%' => $user->get_fullname()
        ], 'Chamilo\Application\Portfolio'
        );

        if ($isPublication && $isPublisher)
        {
            $portfolioContentObject = $publication->get_content_object();
            $portfolioPath = $portfolioContentObject->get_complex_content_object_path();
            $rootNode = $portfolioPath->get_root();

            if ($rootNode->forms_cycle_with($contentObject->getId()))
            {
                return new PublicationResult(PublicationResult::STATUS_FAILURE, $failureMessage);
            }

            if (!$contentObject instanceof Portfolio)
            {
                /**
                 * @var \Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\PortfolioItem $newObject
                 */
                $newObject = ContentObject::factory(PortfolioItem::class);
                $newObject->set_owner_id($publicationTarget->getUserIdentifier());
                $newObject->set_title('portfolio_item');
                $newObject->set_description('portfolio_item');
                $newObject->set_parent_id(0);
                $newObject->set_reference($contentObject->getId());
                $newObject->create();
            }
            else
            {
                $newObject = $contentObject;
            }

            if ($newObject instanceof Portfolio)
            {
                $wrapper = new ComplexPortfolio();
            }
            else
            {
                $wrapper = new ComplexPortfolioItem();
            }

            $wrapper->set_ref($newObject->getId());
            $wrapper->set_parent($portfolioContentObject->getId());
            $wrapper->set_user_id($publicationTarget->getUserIdentifier());
            $wrapper->set_display_order(
                DataManager::select_next_display_order(
                    $portfolioContentObject->getId()
                )
            );

            if (!$wrapper->create())
            {
                return new PublicationResult(PublicationResult::STATUS_FAILURE, $failureMessage);
            }
            else
            {
                Event::trigger(
                    'Activity', \Chamilo\Core\Repository\Manager::CONTEXT, [
                        Activity::PROPERTY_TYPE => Activity::ACTIVITY_ADD_ITEM,
                        Activity::PROPERTY_USER_ID => $publicationTarget->getUserIdentifier(),
                        Activity::PROPERTY_DATE => time(),
                        Activity::PROPERTY_CONTENT_OBJECT_ID => $portfolioContentObject->getId(),
                        Activity::PROPERTY_CONTENT => $portfolioContentObject->get_title() . ' > ' .
                            $contentObject->get_title()
                    ]
                );

                $currentParentsContentObjectIds = $rootNode->get_parents_content_object_ids(true, true);
                $currentParentsContentObjectIds[] = $contentObject->getId();

                $portfolioPath->reset();
                $portfolioPath = $portfolioContentObject->get_complex_content_object_path();

                $successMessage = $this->getTranslator()->trans(
                    'PublicationSuccess', [
                    '%CONTENT_OBJECT%' => $contentObject->get_title(),
                    '%USER%' => $user->get_fullname()
                ], 'Chamilo\Application\Portfolio'
                );

                $portfolioNode = $portfolioPath->follow_path_by_content_object_ids($currentParentsContentObjectIds);

                $publicationUrl = $this->getUrlGenerator()->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        PortfolioDisplayManager::PARAM_STEP => $portfolioNode->get_id()
                    ]
                );

                return new PublicationResult(
                    PublicationResult::STATUS_SUCCESS, $successMessage, $publicationUrl
                );
            }
        }
        else
        {
            return new PublicationResult(PublicationResult::STATUS_FAILURE, $failureMessage);
        }
    }

    public function setPublicationAttributesGenerator(PublicationAttributesGenerator $publicationAttributesGenerator
    ): void
    {
        $this->publicationAttributesGenerator = $publicationAttributesGenerator;
    }

    public function setPublicationService(PublicationService $publicationService): void
    {
        $this->publicationService = $publicationService;
    }

    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    public function setUserService(UserService $userService): void
    {
        $this->userService = $userService;
    }

    public function updateContentObjectPublicationContentObjectIdentifier(Attributes $publicationAttributes): bool
    {
        $publication =
            $this->getPublicationService()->findPublicationByIdentifier((int) $publicationAttributes->getId());

        if ($publication instanceof Publication)
        {
            $publication->set_content_object_id($publicationAttributes->get_content_object_id());

            return $this->getPublicationService()->updatePublication($publication);
        }
        else
        {
            return false;
        }
    }
}