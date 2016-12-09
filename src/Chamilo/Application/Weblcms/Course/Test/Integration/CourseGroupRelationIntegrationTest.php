<?php
namespace Chamilo\Application\Weblcms\Course\Test\Integration;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseGroupRelation;
use Chamilo\Libraries\Storage\DataClassIntegrationTestCase;

/**
 *
 * @author Anthony Hurst (Hogeschool Gent)
 */
class CourseGroupRelationIntegrationTest extends DataClassIntegrationTestCase
{
    // /******************************************************************************************************************/
    // /* Data Providers */
    // /******************************************************************************************************************/
    //
    // public function providerStandardData()
    // {
    // //array($group_id, $course_id, $status);
    // $data = array();
    // $data[] = array(2, 3, CourseGroupRelation :: STATUS_STUDENT);
    // $data[] = array(3, 1, CourseGroupRelation :: STATUS_STUDENT);
    // $data[] = array(9, 10, CourseGroupRelation :: STATUS_TEACHER);
    // return $data;
    // }
    //
    // public function providerStandardDataId()
    // {
    // $data = array();
    // $sets = $this->providerStandardData();
    //
    // $id = 1;
    // foreach ($sets as $set)
    // {
    // $data[] = array_merge(array($id), $set);
    // $id++;
    // }
    // return $data;
    // }
    //
    // /******************************************************************************************************************/
    // /* Helper methods */
    // /******************************************************************************************************************/
    //
    // /**
    // * Creates an instance of CourseGroupRelation from the values passed.
    // * @static
    // * @param int $id The id for the CourseGroupRelation. Not normally set.
    // * @param int $group_id The id of the group referenced by the CourseGroupRelation.
    // * @param int $course_id The id of the course for which the group is being registered.
    // * @param int $status The type of registration awarded to the group. Form: CourseGroupRelation :: STATUS_*.
    // * @return CourseGroupRelation The constructed CourseGroupRelation.
    // */
    // private static function createCourseGroupRelation($id = null, $group_id = null, $course_id = null, $status =
    // null)
    // {
    // $course_group_relation = new CourseGroupRelation();
    // if ($id !== null)
    // {
    // $course_group_relation->set_id($id);
    // }
    // if ($group_id !== null)
    // {
    // $course_group_relation->set_group_id($group_id);
    // }
    // if ($course_id !== null)
    // {
    // $course_group_relation->set_course_id($course_id);
    // }
    // if ($status !== null)
    // {
    // $course_group_relation->set_status($status);
    // }
    // return $course_group_relation;
    // }
    //
    // /**
    // * Modifies the properties of an entity.
    // * @static
    // * @param CourseGroupRelation $entity The entity whose properties are to be modified.
    // * @param boolean $alter_id Whether the id must be modified or not.
    // */
    protected static function modifyEntity(&$entity, $alter_id)
    {
        if ($alter_id)
        {
            $entity->set_id($entity->get_id() + 5);
        }
        $entity->set_group_id($entity->get_group_id() + 1);
        $entity->set_course_id($entity->get_course_id() + 2);
        $new_status = $entity->get_status() === CourseGroupRelation::STATUS_TEACHER ? CourseGroupRelation::STATUS_STUDENT : CourseGroupRelation::STATUS_TEACHER;
        $entity->set_status($new_status);
    }
    //
    // /**
    // * Retrieves a CourseGroupRelation from the database by its id.
    // * @static
    // * @param int $id The id of the CourseGroupRelation being retrieved.
    // * @return CourseGroupRelation The CourseGroupRelation from the database or null if not found.
    // */
    // private static function retrieveCourseGroupRelation($id)
    // {
    // return self :: retrieveEntity(CourseGroupRelation :: class_name(), $id);
    // }
    //
    // /******************************************************************************************************************/
    // /* Test methods */
    // /******************************************************************************************************************/
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreate($group_id, $course_id, $status)
    // {
    // $course_group_relation = self :: createCourseGroupRelation(null, $group_id, $course_id, $status);
    // $this->assertTrue($course_group_relation->create());
    // $this->registerPersistedEntity($course_group_relation);
    // }
    //
    // /**
    // * @dataProvider providerStandardDataId
    // */
    // public function testCreateId($id, $group_id, $course_id, $status)
    // {
    // $course_group_relation = self :: createCourseGroupRelation($id, $group_id, $course_id, $status);
    // $this->assertTrue($course_group_relation->create());
    // $this->registerPersistedEntity($course_group_relation);
    // $this->assertNotEquals($id, $course_group_relation->get_id());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreateNoCourseId($group_id, $course_id, $status)
    // {
    // $course_group_relation = self :: createCourseGroupRelation(null, $group_id, null, $status);
    // $persisted = $course_group_relation->create();
    // if ($persisted)
    // {
    // $this->registerPersistedEntity($course_group_relation);
    // }
    // $this->assertFalse($persisted);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreateNoStatus($group_id, $course_id, $status)
    // {
    // $course_group_relation = self :: createCourseGroupRelation(null, $group_id, $course_id);
    //
    // $this->assertTrue($course_group_relation->create());
    // $this->registerPersistedEntity($course_group_relation);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testCreate
    // */
    // public function testRead($group_id, $course_id, $status)
    // {
    // $original = self :: createCourseGroupRelation(null, $group_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseGroupRelation($original->get_id());
    // $this->assertInstanceOf(CourseGroupRelation :: class_name(), $read);
    //
    // $message = self :: createErrorMessage('$id', $original->get_id(), $read->get_id());
    // $this->assertSame($original->get_id(), $read->get_id(), $message);
    // $expected_group_id = $original->get_group_id() !== null ? $original->get_group_id() : 0;
    // $message = self :: createErrorMessage('$group_id', $expected_group_id, $read->get_group_id());
    // $this->assertSame($expected_group_id, $read->get_group_id(), $message);
    // $message = self :: createErrorMessage('$course_id', $original->get_course_id(), $read->get_course_id());
    // $this->assertSame($original->get_course_id(), $read->get_course_id(), $message);
    // $expected_status =
    // $original->get_status() !== null ? $original->get_status() : CourseGroupRelation :: STATUS_STUDENT;
    // $message = self :: createErrorMessage('$status', $expected_status, $read->get_status());
    // $this->assertSame($expected_status, $read->get_status(), $message);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testReadNoGroupId($group_id, $course_id, $status)
    // {
    // $this->testRead(null, $course_id, $status);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testReadNoStatus($group_id, $course_id, $status)
    // {
    // $this->testRead($group_id, $course_id, null);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdate($group_id, $course_id, $status)
    // {
    // $original = self :: createCourseGroupRelation(null, $group_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseGroupRelation($original->get_id());
    // $this->assertInstanceOf(CourseGroupRelation :: class_name(), $read);
    // self :: modifyEntity($read, false);
    //
    // $this->assertTrue($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoId($group_id, $course_id, $status)
    // {
    // $original = self :: createCourseGroupRelation(null, $group_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseGroupRelation($original->get_id());
    // $this->assertInstanceOf(CourseGroupRelation :: class_name(), $read);
    // self :: modifyEntity($read, false);
    // $read->set_id(null);
    //
    // $this->assertFalse($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoGroupId($group_id, $course_id, $status)
    // {
    // $original = self :: createCourseGroupRelation(null, $group_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseGroupRelation($original->get_id());
    // $this->assertInstanceOf(CourseGroupRelation :: class_name(), $read);
    // self :: modifyEntity($read, false);
    // $read->set_group_id(null);
    //
    // $this->assertTrue($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoCourseId($group_id, $course_id, $status)
    // {
    // $original = self :: createCourseGroupRelation(null, $group_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseGroupRelation($original->get_id());
    // $this->assertInstanceOf(CourseGroupRelation :: class_name(), $read);
    // self :: modifyEntity($read, false);
    // $read->set_course_id(null);
    //
    // $this->assertFalse($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoStatus($group_id, $course_id, $status)
    // {
    // $original = self :: createCourseGroupRelation(null, $group_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseGroupRelation($original->get_id());
    // $this->assertInstanceOf(CourseGroupRelation :: class_name(), $read);
    // self :: modifyEntity($read, false);
    // $read->set_status(null);
    //
    // $this->assertTrue($original->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testUpdate
    // */
    // public function testReread($group_id, $course_id, $status, $method = null)
    // {
    // $original = self :: createCourseGroupRelation(null, $group_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseGroupRelation($original->get_id());
    // $this->assertInstanceOf(CourseGroupRelation :: class_name(), $read);
    // self :: modifyEntity($read, false);
    // if ($method !== null)
    // {
    // $read->$method(null);
    // }
    // $this->assertTrue($read->update());
    //
    // $reread = self :: retrieveCourseGroupRelation($read->get_id());
    // $this->assertInstanceOf(CourseGroupRelation :: class_name(), $reread);
    //
    // $message = self :: createErrorMessage('$id', $read->get_id(), $reread->get_id());
    // $this->assertSame($read->get_id(), $reread->get_id(), $message);
    // $message = self :: createErrorMessage('$group_id', $read->get_group_id(), $reread->get_group_id());
    // $this->assertSame($read->get_group_id(), $reread->get_group_id(), $message);
    // $message = self :: createErrorMessage('$course_id', $read->get_course_id(), $reread->get_course_id());
    // $this->assertSame($read->get_course_id(), $reread->get_course_id(), $message);
    // $expected_status = $read->get_status() !== null ? $read->get_status() : CourseGroupRelation :: STATUS_STUDENT;
    // $message = self :: createErrorMessage('$status', $expected_status, $reread->get_status());
    // $this->assertSame($read->get_status(), $expected_status, $message);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testReread
    // */
    // public function testRereadNoGroupId($group_id, $course_id, $status)
    // {
    // $method = 'set_group_id';
    // $this->testReread($group_id, $course_id, $status, $method);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testReread
    // */
    // public function testRereadNoStatus($group_id, $course_id, $status)
    // {
    // $method = 'set_status';
    // $this->testReread($group_id, $course_id, $status, $method);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testCreate
    // */
    // public function testDelete($group_id, $course_id, $status)
    // {
    // $course_group_relation = self :: createCourseGroupRelation(null, $group_id, $course_id, $status);
    // $this->assertTrue($course_group_relation->create());
    // $persisted = $course_group_relation->delete();
    // if (!$persisted)
    // {
    // $this->registerPersistedEntity($course_group_relation);
    // }
    // $this->assertTrue($persisted);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testSaveCreate($group_id, $course_id, $status)
    // {
    // $original = self :: createCourseGroupRelation(null, $group_id, $course_id, $status);
    // $this->assertTrue($original->save());
    // $this->registerPersistedEntity($original);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testSaveCreateId($id, $group_id, $course_id, $status)
    // {
    // $original = self :: createCourseGroupRelation($id, $group_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->assertNotEquals($id, $original->get_id());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testCreate
    // */
    // public function testSaveUpdate($group_id, $course_id, $status)
    // {
    // $original = self :: createCourseGroupRelation(null, $group_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseGroupRelation($original->get_id());
    // $this->assertInstanceOf(CourseGroupRelation :: class_name(), $read);
    // self :: modifyEntity($read, false);
    //
    // $this->assertTrue($read->save());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testCreate
    // */
    // public function testSaveUpdateNoId($group_id, $course_id, $status)
    // {
    // $original = self :: createCourseGroupRelation(null, $group_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseGroupRelation($original->get_id());
    // $this->assertInstanceOf(CourseGroupRelation :: class_name(), $read);
    // self :: modifyEntity($read, false);
    // $read->set_id(null);
    //
    // $this->assertTrue($read->save());
    //
    // $message = self :: createErrorMessage('$id', '!' . $original->get_id(), $read->get_id());
    // $this->assertNotEquals($original->get_id(), $read->get_id(), $message);
    //
    // $this->registerPersistedEntity($read);
    // }
}
