<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\Repository;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\DataClass\Publication;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationRepository extends CommonDataClassRepository
{
    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\DataClass\Publication|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findPublicationByContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($contentObjectPublication->getId())
        );

        return $this->dataClassRepository->retrieve(Publication::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @param int[] $contentObjectPublicationIdentifiers
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator | \Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\DataClass\Publication[]
     */
    public function findPublicationsByContentObjectPublicationIdentifiers($contentObjectPublicationIdentifiers)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLICATION_ID),
            $contentObjectPublicationIdentifiers
        );

        return $this->dataClassRepository->retrieves(Publication::class, new DataClassRetrievesParameters($condition));
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return bool
     */
    public function deletePublicationForContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($contentObjectPublication->getId())
        );

        return $this->dataClassRepository->deletes(Publication::class, $condition);
    }

    /**
     * @param Publication $publication
     * @param bool $openForStudents
     * @return Publication
     * @throws \Exception
     */
    public function setPublicationOpenForStudents(Publication $publication, bool $openForStudents): Publication
    {
        $publication->setOpenForStudents($openForStudents);
        $publication->update();
        return $publication;
    }
}