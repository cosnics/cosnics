<?php
namespace Chamilo\Application\Weblcms\Course\Test\Integration;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseUserRelation;
use Chamilo\Libraries\Storage\DataClassIntegrationTestCase;

/**
 *
 * @author Anthony Hurst (Hogeschool Gent)
 */
class CourseUserRelationIntegrationTest extends DataClassIntegrationTestCase
{
    // /******************************************************************************************************************/
    // /* Data providers */
    // /******************************************************************************************************************/
    //
    // public function providerStandardData()
    // {
    // //array($user_id, $course_id, $status);
    // $data = array();
    // $data[] = array(2, 3, CourseUserRelation :: STATUS_STUDENT);
    // $data[] = array(3, 1, CourseUserRelation :: STATUS_STUDENT);
    // $data[] = array(9, 10, CourseUserRelation :: STATUS_TEACHER);
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
    // * Creates an instance of CourseUserRelation from the values passed.
    // * @param int $id The id for the CourseUserRelation. Not normally set.
    // * @param int $user_id The id of the user referenced by the CourseUserRelation.
    // * @param int $course_id The id of the course for which the user is being registered.
    // * @param int $status The type of registration awarded to the user. Form: CourseUserRelation :: STATUS_*.
    // * @return CourseUserRelation The constructed CourseUserRelation.
    // */
    // private static function createCourseUserRelation($id = null, $user_id = null, $course_id = null, $status = null)
    // {
    // $course_user_relation = new CourseUserRelation();
    // if ($id !== null)
    // {
    // $course_user_relation->set_id($id);
    // }
    // if ($user_id !== null)
    // {
    // $course_user_relation->set_user_id($user_id);
    // }
    // if ($course_id !== null)
    // {
    // $course_user_relation->set_course_id($course_id);
    // }
    // if ($status !== null)
    // {
    // $course_user_relation->set_status($status);
    // }
    // return $course_user_relation;
    // }
    //
    // /**
    // * Modifies the properties of a CourseUserRelation.
    // * @param CourseUserRelation $entity The CourseUserRelation whose properties are to be modified.
    // * @param bool $alter_id
    // * @return void
    // */
    protected static function modifyEntity(&$entity, $alter_id)
    {
        if ($alter_id)
        {
            $entity->set_id($entity->get_id() + 5);
        }
        $entity->set_user_id($entity->get_user_id() + 1);
        $entity->set_course_id($entity->get_course_id() + 2);
        $new_status = $entity->get_status() === CourseUserRelation :: STATUS_TEACHER ? CourseUserRelation :: STATUS_STUDENT : CourseUserRelation :: STATUS_TEACHER;
        $entity->set_status($new_status);
    }
    //
    // /**
    // * Retrieves a CourseUserRelation from the database by its id.
    // * @param int $id The id of the CourseUserRelation being retrieved.
    // * @return CourseUserRelation The CourseUserRelation from the database or null if none found.
    // */
    // private static function retrieveCourseUserRelation($id)
    // {
    // return self :: retrieveEntity(CourseUserRelation :: class_name(), $id);
    // }
    //
    // /******************************************************************************************************************/
    // /* Test methods */
    // /******************************************************************************************************************/
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreate($user_id, $course_id, $status)
    // {
    // $course_user_relation = self :: createCourseUserRelation(null, $user_id, $course_id, $status);
    // $this->assertTrue($course_user_relation->create());
    // $this->registerPersistedEntity($course_user_relation);
    // }
    //
    // /**
    // * @dataProvider providerStandardDataId
    // */
    // public function testCreateId($id, $user_id, $course_id, $status)
    // {
    // $course_user_relation = self :: createCourseUserRelation($id, $user_id, $course_id, $status);
    // $this->assertTrue($course_user_relation->create());
    // $this->registerPersistedEntity($course_user_relation);
    // $this->assertNotEquals($id, $course_user_relation->get_id());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreateNoCourseId($user_id, $course_id, $status)
    // {
    // $course_user_relation = self :: createCourseUserRelation(null, $user_id, null, $status);
    // $persisted = $course_user_relation->create();
    // if ($persisted)
    // {
    // $this->registerPersistedEntity($course_user_relation);
    // }
    // $this->assertFalse($persisted);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreateNoStatus($user_id, $course_id, $status)
    // {
    // $course_user_relation = self :: createCourseUserRelation(null, $user_id, $course_id);
    // $this->assertTrue($course_user_relation->create());
    // $this->registerPersistedEntity($course_user_relation);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testCreate
    // */
    // public function testRead($user_id, $course_id, $status)
    // {
    // $original = self :: createCourseUserRelation(null, $user_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseUserRelation($original->get_id());
    // $this->assertInstanceOf(CourseUserRelation :: class_name(), $read);
    //
    // $message = self :: createErrorMessage('$id', $original->get_id(), $read->get_id());
    // $this->assertSame($original->get_id(), $read->get_id(), $message);
    // $original_user_id = $original->get_user_id() !== null ? $original->get_user_id() : 0;
    // $message = self :: createErrorMessage('$user_id', $original_user_id, $read->get_user_id());
    // $this->assertSame($original_user_id, $read->get_user_id(), $message);
    // $message = self :: createErrorMessage('$course_id', $original->get_course_id(), $read->get_course_id());
    // $this->assertSame($original->get_course_id(), $read->get_course_id(), $message);
    // $original_status =
    // $original->get_status() !== null ? $original->get_status() : CourseUserRelation :: STATUS_STUDENT;
    // $message = self :: createErrorMessage('$status', $original_status, $read->get_status());
    // $this->assertSame($original_status, $read->get_status(), $message);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testReadNoUserId($user_id, $course_id, $status)
    // {
    // $this->testRead(null, $course_id, $status);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testReadNoStatus($user_id, $course_id, $status)
    // {
    // $this->testRead($user_id, $course_id, null);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdate($user_id, $course_id, $status)
    // {
    // $original = self :: createCourseUserRelation(null, $user_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseUserRelation($original->get_id());
    // $this->assertInstanceOf(CourseUserRelation :: class_name(), $read);
    // self :: modifyEntity($read, false);
    //
    // $this->assertTrue($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoId($user_id, $course_id, $status)
    // {
    // $original = self :: createCourseUserRelation(null, $user_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseUserRelation($original->get_id());
    // $this->assertInstanceOf(CourseUserRelation :: class_name(), $read);
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
    // public function testUpdateNoUserId($user_id, $course_id, $status)
    // {
    // $original = self :: createCourseUserRelation(null, $user_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseUserRelation($original->get_id());
    // $this->assertInstanceOf(CourseUserRelation :: class_name(), $read);
    // self :: modifyEntity($read, false);
    // $read->set_user_id(null);
    //
    // $this->assertTrue($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoCourseId($user_id, $course_id, $status)
    // {
    // $original = self :: createCourseUserRelation(null, $user_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseUserRelation($original->get_id());
    // $this->assertInstanceOf(CourseUserRelation :: class_name(), $read);
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
    // public function testUpdateNoStatus($user_id, $course_id, $status)
    // {
    // $original = self :: createCourseUserRelation(null, $user_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseUserRelation($original->get_id());
    // $this->assertInstanceOf(CourseUserRelation :: class_name(), $read);
    // self :: modifyEntity($read, false);
    // $read->set_status(null);
    //
    // $this->assertTrue($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testUpdate
    // */
    // public function testReread($user_id, $course_id, $status, $method = null)
    // {
    // $original = self :: createCourseUserRelation(null, $user_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseUserRelation($original->get_id());
    // $this->assertInstanceOf(CourseUserRelation :: class_name(), $read);
    // self :: modifyEntity($read, false);
    // if ($method !== null)
    // {
    // $read->$method(null);
    // }
    // $this->assertTrue($read->update());
    //
    // $reread = self :: retrieveCourseUserRelation($read->get_id());
    // $this->assertInstanceOf(CourseUserRelation :: class_name(), $reread);
    //
    // $message = self :: createErrorMessage('$id', $read->get_id(), $reread->get_id());
    // $this->assertSame($read->get_id(), $reread->get_id(), $message);
    // $expected_user_id = $read->get_user_id() !== null ? $read->get_user_id() : 0;
    // $message = self :: createErrorMessage('$user_id', $expected_user_id, $reread->get_user_id());
    // $this->assertSame($expected_user_id, $reread->get_user_id(), $message);
    // $message = self :: createErrorMessage('$course_id', $read->get_course_id(), $reread->get_course_id());
    // $this->assertSame($read->get_course_id(), $reread->get_course_id(), $message);
    // $expected_status = $read->get_status() !== null ? $read->get_status() : CourseUserRelation :: STATUS_STUDENT;
    // $message = self :: createErrorMessage('$status', $expected_status, $reread->get_status());
    // $this->assertSame($expected_status, $reread->get_status(), $message);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testReread
    // */
    // public function testRereadNoUserId($user_id, $course_id, $status)
    // {
    // $method = 'set_user_id';
    // $this->testReread($user_id, $course_id, $status, $method);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testReread
    // */
    // public function testRereadNoStatus($user_id, $course_id, $status)
    // {
    // $method = 'set_status';
    // $this->testReread($user_id, $course_id, $status, $method);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testCreate
    // */
    // public function testDelete($user_id, $course_id, $status)
    // {
    // $course_user_relation = self :: createCourseUserRelation(null, $user_id, $course_id, $status);
    // $this->assertTrue($course_user_relation->create());
    // $persisted = $course_user_relation->delete();
    // if (!$persisted)
    // {
    // $this->registerPersistedEntity($course_user_relation);
    // }
    // $this->assertTrue($persisted);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testSaveCreate($user_id, $course_id, $status)
    // {
    // $original = self :: createCourseUserRelation(null, $user_id, $course_id, $status);
    // $this->assertTrue($original->save());
    // $this->registerPersistedEntity($original);
    // }
    //
    // /**
    // * @dataProvider providerStandardDataId
    // */
    // public function testSaveCreateId($id, $user_id, $course_id, $status)
    // {
    // $original = self :: createCourseUserRelation($id, $user_id, $course_id, $status);
    // $persisted = $original->save();
    // if ($persisted)
    // {
    // $this->registerPersistedEntity($original);
    // }
    // $this->assertFalse($persisted);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testCreate
    // */
    // public function testSaveUpdate($user_id, $course_id, $status)
    // {
    // $original = self :: createCourseUserRelation(null, $user_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseUserRelation($original->get_id());
    // $this->assertInstanceOf(CourseUserRelation :: class_name(), $read);
    // self :: modifyEntity($read, false);
    //
    // $this->assertTrue($read->save());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testCreate
    // */
    // public function testSaveUpdateNoId($user_id, $course_id, $status)
    // {
    // $original = self :: createCourseUserRelation(null, $user_id, $course_id, $status);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseUserRelation($original->get_id());
    // $this->assertInstanceOf(CourseUserRelation :: class_name(), $read);
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
