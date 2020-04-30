<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Infrastructure\Repository;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Interface for a course copier repository
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseCopierRepository implements CourseCopierRepositoryInterface
{

    /**
     * Returns the root course group for a given course
     * 
     * @param int $courseId
     *
     * @return CourseGroup
     */
    public function findRootCourseGroupForCourse($courseId)
    {
        return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager::retrieve_course_group_root(
            $courseId);
    }

    /**
     * Finds publication categories by a given array of category ids, ordered by parent and display order
     * 
     * @param int[] $categoryIds
     *
     * @return ContentObjectPublicationCategory[]
     */
    public function findPublicationCategoriesByIds($categoryIds = array())
    {
        if (empty($categoryIds))
        {
            return array();
        }
        
        $condition = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class,
                ContentObjectPublicationCategory::PROPERTY_ID), 
            $categoryIds);
        
        $order_by = array();
        
        $order_by[] = new OrderBy(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class,
                ContentObjectPublicationCategory::PROPERTY_PARENT));
        
        $order_by[] = new OrderBy(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class,
                ContentObjectPublicationCategory::PROPERTY_DISPLAY_ORDER));
        
        return DataManager::retrieves(
            ContentObjectPublicationCategory::class,
            new DataClassRetrievesParameters($condition, null, null, $order_by))->as_array();
    }

    /**
     * Finds a content object publication by a given id
     * 
     * @param int $contentObjectPublicationId
     *
     * @return ContentObjectPublication
     */
    public function findContentObjectPublicationById($contentObjectPublicationId)
    {
        return DataManager::retrieve_by_id(
            ContentObjectPublication::class,
            $contentObjectPublicationId);
    }

    /**
     * Finds a possible extension object for a given content object publication
     * 
     * @param ContentObjectPublication $contentObjectPublication
     *
     * @return DataClass
     */
    public function findContentObjectPublicationExtension(ContentObjectPublication $contentObjectPublication)
    {
        $possible_publication_class = 'Chamilo\Application\Weblcms\Tool\Implementation\\' .
             $contentObjectPublication->get_tool() . '\\Storage\\DataClass\\Publication';
        
        if (class_exists($possible_publication_class))
        {
            $datamanager_class = 'Chamilo\Application\Weblcms\Tool\Implementation\\' .
                 $contentObjectPublication->get_tool() . '\\Storage\\DataManager';
            
            return $datamanager_class::retrieve(
                $possible_publication_class, 
                new DataClassRetrieveParameters(
                    new EqualityCondition(
                        new PropertyConditionVariable(
                            $possible_publication_class, 
                            $possible_publication_class::PROPERTY_PUBLICATION_ID), 
                        new StaticConditionVariable($contentObjectPublication->getId()))));
        }
        
        return null;
    }
}