<?php
namespace Chamilo\Application\Survey\Service;

use Chamilo\Application\Survey\Repository\EntityRelationRepository;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Application\Survey\Storage\DataClass\PublicationEntityRelation;

/**
 *
 * @package Chamilo\Application\Survey\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityRelationService
{

    /**
     *
     * @var \Chamilo\Application\Survey\Repository\EntityRelationRepository
     */
    private $entityRelationRepository;

    /**
     *
     * @param \Chamilo\Application\Survey\Repository\EntityRelationRepository $entityRelationRepository
     */
    public function __construct(EntityRelationRepository $entityRelationRepository)
    {
        $this->entityRelationRepository = $entityRelationRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Survey\Repository\EntityRelationRepository
     */
    public function getEntityRelationRepository()
    {
        return $this->entityRelationRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Repository\EntityRelationRepository $entityRelationRepository
     */
    public function setEntityRelationRepository($entityRelationRepository)
    {
        $this->entityRelationRepository = $entityRelationRepository;
    }

    /**
     *
     * @param integer[] $entities
     * @param integer $right
     * @param Chamilo\Application\Survey\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function hasRight($entities, $right, Publication $publication)
    {
        return $this->getEntityRelationRepository()->findEntitiesWithRight($entities, $right, $publication);
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Storage\DataClass\Publication $publication
     * @param integer[] $selectedEntityTypeIdentifiers
     * @param integer $right
     * @return boolean
     */
    public function setEntityRelations(Publication $publication, $selectedEntityTypeIdentifiers, $right)
    {
        foreach ($selectedEntityTypeIdentifiers as $entityType => $entityIdentifiers)
        {
            foreach ($entityIdentifiers as $entityIdentifier)
            {
                $entityRelation = $this->getEntityRelationForPublicationEntityTypeAndIdentifier(
                    $publication,
                    $entityType,
                    $entityIdentifier);

                if ($entityRelation instanceof PublicationEntityRelation)
                {
                    $success = $this->updateEntityRelation(
                        $entityRelation,
                        $publication->getId(),
                        $entityType,
                        $entityIdentifier,
                        $right);
                }
                else
                {
                    $success = $this->createEntityRelation($publication->getId(), $entityType, $entityIdentifier, $right);
                }

                if (! $success)
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     *
     * @param integer $publicationId
     * @param integer $entityType
     * @param integer $entityIdentifier
     * @param integer $right
     * @return boolean
     */
    public function createEntityRelation($publicationId, $entityType, $entityIdentifier, $right)
    {
        $publicationEntityRelation = new PublicationEntityRelation();
        $this->setEntityRelationProperties(
            $publicationEntityRelation,
            $publicationId,
            $entityType,
            $entityIdentifier,
            $right);

        if (! $publicationEntityRelation->create())
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Storage\DataClass\PublicationEntityRelation $publicationEntityRelation
     * @param integer $publicationId
     * @param integer $entityType
     * @param integer $entityIdentifier
     * @param integer $right
     * @return boolean
     */
    public function updateEntityRelation(PublicationEntityRelation $publicationEntityRelation, $publicationId, $entityType,
        $entityIdentifier, $right)
    {
        $this->setEntityRelationProperties(
            $publicationEntityRelation,
            $publicationId,
            $entityType,
            $entityIdentifier,
            $right);

        if (! $publicationEntityRelation->update())
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Storage\DataClass\PublicationEntityRelation $publicationEntityRelation
     * @param integer $publicationId
     * @param integer $entityType
     * @param integer $entityIdentifier
     * @param integer $right
     */
    private function setEntityRelationProperties(PublicationEntityRelation $publicationEntityRelation, $publicationId,
        $entityType, $entityIdentifier, $right)
    {
        $publicationEntityRelation->setPublicationId($publicationId);
        $publicationEntityRelation->setEntityType($entityType);
        $publicationEntityRelation->setEntityId($entityIdentifier);
        $publicationEntityRelation->setRights($right);
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Storage\DataClass\Publication $publication
     * @param integer $entityType
     * @param integer $entityIdentifier
     * @return \Chamilo\Application\Survey\Storage\DataClass\PublicationEntityRelation
     */
    public function getEntityRelationForPublicationEntityTypeAndIdentifier(Publication $publication, $entityType,
        $entityIdentifier)
    {
        return $this->getEntityRelationRepository()->findEntityRelationForPublicationEntityTypeAndIdentifier(
            $publication,
            $entityType,
            $entityIdentifier);
    }

    /**
     *
     * @param integer $identifier
     * @return \Chamilo\Application\Survey\Storage\DataClass\PublicationEntityRelation
     */
    public function getEntityRelationByIdentifier($identifier)
    {
        return $this->getEntityRelationRepository()->findEntityRelationByIdentifier($identifier);
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Storage\DataClass\PublicationEntityRelation $entityRelation
     * @return boolean
     */
    public function deleteEntityRelation(PublicationEntityRelation $entityRelation)
    {
        if (! $entityRelation->delete())
        {
            return false;
        }

        return true;
    }
}