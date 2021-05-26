<?php
namespace Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PublicationEntityServiceManager
{
    /**
     * @var PublicationEntityServiceInterface[]
     */
    protected $entityServicesByType;

    /**
     * @var ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * EvaluationEntityServiceManager constructor.
     */
    public function __construct()
    {
        $this->entityServicesByType = [];
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     */
    public function setContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        $this->contentObjectPublication = $contentObjectPublication;
    }

    /**
     * @return ContentObjectPublication
     */
    public function getContentObjectPublication(): ContentObjectPublication
    {
        return $this->contentObjectPublication;
    }

    /**
     * @param int $entityType
     *
     * @param PublicationEntityServiceInterface $entityService
     */
    public function addEntityService(int $entityType, PublicationEntityServiceInterface $entityService)
    {
        $this->entityServicesByType[$entityType] = $entityService;
    }

    /**
     * @param int $entityType
     *
     * @return PublicationEntityServiceInterface|mixed
     */
    public function getEntityServiceByType(int $entityType)
    {
        if (!array_key_exists($entityType, $this->entityServicesByType))
        {
            throw new \InvalidArgumentException(sprintf('The given entityType %s is not supported', $entityType));
        }

        return $this->entityServicesByType[$entityType];
    }
}
