<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Location;
use Chamilo\Application\Portfolio\Service\PublicationService;
use Chamilo\Application\Portfolio\Storage\DataClass\Publication;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Repository\Publication\Location\Locations;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationAggregator implements PublicationAggregatorInterface
{
    /**
     * @var \Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service\PublicationAttributesGenerator
     */
    private $publicationAttributesGenerator;

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
     * PublicationAggregator constructor.
     *
     * @param \Chamilo\Application\Portfolio\Service\PublicationService $publicationService
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service\PublicationAttributesGenerator $publicationAttributesGenerator
     */
    public function __construct(
        PublicationService $publicationService, Translator $translator,
        PublicationAttributesGenerator $publicationAttributesGenerator
    )
    {
        $this->publicationService = $publicationService;
        $this->translator = $translator;
        $this->publicationAttributesGenerator = $publicationAttributesGenerator;
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
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return boolean
     */
    public function deleteContentObjectPublications(ContentObject $contentObject)
    {
        return $this->getPublicationService()->deletePublicationsForContentObject($contentObject);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\Repository\Publication\Location\Locations
     */
    public function getContentObjectPublicationLocations(ContentObject $contentObject, User $user)
    {
        $locations = new Locations('Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication');
        $allowedTypes = Portfolio::get_allowed_types();

        $userPublication = $this->getPublicationService()->findPublicationForUser($user);

        $type = $contentObject->get_type();

        if (in_array($type, $allowedTypes) && $userPublication instanceof Publication)
        {
            $locations->add_location(
                new Location(
                    'Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication',
                    $this->getTranslator()->trans('TypeName', [], \Chamilo\Application\Portfolio\Manager::context()),
                    $user->getId(), $userPublication->getId()
                )
            );
        }

        return array($locations);
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
            $objectIdentifier, $type, $condition, $count, $offset, $orderProperties
        );

        $publicationAttributes = array();

        foreach ($publicationRecords as $publicationRecord)
        {
            $publicationAttributes[] = $this->createContentObjectPublicationAttributesFromRecord($publicationRecord);
        }

        return $publicationAttributes;
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
}