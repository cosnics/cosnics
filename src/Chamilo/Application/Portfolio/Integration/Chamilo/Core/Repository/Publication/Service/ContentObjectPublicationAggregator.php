<?php

namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Location;
use Chamilo\Application\Portfolio\Service\PublicationService;
use Chamilo\Application\Portfolio\Storage\DataClass\Publication;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\ComplexPortfolio;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\PortfolioItem;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Publication\Location\Locations;
use Chamilo\Core\Repository\Publication\LocationSupport;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Publication\Service\ContentObjectPublicationAggregatorInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectPublicationAggregator implements ContentObjectPublicationAggregatorInterface
{

    /**
     * @var \Chamilo\Application\Portfolio\Service\PublicationService
     */
    private $publicationService;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * ContentObjectPublicationAggregator constructor.
     *
     * @param \Chamilo\Application\Portfolio\Service\PublicationService $publicationService
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(PublicationService $publicationService, Translator $translator)
    {
        $this->publicationService = $publicationService;
        $this->translator = $translator;
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     */
    public function addContentObjectPublicationAttributesElementsToForm(FormValidator $formValidator)
    {
        // TODO: Please implement me !
    }

    /**
     * @param integer[] $contentObjectIdentifiers
     *
     * @return boolean
     */
    public function areContentObjectsPublished(array $contentObjectIdentifiers)
    {
        $publicationCount =
            $this->getPublicationService()->countPublicationsForContentObjectIdentifiers($contentObjectIdentifiers);

        return $publicationCount > 0;
    }

    /**
     * @param integer $contentObjectIdentifier
     *
     * @return boolean
     */
    public function canContentObjectBeEdited(int $contentObjectIdentifier)
    {
        return true;
    }

    /**
     * Returns whether or not a content object can be unlinked
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function canContentObjectBeUnlinked(ContentObject $contentObject)
    {
        return true;
    }

    /**
     * @param integer $type
     * @param integer $objectIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countPublicationAttributes(
        int $type = PublicationInterface::ATTRIBUTES_TYPE_OBJECT, int $objectIdentifier = null,
        Condition $condition = null
    )
    {
        return $this->getPublicationService()->countPublicationsForTypeAndIdentifier(
            $type, $objectIdentifier, $condition
        );
    }

    /**
     * @param string[] $record
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes
     */
    protected function createContentObjectPublicationAttributesFromRecord($record)
    {
        $attributes = new Attributes();

        $attributes->setId($record[Publication::PROPERTY_ID]);
        $attributes->set_publisher_id($record[Publication::PROPERTY_PUBLISHER_ID]);
        $attributes->set_date($record[Publication::PROPERTY_PUBLISHED]);
        $attributes->set_application(\Chamilo\Application\Portfolio\Manager::context());

        $attributes->set_location(
            $this->getTranslator()->trans('TypeName', [], \Chamilo\Application\Portfolio\Manager::context())
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Portfolio\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Application\Portfolio\Manager::ACTION_HOME,
                \Chamilo\Application\Portfolio\Manager::PARAM_USER_ID => $record[Publication::PROPERTY_PUBLISHER_ID]
            )
        );

        $attributes->set_url($redirect->getUrl());
        $attributes->set_title($record[ContentObject::PROPERTY_TITLE]);
        $attributes->set_content_object_id($record[Publication::PROPERTY_CONTENT_OBJECT_ID]);

        return $attributes;
    }

    /**
     * @param $publicationIdentifier
     *
     * @return bool
     */
    public function deleteContentObjectPublication($publicationIdentifier)
    {
        return $this->getPublicationService()->deletePublicationByIdentifier($publicationIdentifier);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return boolean
     */
    public function deleteContentObjectPublications(ContentObject $contentObject)
    {
        return $this->getPublicationService()->deletePublicationsForContentObject($contentObject);
    }

    /**
     * @param integer $publicationIdentifier
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes
     * @throws \Exception
     */
    public function getContentObjectPublicationAttributes(int $publicationIdentifier)
    {
        return $this->createContentObjectPublicationAttributesFromRecord(
            $this->getPublicationService()->findPublicationRecordByIdentifier($publicationIdentifier)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\Repository\Publication\Location\Locations
     */
    public function getContentObjectPublicationLocations(ContentObject $contentObject, User $user)
    {
        $locations = new Locations(__NAMESPACE__);
        $allowedTypes = Portfolio::get_allowed_types();

        $userPublication = $this->getPublicationService()->findPublicationForUser($user);

        $type = $contentObject->get_type();

        if (in_array($type, $allowedTypes) && $userPublication instanceof Publication)
        {
            $locations->add_location(
                new Location(
                    __NAMESPACE__,
                    $this->getTranslator()->trans('TypeName', [], \Chamilo\Application\Portfolio\Manager::context()),
                    $user->getId(),
                    $userPublication->getId()
                )
            );
        }

        return $locations;
    }

    /**
     * @param integer $type
     * @param integer $objectIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperties
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes[]
     */
    public function getContentObjectPublicationsAttributes(
        int $type = PublicationInterface::ATTRIBUTES_TYPE_OBJECT, int $objectIdentifier, Condition $condition = null,
        int $count = null, int $offset = null, array $orderProperties = null
    )
    {
        $publicationRecords = $this->getPublicationService()->findPublicationRecordsForTypeAndIdentifier(
            $objectIdentifier, $type, $condition, $count,
            $offset, $orderProperties
        );

        $publicationAttributes = array();

        foreach ($publicationRecords as $publicationRecord)
        {
            $publicationAttributes[] = $this->createContentObjectPublicationAttributesFromRecord($publicationRecord);
        }

        return $publicationAttributes;
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
    public function setPublicationService(PublicationService $publicationService
    ): void
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
     * @param integer $contentObjectIdentifier
     *
     * @return boolean
     */
    public function isContentObjectPublished(int $contentObjectIdentifier)
    {
        $publicationCount =
            $this->getPublicationService()->countPublicationsForContentObjectIdentifier($contentObjectIdentifier);

        return $publicationCount > 0;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Location $location
     * @param array $options
     *
     * @return \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode
     * @throws \Exception
     */
    public function publishContentObject(ContentObject $contentObject, LocationSupport $location, $options = array()
    )
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
                    'Activity',
                    \Chamilo\Core\Repository\Manager::context(),
                    array(
                        Activity::PROPERTY_TYPE => Activity::ACTIVITY_ADD_ITEM,
                        Activity::PROPERTY_USER_ID => $location->getUserIdentifier(),
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
    public function updateContentObjectPublicationIdentifier(Attributes $publicationAttributes)
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