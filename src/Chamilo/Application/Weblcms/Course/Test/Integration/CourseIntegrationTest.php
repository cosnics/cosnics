<?php
namespace Chamilo\Application\Weblcms\Course\Test\Integration;

use Chamilo\Libraries\Storage\DataClassIntegrationTestCase;

/**
 *
 * @author Anthony Hurst (Hogeschool Gent)
 */
class CourseIntegrationTest extends DataClassIntegrationTestCase
{
    // const WEEK = 604800;
    // const DAY = 86400;
    //
    // /******************************************************************************************************************/
    // /* Data providers */
    // /******************************************************************************************************************/
    //
    // public function providerStandardData()
    // {
    // //array($course_type_id, $titular_id, $title, $visual_code, $creation_date, $expiration_date, $last_edit,
    // $last_visit, $category_id, $language)
    // $data = array();
    // $data[] = array(1, 52, 'ndhfkjfh', 'jhdsg', 1343811840, 1343899422, 1343811840, 1343811840, 2, 'de');
    // $data[] = array(23, 1, 'jslifg', 'jsidigfzu', 1343821201, 1458124681, 1234753120, 1684579423, 7, 'nl');
    // $data[] = array(11, 23, 'jhfdg', 'qwreutg', 1523814650, 1567426853, 1257859864, 1285698741, 23, 'fr');
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
    // * @param int $id
    // * @param int $course_type_id
    // * @param int $titular_id
    // * @param string $title
    // * @param string $visual_code
    // * @param int $creation_date
    // * @param int $expiration_date
    // * @param int $last_edit
    // * @param int $last_visit
    // * @param int $category_id
    // * @param string $language
    // * @return Course The created course.
    // */
    // private static function createCourse(
    // $id = null, $course_type_id = null, $titular_id = null, $title = null,
    // $visual_code = null, $creation_date = null, $expiration_date = null, $last_edit = null, $last_visit = null,
    // $category_id = null, $language = null
    // )
    // {
    // $course = new Course();
    // if ($id !== null)
    // {
    // $course->set_id($id);
    // }
    // if ($course_type_id !== null)
    // {
    // $course->set_course_type_id($course_type_id);
    // }
    // if ($titular_id !== null)
    // {
    // $course->set_titular_id($titular_id);
    // }
    // if ($title !== null)
    // {
    // $course->set_title($title);
    // }
    // if ($visual_code !== null)
    // {
    // $course->set_visual_code($visual_code);
    // }
    // if ($creation_date !== null)
    // {
    // $course->set_creation_date($creation_date);
    // }
    // if ($expiration_date !== null)
    // {
    // $course->set_expiration_date($expiration_date);
    // }
    // if ($last_edit !== null)
    // {
    // $course->set_last_edit($last_edit);
    // }
    // if ($last_visit !== null)
    // {
    // $course->set_last_visit($last_visit);
    // }
    // if ($category_id !== null)
    // {
    // $course->set_category_id($category_id);
    // }
    // if ($language !== null)
    // {
    // $course->set_language($language);
    // }
    // return $course;
    // }
    //
    // /**
    // * Modifies the properties of the course passed.
    // * @param Course $entity The course whose properties are to be modified.
    // * @param bool $alter_id
    // * @return void
    // */
    protected static function modifyEntity(&$entity, $alter_id)
    {
        if ($alter_id)
        {
            $entity->set_id($entity->get_id() + 5);
        }
        $entity->set_course_type_id($entity->get_course_type_id() + 1);
        $entity->set_titular_id($entity->get_titular_id() + 2);
        $entity->set_title(str_rot13($entity->get_title()));
        $entity->set_visual_code(str_rot13($entity->get_visual_code()));
        $entity->set_creation_date($entity->get_creation_date() - self::WEEK);
        $entity->set_expiration_date($entity->get_expiration_date() + self::WEEK);
        $entity->set_last_edit($entity->get_last_edit() - self::DAY);
        $entity->set_last_visit($entity->get_last_visit() + self::DAY);
        $entity->set_category_id($entity->get_category_id() + 1);
        $entity->set_language($entity->get_language() !== 'en' ? 'en' : 'nl');
    }
    //
    // /**
    // * @static
    // * @param int $id The id of the course being retrieved.
    // * @return Course The retrieved course or null if none found.
    // */
    // private static function retrieveCourse($id)
    // {
    // return self :: retrieveEntity(Course :: class_name(), $id);
    // }
    //
    // /******************************************************************************************************************/
    // /* Test methods */
    // /******************************************************************************************************************/
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreate(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $course = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($course->create());
    // $this->registerPersistedEntity($course);
    // }
    //
    // /**
    // * @dataProvider providerStandardDataId
    // */
    // public function testCreateId(
    // $id, $course_type_id, $titular_id, $title, $visual_code, $creation_date, $expiration_date,
    // $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $course = self :: createCourse(
    // $id, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($course->create());
    // $this->registerPersistedEntity($course);
    // $this->assertNotEquals($id, $course->get_id());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreateNoCourseTypeId(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $course = self :: createCourse(
    // null, null, $titular_id, $title, $visual_code, $creation_date, $expiration_date,
    // $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($course->create());
    // $this->registerPersistedEntity($course);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreateNoTitularId(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $course = self :: createCourse(
    // null, $course_type_id, null, $title, $visual_code, $creation_date, $expiration_date,
    // $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($course->create());
    // $this->registerPersistedEntity($course);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreateNoVisualCode(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $course = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, null, $creation_date, $expiration_date,
    // $last_edit, $last_visit, $category_id, $language
    // );
    // $persisted = $course->create();
    // if ($persisted)
    // {
    // $this->registerPersistedEntity($course);
    // }
    // $this->assertFalse($persisted);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreateNoCreationDate(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $course = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, null, $expiration_date,
    // $last_edit, $last_visit, $category_id, $language
    // );
    // $persisted = $course->create();
    // if ($persisted)
    // {
    // $this->registerPersistedEntity($course);
    // }
    // $this->assertFalse($persisted);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreateNoExpirationDate(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $course = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date, null,
    // $last_edit, $last_visit, $category_id, $language
    // );
    // $persisted = $course->create();
    // if ($persisted)
    // {
    // $this->registerPersistedEntity($course);
    // }
    // $this->assertFalse($persisted);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreateNoLastEdit(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $course = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, null, $last_visit, $category_id, $language
    // );
    // $persisted = $course->create();
    // if ($persisted)
    // {
    // $this->registerPersistedEntity($course);
    // }
    // $this->assertFalse($persisted);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreateNoLastVisit(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $course = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, null, $category_id, $language
    // );
    // $persisted = $course->create();
    // if ($persisted)
    // {
    // $this->registerPersistedEntity($course);
    // }
    // $this->assertFalse($persisted);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreateNoCategoryId(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $course = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, null, $language
    // );
    // $this->assertTrue($course->create());
    // $this->registerPersistedEntity($course);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testCreateNoLanguage(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $course = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, null
    // );
    // $persisted = $course->create();
    // if ($persisted)
    // {
    // $this->registerPersistedEntity($course);
    // }
    // $this->assertFalse($persisted);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testCreate
    // */
    // public function testRead(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date, $expiration_date,
    // $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $original = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourse($original->get_id());
    // $this->assertInstanceOf(Course :: class_name(), $read);
    // ;
    //
    // $message = self :: createErrorMessage('$id', $original->get_id(), $read->get_id());
    // $this->assertSame($original->get_id(), $read->get_id(), $message);
    // $original_course_type_id = $course_type_id !== null ? $course_type_id : 0;
    // $message = self :: createErrorMessage('$course_type_id', $original_course_type_id, $read->get_course_type_id());
    // $this->assertSame($original_course_type_id, $read->get_course_type(), $message);
    // $original_titular_id = $titular_id !== null ? $titular_id : 0;
    // $message = self :: createErrorMessage('$titular_id', $original_titular_id, $read->get_titular_id());
    // $this->assertSame($original_titular_id, $read->get_titular_id(), $message);
    // $message = self :: createErrorMessage('$title', $title, $read->get_title());
    // $this->assertSame($title, $read->get_title(), $message);
    // $message = self :: createErrorMessage('$visual_code', $visual_code, $read->get_visual_code());
    // $this->assertSame($visual_code, $read->get_visual_code(), $message);
    // $message = self :: createErrorMessage('$creation_date', $creation_date, $read->get_creation_date());
    // $this->assertSame($creation_date, $read->get_creation_date(), $message);
    // $message = self :: createErrorMessage('$expiration_date', $expiration_date, $read->get_expiration_date());
    // $this->assertSame($expiration_date, $read->get_expiration_date(), $message);
    // $message = self :: createErrorMessage('$last_edit', $last_edit, $read->get_last_edit());
    // $this->assertSame($last_edit, $read->get_last_edit(), $message);
    // $message = self :: createErrorMessage('$last_visit', $last_visit, $read->get_last_visit());
    // $this->assertSame($last_visit, $read->get_last_visit(), $message);
    // $original_category_id = $category_id !== null ? $category_id : 0;
    // $message = self :: createErrorMessage('$category_id', $original_category_id, $read->get_category_id());
    // $this->assertSame($original_category_id, $read->get_category_id(), $message);
    // $message = self :: createErrorMessage('$language', $language, $read->get_language());
    // $this->assertSame($language, $read->get_language(), $message);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testReadNoCourseTypeId(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $this->testRead(
    // null, $titular_id, $title, $visual_code, $creation_date, $expiration_date, $last_edit,
    // $last_visit, $category_id, $language
    // );
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testReadNoTitularId(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $this->testRead(
    // $course_type_id, null, $title, $visual_code, $creation_date, $expiration_date, $last_edit,
    // $last_visit, $category_id, $language
    // );
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testReadNoCategoryId(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $this->testRead(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date, $expiration_date, $last_edit,
    // $last_visit, null, $language
    // );
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdate(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $original = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourse($original->get_id());
    // $this->assertInstanceOf(Course :: class_name(), $read);
    // ;
    // self :: modifyEntity($read, false);
    //
    // $this->assertTrue($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoId(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $original = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourse($original->get_id());
    // $this->assertInstanceOf(Course :: class_name(), $read);
    // ;
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
    // public function testUpdateNoCourseTypeId(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $original = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourse($original->get_id());
    // $this->assertInstanceOf(Course :: class_name(), $read);
    // ;
    // self :: modifyEntity($read, false);
    // $read->set_course_type_id(null);
    //
    // $this->assertTrue($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoTitularId(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $original = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourse($original->get_id());
    // $this->assertInstanceOf(Course :: class_name(), $read);
    // ;
    // self :: modifyEntity($read, false);
    // $read->set_titular_id(null);
    //
    // $this->assertTrue($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoVisualCode(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $original = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourse($original->get_id());
    // $this->assertInstanceOf(Course :: class_name(), $read);
    // ;
    // self :: modifyEntity($read, false);
    // $read->set_visual_code(null);
    //
    // $this->assertFalse($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoCreationDate(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $original = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourse($original->get_id());
    // $this->assertInstanceOf(Course :: class_name(), $read);
    // ;
    // self :: modifyEntity($read, false);
    // $read->set_creation_date(null);
    //
    // $this->assertFalse($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoExpirationDate(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $original = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourse($original->get_id());
    // $this->assertInstanceOf(Course :: class_name(), $read);
    // ;
    // self :: modifyEntity($read, false);
    // $read->set_expiration_date(null);
    //
    // $this->assertFalse($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoLastEdit(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $original = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourse($original->get_id());
    // $this->assertInstanceOf(Course :: class_name(), $read);
    // ;
    // self :: modifyEntity($read, false);
    // $read->set_last_edit(null);
    //
    // $this->assertFalse($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoLastVisit(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $original = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourse($original->get_id());
    // $this->assertInstanceOf(Course :: class_name(), $read);
    // ;
    // self :: modifyEntity($read, false);
    // $read->set_last_visit(null);
    //
    // $this->assertFalse($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoCategoryId(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $original = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourse($original->get_id());
    // $this->assertInstanceOf(Course :: class_name(), $read);
    // ;
    // self :: modifyEntity($read, false);
    // $read->set_category_id(null);
    //
    // $this->assertTrue($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testRead
    // */
    // public function testUpdateNoLanguage(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $original = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourse($original->get_id());
    // $this->assertInstanceOf(Course :: class_name(), $read);
    // ;
    // self :: modifyEntity($read, false);
    // $read->set_language(null);
    //
    // $this->assertFalse($read->update());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testUpdate
    // */
    // public function testReread(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language, $method = null
    // )
    // {
    // $original = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourse($original->get_id());
    // $this->assertInstanceOf(Course :: class_name(), $read);
    // ;
    // self :: modifyEntity($read, false);
    // if ($method !== null)
    // {
    // $read->$method(null);
    // }
    // $this->assertTrue($read->update());
    //
    // $reread = self :: retrieveCourse($original->get_id());
    // $this->assertInstanceOf(Course :: class_name(), $read);
    // ;
    //
    // $message = self :: createErrorMessage('$id', $read->get_id(), $reread->get_id());
    // $this->assertSame($read->get_id(), $reread->get_id(), $message);
    // $expected_course_type_id = $read->get_course_type_id() !== null ? $read->get_course_type_id() : 0;
    // $message =
    // self :: createErrorMessage('$course_type_id', $expected_course_type_id, $reread->get_course_type_id());
    // $this->assertSame($expected_course_type_id, $reread->get_course_type_id(), $message);
    // $expected_titular_id = $read->get_titular_id() !== null ? $read->get_titular_id() : 0;
    // $message = self :: createErrorMessage('$titular_id', $expected_titular_id, $reread->get_titular_id());
    // $this->assertSame($expected_titular_id, $reread->get_titular_id(), $message);
    // $message = self :: createErrorMessage('$title', $read->get_title(), $reread->get_title());
    // $this->assertSame($read->get_title(), $reread->get_title(), $message);
    // $message = self :: createErrorMessage('$visual_code', $read->get_visual_code(), $reread->get_visual_code());
    // $this->assertSame($read->get_visual_code(), $reread->get_visual_code(), $message);
    // $message =
    // self :: createErrorMessage('$creation_date', $read->get_creation_date(), $reread->get_creation_date());
    // $this->assertSame($read->get_creation_date(), $reread->get_creation_date(), $message);
    // $message = self :: createErrorMessage(
    // '$expiration_date', $read->get_expiration_date(), $reread->get_expiration_date()
    // );
    // $this->assertSame($read->get_expiration_date(), $reread->get_expiration_date(), $message);
    // $message = self :: createErrorMessage('$last_edit', $read->get_last_edit(), $reread->get_last_edit());
    // $this->assertSame($read->get_last_edit(), $reread->get_last_edit(), $message);
    // $message = self :: createErrorMessage('$last_visit', $read->get_last_visit(), $reread->get_last_visit());
    // $this->assertSame($read->get_last_visit(), $reread->get_last_visit(), $message);
    // $expected_category_id = $read->get_category_id() !== null ? $read->get_category_id() : 0;
    // $message = self :: createErrorMessage('$category_id', $expected_category_id, $reread->get_category_id());
    // $this->assertSame($expected_category_id, $reread->get_category_id(), $message);
    // $message = self :: createErrorMessage('$language', $read->get_language(), $reread->get_language());
    // $this->assertSame($read->get_language(), $reread->get_language(), $message);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testReread
    // */
    // public function testRereadNoCourseTypeId(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $method = 'set_course_type_id';
    // $this->testReread(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date, $expiration_date,
    // $last_edit, $last_visit, $category_id, $language, $method
    // );
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testReread
    // */
    // public function testRereadNoTitularId(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $method = 'set_titular_id';
    // $this->testReread(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date, $expiration_date,
    // $last_edit, $last_visit, $category_id, $language, $method
    // );
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testReread
    // */
    // public function testRereadNoCategoryId(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $method = 'set_category_id';
    // $this->testReread(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date, $expiration_date,
    // $last_edit, $last_visit, $category_id, $language, $method
    // );
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testCreate
    // */
    // public function testDelete(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $course = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($course->create());
    // $persisted = $course->delete();
    // if (!$persisted)
    // {
    // $this->registerPersistedEntity($course);
    // }
    // $this->assertTrue($persisted);
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // */
    // public function testSaveCreate(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $original = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($original->save());
    // $this->registerPersistedEntity($original);
    // }
    //
    // /**
    // * @dataProvider providerStandardDataId
    // */
    // public function testCreateSaveId(
    // $id, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $original = self :: createCourse(
    // $id, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
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
    // public function testSaveUpdate(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $original = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourse($original->get_id());
    // $this->assertInstanceOf(Course :: class_name(), $read);
    // ;
    // self :: modifyEntity($read, false);
    //
    // $this->assertTrue($read->save());
    // }
    //
    // /**
    // * @dataProvider providerStandardData
    // * @depends testCreate
    // */
    // public function testSaveUpdateNoId(
    // $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // )
    // {
    // $original = self :: createCourse(
    // null, $course_type_id, $titular_id, $title, $visual_code, $creation_date,
    // $expiration_date, $last_edit, $last_visit, $category_id, $language
    // );
    // $this->assertTrue($original->create());
    // $this->registerPersistedEntity($original);
    //
    // $read = self :: retrieveCourse($original->get_id());
    // $this->assertInstanceOf(Course :: class_name(), $read);
    // ;
    // self :: modifyEntity($read, false);
    // $read->set_id(null);
    // $this->assertTrue($read->save());
    //
    // $message = self :: createErrorMessage('$id', '!' . $original->get_id(), $read->get_id());
    // $this->assertNotEquals($original->get_id(), $read->get_id(), $message);
    //
    // $this->registerPersistedEntity($read);
    // }
}
