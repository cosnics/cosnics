<?php
namespace Chamilo\Application\Weblcms\Test\Integration\Service;

use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloFixturesBasedTestCase;

/**
 * @package Chamilo\Application\Weblcms\Test\Integration\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupEventListenerTest extends ChamiloFixturesBasedTestCase
{

    /**
     * Returns the storage units that need to be created. This method requires a multidimensional array with the
     * names of the storage units per context
     *
     * [ $context => [$storageUnit1, $storageUnit2] ]
     *
     * @return array
     */
    protected function getStorageUnitsToCreate()
    {
        return [
            'Chamilo\Application\Weblcms' => [
                'course', 'course_entity_relation', 'rights_location', 'rights_location_entity_right', 'course_group',
                'course_group_user_relation'
            ],
            'Chamilo\Core\Group' => [
                'group', 'group_rel_user'
            ],
            'Chamilo\Core\User' => [
                'user'
            ]
        ];
    }

    /**
     * Returns the fixture files that need to be inserted. This method requires a multidimensional array with the
     * names of the fixture files per context
     *
     * [ $context => [$fixtureFileName1, $fixtureFileName2] ]
     *
     * @return array
     */
    protected function getFixtureFiles()
    {
        return [
            'Chamilo\Core\User' => [
                'User'
            ],
            'Chamilo\Core\Group' => [
                'Group', 'GroupUserRelation'
            ],
            'Chamilo\Application\Weblcms' => [
                'Course', 'CourseEntityRelation', 'CourseGroup', 'CourseGroupUserRelation', 'RightsLocation',
                'RightsLocationEntityRight'
            ],
        ];
    }

    public function testBase()
    {
        $this->assertTrue(true);
    }

}