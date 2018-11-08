<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Application\Portfolio\Service\PublicationService;
use Chamilo\Application\Portfolio\Storage\DataClass\Publication;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\ComplexPortfolio;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\PortfolioItem;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Publication\LocationSupport;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Publication\Service\PublicationModifierInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Format\Form\FormValidator;

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
     * PublicationAggregator constructor.
     *
     * @param \Chamilo\Application\Portfolio\Service\PublicationService $publicationService
     * @param \Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service\PublicationAttributesGenerator $publicationAttributesGenerator
     */
    public function __construct(
        PublicationService $publicationService, PublicationAttributesGenerator $publicationAttributesGenerator
    )
    {
        $this->publicationService = $publicationService;
        $this->publicationAttributesGenerator = $publicationAttributesGenerator;
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
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\Repository\Publication\LocationSupport $location
     * @param array $options
     *
     * @return \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode
     * @throws \Exception
     * @see PublicationInterface::publish_content_object()
     */
    public function publishContentObject(ContentObject $contentObject, LocationSupport $location, $options = array())
    {
        $publication =
            $this->getPublicationService()->findPublicationByIdentifier($location->getPublicationIdentifier());

        $isPublication = $publication instanceof Publication;
        $isPublisher = $publication->get_publisher_id() == $location->getUserIdentifier();

        if ($isPublication && $isPublisher)
        {
            $portfolioContentObject = $publication->get_content_object();
            $portfolioPath = $portfolioContentObject->get_complex_content_object_path();
            $rootNode = $portfolioPath->get_root();

            if ($rootNode->forms_cycle_with($contentObject->getId()))
            {
                return false;
            }

            if (!$contentObject instanceof Portfolio)
            {
                /**
                 * @var \Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\PortfolioItem $newObject
                 */
                $newObject = ContentObject::factory(PortfolioItem::class_name());
                $newObject->set_owner_id($location->getUserIdentifier());
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
                    new \Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\ComplexPortfolioItem();
            }

            $wrapper->set_ref($newObject->getId());
            $wrapper->set_parent($portfolioContentObject->getId());
            $wrapper->set_user_id($location->getUserIdentifier());
            $wrapper->set_display_order(
                \Chamilo\Core\Repository\Storage\DataManager::select_next_display_order(
                    $portfolioContentObject->getId()
                )
            );

            if (!$wrapper->create())
            {
                return false;
            }
            else
            {
                Event::trigger(
                    'Activity', \Chamilo\Core\Repository\Manager::context(), array(
                        Activity::PROPERTY_TYPE => Activity::ACTIVITY_ADD_ITEM,
                        Activity::PROPERTY_USER_ID => $location->getUserIdentifier(), Activity::PROPERTY_DATE => time(),
                        Activity::PROPERTY_CONTENT_OBJECT_ID => $portfolioContentObject->getId(),
                        Activity::PROPERTY_CONTENT => $portfolioContentObject->get_title() . ' > ' .
                            $contentObject->get_title()
                    )
                );

                $currentParentsContentObjectIds = $rootNode->get_parents_content_object_ids(true, true);
                $currentParentsContentObjectIds[] = $contentObject->getId();

                $portfolioPath->reset();
                $portfolioPath = $portfolioContentObject->get_complex_content_object_path();

                return $portfolioPath->follow_path_by_content_object_ids($currentParentsContentObjectIds);
            }
        }
        else
        {
            return false;
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