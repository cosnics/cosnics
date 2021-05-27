<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service;

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
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Publication\Service\PublicationModifierInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationModifier implements PublicationModifierInterface
{

    /**
     * @var \Chamilo\Application\Portfolio\Service\PublicationService
     */
    private $publicationService;

    /**
     * @var \Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service\PublicationAttributesGenerator
     */
    private $publicationAttributesGenerator;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     *
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     *
     * @param \Chamilo\Application\Portfolio\Service\PublicationService $publicationService
     * @param \Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service\PublicationAttributesGenerator $publicationAttributesGenerator
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function __construct(
        PublicationService $publicationService, PublicationAttributesGenerator $publicationAttributesGenerator,
        Translator $translator, UserService $userService
    )
    {
        $this->publicationService = $publicationService;
        $this->publicationAttributesGenerator = $publicationAttributesGenerator;
        $this->translator = $translator;
        $this->userService = $userService;
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     *
     * @see PublicationInterface::add_publication_attributes_elements()
     */
    public function addContentObjectPublicationAttributesElementsToForm(FormValidator $formValidator)
    {
    }

    /**
     * @param integer $publicationIdentifier
     *
     * @return bool
     */
    public function deleteContentObjectPublication(int $publicationIdentifier)
    {
        return $this->getPublicationService()->deletePublicationByIdentifier($publicationIdentifier);
    }

    /**
     * @param integer $publicationIdentifier
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes
     * @throws \Exception
     */
    public function getContentObjectPublicationAttributes(int $publicationIdentifier)
    {
        return $this->getPublicationAttributesGenerator()->createAttributesFromRecord(
            $this->getPublicationService()->findPublicationRecordByIdentifier($publicationIdentifier)
        );
    }

    /**
     * @return \Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service\PublicationAttributesGenerator
     */
    public function getPublicationAttributesGenerator(): PublicationAttributesGenerator
    {
        return $this->publicationAttributesGenerator;
    }

    /**
     * @param \Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service\PublicationAttributesGenerator $publicationAttributesGenerator
     */
    public function setPublicationAttributesGenerator(PublicationAttributesGenerator $publicationAttributesGenerator
    ): void
    {
        $this->publicationAttributesGenerator = $publicationAttributesGenerator;
    }

    /**
     * @return \Chamilo\Application\Portfolio\Service\PublicationService
     */
    public function getPublicationService(): PublicationService
    {
        return $this->publicationService;
    }

    /**
     * @param \Chamilo\Application\Portfolio\Service\PublicationService $publicationService
     */
    public function setPublicationService(PublicationService $publicationService): void
    {
        $this->publicationService = $publicationService;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function setUserService(UserService $userService): void
    {
        $this->userService = $userService;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Domain\PublicationTarget $publicationTarget
     * @param array $options
     *
     * @return \Chamilo\Core\Repository\Publication\Domain\PublicationResult
     * @throws \Exception
     * @see PublicationModifierInterface::publishContentObject()
     */
    public function publishContentObject(
        ContentObject $contentObject, PublicationTarget $publicationTarget, $options = []
    )
    {
        $publication =
            $this->getPublicationService()->findPublicationByIdentifier($publicationTarget->getPublicationIdentifier());
        $user = $this->getUserService()->findUserByIdentifier($publicationTarget->getUserIdentifier());

        $isPublication = $publication instanceof Publication;
        $isPublisher = $publication->get_publisher_id() == $publicationTarget->getUserIdentifier();

        $failureMessage = $this->getTranslator()->trans(
            'PublicationFailure', [
            '%CONTENT_OBJECT%' => $contentObject->get_title(), '%USER%' => $user->get_fullname()
        ], 'Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository'
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
                $newObject->set_title(PortfolioItem::get_type_name());
                $newObject->set_description(PortfolioItem::get_type_name());
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
                $wrapper =
                    new ComplexPortfolioItem();
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
                    'Activity', \Chamilo\Core\Repository\Manager::context(), array(
                        Activity::PROPERTY_TYPE => Activity::ACTIVITY_ADD_ITEM,
                        Activity::PROPERTY_USER_ID => $publicationTarget->getUserIdentifier(),
                        Activity::PROPERTY_DATE => time(),
                        Activity::PROPERTY_CONTENT_OBJECT_ID => $portfolioContentObject->getId(),
                        Activity::PROPERTY_CONTENT => $portfolioContentObject->get_title() . ' > ' .
                            $contentObject->get_title()
                    )
                );

                $currentParentsContentObjectIds = $rootNode->get_parents_content_object_ids(true, true);
                $currentParentsContentObjectIds[] = $contentObject->getId();

                $portfolioPath->reset();
                $portfolioPath = $portfolioContentObject->get_complex_content_object_path();

                $successMessage = $this->getTranslator()->trans(
                    'PublicationSuccess', [
                    '%CONTENT_OBJECT%' => $contentObject->get_title(), '%USER%' => $user->get_fullname()
                ], 'Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository'
                );

                $portfolioNode = $portfolioPath->follow_path_by_content_object_ids($currentParentsContentObjectIds);

                $publicationUrl = new Redirect(
                    array(
                        Application::PARAM_CONTEXT => Manager::package(),
                        PortfolioDisplayManager::PARAM_STEP => $portfolioNode->get_id()
                    )
                );

                return new PublicationResult(
                    PublicationResult::STATUS_SUCCESS, $successMessage, $publicationUrl->getUrl()
                );
            }
        }
        else
        {
            return new PublicationResult(PublicationResult::STATUS_FAILURE, $failureMessage);
        }
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes $publicationAttributes
     *
     * @return boolean
     */
    public function updateContentObjectPublicationContentObjectIdentifier(Attributes $publicationAttributes)
    {
        $publication = $this->getPublicationService()->findPublicationByIdentifier($publicationAttributes->getId());

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