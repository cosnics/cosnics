<?php

namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Application\Portfolio\Service\PublicationService;
use Chamilo\Application\Portfolio\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Publication\Service\ContentObjectPublicationManagerInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Symfony\Component\Translation\Translator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * @package Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectPublicationManager implements ContentObjectPublicationManagerInterface
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
     * ContentObjectPublicationManager constructor.
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
     * @param integer $contentObjectIdentifier
     *
     * @return boolean
     */
    public function canContentObjectBeEdited(int $contentObjectIdentifier)
    {
        return true;
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
     * @param integer $objectIdentifier
     * @param integer $type
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperties
     *
     * @return string[]
     */
    public function getContentObjectPublicationsAttributes(
        int $type = PublicationInterface::ATTRIBUTES_TYPE_OBJECT, int $objectIdentifier, Condition $condition = null,
        int $count = null, int $offset = null, $orderProperties = null
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
     * @param string[] $record
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes
     */
    protected function createContentObjectPublicationAttributesFromRecord($record)
    {
        $attributes = new \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes();

        $attributes->setId($record[Publication::PROPERTY_ID]);
        $attributes->set_publisher_id($record[Publication::PROPERTY_PUBLISHER_ID]);
        $attributes->set_date($record[Publication::PROPERTY_PUBLISHED]);
        $attributes->set_application(\Chamilo\Application\Portfolio\Manager::context());

        $attributes->set_location(
            $this->getTranslator()->trans('TypeName', [], \Chamilo\Application\Portfolio\Manager::context())
        );

        // TODO: Fix this using a Redirect instance
        $url =
            'index.php?application=portfolio&amp;go=' . \Chamilo\Application\Portfolio\Manager::ACTION_HOME . '&amp;' .
            \Chamilo\Application\Portfolio\Manager::PARAM_USER_ID . '=' . $record[Publication::PROPERTY_PUBLISHER_ID];

        $attributes->set_url($url);
        $attributes->set_title($record[ContentObject::PROPERTY_TITLE]);
        $attributes->set_content_object_id($record[Publication::PROPERTY_CONTENT_OBJECT_ID]);

        return $attributes;
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

    // TODO: CONTINUE HERE
    public function deleteContentObjectPublications($object_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($object_id)
        );
        $parameters = new DataClassRetrievesParameters($condition);

        $publications = DataManager::retrieves(Publication::class_name(), $parameters);

        while ($publication = $publications->next_result())
        {
            if (!$publication->delete())
            {
                return false;
            }
        }

        return true;
    }

    public function deleteContentObjectPublication($publication_id)
    {
        $publication = DataManager::retrieve_by_id(Publication::class_name(), $publication_id);

        if ($publication instanceof Publication && $publication->delete())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getContentObjectPublicationLocations($content_object, $user = null)
    {
        $applicationContext = \Chamilo\Application\Portfolio\Manager::context();

        $locations = new Locations(__NAMESPACE__);
        $allowed_types = Portfolio::get_allowed_types();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_PUBLISHER_ID),
            new StaticConditionVariable($user->get_id())
        );
        $userPublication =
            DataManager::retrieve(Publication::class_name(), new DataClassRetrieveParameters($condition));

        $type = $content_object->get_type();

        if (in_array($type, $allowed_types) && $userPublication instanceof Publication)
        {
            $locations->add_location(
                new Location(
                    __NAMESPACE__,
                    Translation::get('TypeName', null, $applicationContext),
                    $user->getId(),
                    $userPublication->getId()
                )
            );
        }

        return $locations;
    }

    public function publishContentObject(
        \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject, LocationSupport $location,
        $options = array()
    )
    {
        $publication = DataManager::retrieve_by_id(Publication::class_name(), $location->getPublicationIdentifier());

        if ($publication instanceof Publication && $publication->get_publisher_id() == $location->getUserIdentifier())
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

            $wrapper->set_ref($newObject->get_id());
            $wrapper->set_parent($portfolioContentObject->get_id());
            $wrapper->set_user_id($location->getUserIdentifier());
            $wrapper->set_display_order(
                \Chamilo\Core\Repository\Storage\DataManager::select_next_display_order(
                    $portfolioContentObject->get_id()
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
                        Activity::PROPERTY_CONTENT_OBJECT_ID => $portfolioContentObject->get_id(),
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

    public function addContentObjectPublicationAttributesElementsToForm($form)
    {
        // TODO: Please implement me !
    }

    public function updateContentObjectPublicationIdentifier($publication_attributes)
    {
        $publication = DataManager::retrieve_by_id(Publication::class_name(), $publication_attributes->get_id());

        if ($publication instanceof Publication)
        {
            $publication->set_content_object_id($publication_attributes->get_content_object_id());

            return $publication->update();
        }
        else
        {
            return false;
        }

        return DataManager::update_content_object_publication_id($publication_attributes);
    }
}