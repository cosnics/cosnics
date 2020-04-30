<?php
namespace Chamilo\Application\Weblcms\Course\Test\Integration;

use Chamilo\Libraries\Storage\DataClassIntegrationTestCase;

/**
 *
 * @author Anthony Hurst (Hogeschool Gent)
 */
class CourseRelCourseSettingValueIntegrationTest extends DataClassIntegrationTestCase
{
    // /******************************************************************************************************************/
    // /* Data providers */
    // /******************************************************************************************************************/
    //
    // public function providerStandardData()
    // {
    // //array($course_rel_course_setting_id, $value);
    // $data = array();
    // $data[] = array(1, 'sjdifhogszug');
    // $data[] = array(32, 'jaoifzuvg');
    // $data[] = array(12, 'jolisuzg');
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
    // * @static
    // * @param int $id
    // * @param int $course_rel_course_setting_id
    // * @param string $value
    // * @return CourseRelCourseSettingValue
    // */
    // private static function createCourseRelCourseSettingValue(
    // $id = null, $course_rel_course_setting_id = null, $value = null
    // )
    // {
    // $course_rel_course_setting_value = new CourseRelCourseSettingValue();
    // if ($id !== null)
    // {
    // $course_rel_course_setting_value->set_id($id);
    // }
    // if ($course_rel_course_setting_id !== null)
    // {
    // $course_rel_course_setting_value->set_course_rel_course_setting_id($course_rel_course_setting_id);
    // }
    // if ($value !== null)
    // {
    // $course_rel_course_setting_value->set_value($value);
    // }
    // return $course_rel_course_setting_value;
    // }
    //
    // /**
    // * @static
    // * @param CourseRelCourseSettingValue $entity
    // * @param bool $alter_id
    // * @return void
    // */
    protected static function modifyEntity(&$entity, $alter_id)
    {
        if ($alter_id)
        {
            $entity->set_id($entity->get_id() + 5);
        }
        $entity->set_course_rel_course_setting_id($entity->get_course_rel_course_setting_id() + 1);
        $entity->set_value(str_rot13($entity->get_value()));
    }
    //
    // /**
    // * @static
    // * @param int $id
    // * @return CourseRelCourseSettingValue
    // */
    // public static function retrieveCourseRelCourseSettingValue($id)
    // {
    // return self :: retrieveEntity(CourseRelCourseSettingValue ::class, $id);
    // }
    //
    // /******************************************************************************************************************/
    // /* Test methods */
    // /******************************************************************************************************************/
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreate($course_rel_course_setting_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue(null, $course_rel_course_setting_id, $value);
    //
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    // }
    //
    // /**
    // * @dataProvider providerStandardDataId
    // */
    // public function testCreateId($id, $course_rel_course_setting_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue($id, $course_rel_course_setting_id, $value);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    // $this->assertNotEquals($id, $original->get_id());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreateNoCourseRelCourseSettingId($course_rel_course_setting_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue(null, null, $value);
    //
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
    // public function testCreateNoValue($course_rel_course_setting_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue(null, $course_rel_course_setting_id, null);
    //
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
    // public function testRead($course_rel_course_setting_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue(null, $course_rel_course_setting_id, $value);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseRelCourseSettingValue($original->get_id());
    // $this->assertInstanceOf(CourseRelCourseSettingValue ::class, $read);
    //
    // $message = self :: createErrorMessage('$id', $original->get_id(), $read->get_id());
    // $this->assertSame($original->get_id(), $read->get_id(), $message);
    // $message = self :: createErrorMessage(
    // '$course_rel_course_setting_id', $original->get_course_rel_course_setting_id(),
    // $read->get_course_rel_course_setting_id()
    // );
    // $this->assertSame(
    // $original->get_course_rel_course_setting_id(), $read->get_course_rel_course_setting_id(), $message
    // );
    // $message = self :: createErrorMessage('$value', $original->get_value(), $read->get_value());
    // $this->assertSame($original->get_value(), $read->get_value(), $message);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdate($course_rel_course_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue(null, $course_rel_course_id, $value);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseRelCourseSettingValue($original->get_id());
    // $this->assertInstanceOf(CourseRelCourseSettingValue ::class, $read);
    // self :: modifyEntity($read, false);
    //
    // $this->assertTrue($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoId($course_rel_course_setting_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue(null, $course_rel_course_setting_id, $value);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseRelCourseSettingValue($original->get_id());
    // $this->assertInstanceOf(CourseRelCourseSettingValue ::class, $read);
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
    // public function testUpdateNoCourseRelCourseSettingId($course_rel_course_setting_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue(null, $course_rel_course_setting_id, $value);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseRelCourseSettingValue($original->get_id());
    // $this->assertInstanceOf(CourseRelCourseSettingValue ::class, $read);
    // self :: modifyEntity($read, false);
    // $read->set_course_rel_course_setting_id(null);
    //
    // $this->assertFalse($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoValue($course_rel_course_setting_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue(null, $course_rel_course_setting_id, $value);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseRelCourseSettingValue($original->get_id());
    // $this->assertInstanceOf(CourseRelCourseSettingValue ::class, $read);
    // self :: modifyEntity($read, false);
    // $read->set_value(null);
    //
    // $this->assertFalse($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testUpdate
    // */
    // public function testReread($course_rel_course_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue(null, $course_rel_course_id, $value);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseRelCourseSettingValue($original->get_id());
    // $this->assertInstanceOf(CourseRelCourseSettingValue ::class, $read);
    // self :: modifyEntity($read, false);
    // $this->assertTrue($read->update());
    //
    // $reread = self :: retrieveCourseRelCourseSettingValue($read->get_id());
    // $this->assertInstanceOf(CourseRelCourseSettingValue ::class, $reread);
    //
    // $message = self :: createErrorMessage('$id', $read->get_id(), $reread->get_id());
    // $this->assertSame($read->get_id(), $reread->get_id(), $message);
    // $message = self :: createErrorMessage(
    // '$course_rel_course_setting_id', $read->get_course_rel_course_setting_id(),
    // $reread->get_course_rel_course_setting_id()
    // );
    // $this->assertSame(
    // $read->get_course_rel_course_setting_id(), $reread->get_course_rel_course_setting_id(), $message
    // );
    // $message = self :: createErrorMessage('$value', $read->get_value(), $reread->get_value());
    // $this->assertSame($read->get_value(), $reread->get_value(), $message);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testCreate
    // */
    // public function testDelete($course_rel_course_setting_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue(null, $course_rel_course_setting_id, $value);
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
    // public function testSaveCreate($course_rel_course_setting_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue(null, $course_rel_course_setting_id, $value);
    // $this->assertTrue($original->save());
    // $this->registerPersistedEntity($original);
    // }
    //
    // /**
    // * @dataProvider providerStandardDataId
    // */
    // public function testSaveCreateId($id, $course_rel_course_setting_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue($id, $course_rel_course_setting_id, $value);
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
    // */
    // public function testSaveCreateNoCourseRelCourseSettingId($course_rel_course_setting_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue(null, null, $value);
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
    // */
    // public function testSaveCreateNoValue($course_rel_course_setting_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue(null, $course_rel_course_setting_id);
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
    // public function testSaveUpdate($course_rel_course_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue(null, $course_rel_course_id, $value);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseRelCourseSettingValue($original->get_id());
    // $this->assertInstanceOf(CourseRelCourseSettingValue ::class, $read);
    // self :: modifyEntity($read, false);
    //
    // $this->assertTrue($read->save());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testCreate
    // */
    // public function testSaveUpdateNoId($course_rel_course_setting_id, $value)
    // {
    // $original = self :: createCourseRelCourseSettingValue(null, $course_rel_course_setting_id, $value);
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourseRelCourseSettingValue($original->get_id());
    // $this->assertInstanceOf(CourseRelCourseSettingValue ::class, $read);
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
