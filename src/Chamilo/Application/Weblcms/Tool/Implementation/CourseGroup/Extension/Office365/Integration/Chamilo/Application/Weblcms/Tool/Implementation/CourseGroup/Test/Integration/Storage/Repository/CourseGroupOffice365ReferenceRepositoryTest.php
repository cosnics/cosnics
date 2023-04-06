<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Test\Storage\Repository;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupOffice365Reference;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\Repository\CourseGroupOffice365ReferenceRepository;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloFixturesBasedTestCase;

/**
 * Tests the CourseGroupOffice365ReferenceRepository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupOffice365ReferenceRepositoryTest extends ChamiloFixturesBasedTestCase
{
    /**
     * @var CourseGroupOffice365ReferenceRepository
     */
    protected $courseGroupOffice365ReferenceRepository;

    /**
     * Setup before each test
     */
    protected function setUp(): void    {
        parent::setUp();

        $this->courseGroupOffice365ReferenceRepository =
            new CourseGroupOffice365ReferenceRepository($this->getTestDataClassRepository());
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void    {
        parent::tearDown();

        unset($this->courseGroupOffice365ReferenceRepository);
    }

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
            'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup' =>
                ['course_group_office365_reference']
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
            'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup' =>
                ['CourseGroupOffice365Reference']
        ];
    }

    public function testFindByCourseGroup()
    {
        $courseGroup = new CourseGroup();
        $courseGroup->setId(4);

        $reference = $this->courseGroupOffice365ReferenceRepository->findByCourseGroup($courseGroup);

        $this->assertInstanceOf(CourseGroupOffice365Reference::class, $reference);
    }

    public function testCreateReference()
    {
        $courseGroup = new CourseGroup();
        $courseGroup->setId(8);

        $reference = new CourseGroupOffice365Reference();
        $reference->setCourseGroupId(8);
        $reference->setOffice365GroupId('AERASDK-QSDFAERWXV-AEZRJMQSDFQS');

        $this->courseGroupOffice365ReferenceRepository->createReference($reference);

        $reference = $this->courseGroupOffice365ReferenceRepository->findByCourseGroup($courseGroup);
        $this->assertInstanceOf(CourseGroupOffice365Reference::class, $reference);
    }

}

