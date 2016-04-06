<?php
namespace Chamilo\Application\Weblcms\Course\Test\Integration;

use Chamilo\Libraries\Storage\DataClassIntegrationTestCase;

/**
 *
 * @author Anthony Hurst (Hogeschool Gent)
 */
class CourseRelCourseSettingIntegrationTest extends DataClassIntegrationTestCase
{
    // /******************************************************************************************************************/
    // /* Data providers */
    // /******************************************************************************************************************/
    //
    // public function providerStandardData()
    // {
    // //array($course_id, $course_setting_id);
    // $data = array();
    // $data[] = array(5, 6);
    // $data[] = array(7, 12);
    // $data[] = array(1, 122);
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
    // * Creates an instance of CourseRelCourseSetting from the values passed.
    // * @static
    // * @param int $id The id for the CourseRelCourseSetting. Not normally set.
    // * @param int $course_id The id of the course referenced by the CourseRelCourseSetting.
    // * @param int $course_setting_id The id of the course setting referenced by the CourseRelCourseSetting.
    // * @return CourseRelCourseSetting The constructed CourseRelCourseSetting.
    // */
    // private static function createCourseRelCourseSetting($id = null, $course_id = null, $course_setting_id = null)
    // {
    // $course_rel_course_setting = new CourseRelCourseSetting();
    // if ($id !== null)
    // {
    // $course_rel_course_setting->set_id($id);
    // }
    // if ($course_id !== null)
    // {
    // $course_rel_course_setting->set_course_id($course_id);
    // }
    // if ($course_setting_id !== null)
    // {
    // $course_rel_course_setting->set_course_setting_id($course_setting_id);
    // }
    // return $course_rel_course_setting;
    // }
    //
    // /**
    // * @static
    // * @param CourseRelCourseSetting $entity
    // * @param bool $alter_id
    // * @return void
    // */
    protected static function modifyEntity(&$entity, $alter_id)
    {
        if ($alter_id)
        {
            $entity->set_id($entity->get_id() + 5);
        }
        $entity->set_course_id($entity->get_course_id() + 2);
        $entity->set_course_setting_id($entity->get_course_setting_id() + 1);
    }
    //
    // /**
    // * @static
    // * @param int $id
    // * @return CourseRelCourseSetting
    // */
    // private static function retrieveCourseRelCourseSetting($id)
    // {
    // return self :: retrieveEntity(CourseRelCourseSetting :: class_name(), $id);
    // }
    //
    // /******************************************************************************************************************/
    // /* Test methods */
    // /******************************************************************************************************************/
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreate($course_id, $course_setting_id)
    // {
    // $original = self :: createCourseRelCourseSetting(null, $course_id, $course_setting_id);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    // }
    //
    // /**
    // * @dataProvider providerStandardDataId
    // */
    // public function testCreateId($id, $course_id, $course_setting_id)
    // {
    // $original = self :: createCourseRelCourseSetting($id, $course_id, $course_setting_id);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    // $this->assertNotEquals($id, $original->get_id());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreateNoCourseId($course_id, $course_setting_id)
    // {
    // $original = self :: createCourseRelCourseSetting(null, null, $course_setting_id);
    // $persisted = $original->create();
    // if ($persisted)
    // {
    // $this->registerPersistedEntity($original);
    // }
    // $this->assertFalse($persisted);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreateNoCourseSettingId($course_id, $course_setting_id)
    // {
    // $original = self :: createCourseRelCourseSetting(null, $course_id);
    // $persisted = $original->create();
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
    // public function testRead($course_id, $course_setting_id)
    // {
    // $original = self :: createCourseRelCourseSetting(null, $course_id, $course_setting_id);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseRelCourseSetting($original->get_id());
    // $this->assertInstanceOf(CourseRelCourseSetting :: class_name(), $read);
    //
    // $message = self :: createErrorMessage('$id', $original->get_id(), $read->get_id());
    // $this->assertSame($original->get_id(), $read->get_id(), $message);
    // $message = self :: createErrorMessage('$course_id', $course_id, $read->get_course_id());
    // $this->assertSame($course_id, $read->get_course_id(), $message);
    // $message = self :: createErrorMessage('$course_setting_id', $course_setting_id, $read->get_course_setting_id());
    // $this->assertSame($course_setting_id, $read->get_course_setting_id(), $message);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdate($course_id, $course_setting_id)
    // {
    // $original = self :: createCourseRelCourseSetting(null, $course_id, $course_setting_id);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseRelCourseSetting($original->get_id());
    // $this->assertInstanceOf(CourseRelCourseSetting :: class_name(), $read);
    // self :: modifyEntity($read, false);
    //
    // $this->assertTrue($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoCourseId($course_id, $course_setting_id)
    // {
    // $original = self :: createCourseRelCourseSetting(null, $course_id, $course_setting_id);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseRelCourseSetting($original->get_id());
    // $this->assertInstanceOf(CourseRelCourseSetting :: class_name(), $read);
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
    // public function testUpdateNoCourseSettingId($course_id, $course_setting_id)
    // {
    // $original = self :: createCourseRelCourseSetting(null, $course_id, $course_setting_id);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseRelCourseSetting($original->get_id());
    // $this->assertInstanceOf(CourseRelCourseSetting :: class_name(), $read);
    // self :: modifyEntity($read, false);
    // $read->set_course_setting_id(null);
    //
    // $this->assertFalse($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testUpdate
    // */
    // public function testReread($course_id, $course_setting_id)
    // {
    // $original = self :: createCourseRelCourseSetting(null, $course_id, $course_setting_id);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseRelCourseSetting($original->get_id());
    // $this->assertInstanceOf(CourseRelCourseSetting :: class_name(), $read);
    // self :: modifyEntity($read, false);
    // $this->assertTrue($read->update());
    //
    // $reread = self :: retrieveCourseRelCourseSetting($read->get_id());
    // $this->assertInstanceOf(CourseRelCourseSetting :: class_name(), $reread);
    //
    // $message = self :: createErrorMessage('$id', $read->get_id(), $reread->get_id());
    // $this->assertSame($read->get_id(), $reread->get_id(), $message);
    // $message = self :: createErrorMessage('$course_id', $read->get_course_id(), $reread->get_course_id());
    // $this->assertSame($read->get_course_id(), $reread->get_course_id(), $message);
    // $message = self :: createErrorMessage(
    // '$course_setting_id', $read->get_course_setting_id(), $reread->get_course_setting_id()
    // );
    // $this->assertSame($read->get_course_setting_id(), $reread->get_course_setting_id(), $message);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testCreate
    // */
    // public function testDelete($course_id, $course_setting_id)
    // {
    // $original = self :: createCourseRelCourseSetting(null, $course_id, $course_setting_id);
    // $this->assertTrue($original->create());
    // $persisted = $original->delete();
    // if (!$persisted)
    // {
    // $this->registerPersistedEntity($original);
    // }
    // $this->assertTrue($persisted);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testSaveCreate($course_id, $course_setting_id)
    // {
    // $original = self :: createCourseRelCourseSetting(null, $course_id, $course_setting_id);
    // $this->assertTrue($original->save());
    // $this->registerPersistedEntity($original);
    // }
    //
    // /**
    // * @dataProvider providerStandardDataId
    // */
    // public function testSaveCreateId($id, $course_id, $course_setting_id)
    // {
    // $original = self :: createCourseRelCourseSetting($id, $course_id, $course_setting_id);
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
    // public function testSaveUpdate($course_id, $course_setting_id)
    // {
    // $original = self :: createCourseRelCourseSetting(null, $course_id, $course_setting_id);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseRelCourseSetting($original->get_id());
    // $this->assertInstanceOf(CourseRelCourseSetting :: class_name(), $read);
    // self :: modifyEntity($read, false);
    //
    // $this->assertTrue($read->save());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testCreate
    // */
    // public function testSaveUpdateNoId($course_id, $course_setting_id)
    // {
    // $original = self :: createCourseRelCourseSetting(null, $course_id, $course_setting_id);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseRelCourseSetting($original->get_id());
    // $this->assertInstanceOf(CourseRelCourseSetting :: class_name(), $read);
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
