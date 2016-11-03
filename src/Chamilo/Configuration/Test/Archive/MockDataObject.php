<?php
namespace Chamilo\Configuration\Test\Archive;

use Chamilo\Libraries\Mdb2Database;
use Chamilo\Libraries\ObjectTableOrder;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;

class MockDataObject
{

    public $props;

    public function get_table_name()
    {
        return 'mockdata';
    }

    public function get_default_properties()
    {
        return $this->props;
    }

    public function set_default_properties($props)
    {
        $this->props = $props;
    }

    public function get_default_property_names()
    {
        return array('id', 'inta', 'intb', 'texta', 'textb', 'bool');
    }

    public function set_id($id)
    {
        $this->props['id'] = $id;
    }
}
class Mdb2DatabaseTest extends \PHPUnit_Framework_TestCase
{

    public $db;

    public function setUp()
    {
        $this->db = new Mdb2Database();
        // TODO: Find a way to have a separate test database which can
        // be dropped and recreated as tests are run to ensure no
        // leftovers from previous test runs.
        $this->db->set_prefix('unit_test_');
    }

    public function tearDown()
    {
        // $this->db = new Mdb2Database; // For some reason, the db var is dropped before teardown?
        // FIXME: This is MDB2-specific.
        $mdb2conn = $this->db->get_connection();
        $mdb2conn->loadModule('Manager');
        $tabs = $mdb2conn->manager->listTables();
        foreach ($tabs as $tab)
        {
            if (strpos($tab, 'unit_test_') === 0)
            {
                $mdb2conn->manager->dropTable($tab);
            }
        }
    }
    
    // ////////////////////////////////////////
    // ////// Creating/dropping tables ////////
    // ////////////////////////////////////////
    private function make_table_for_create_and_drop_tests()
    {
        $props = array();
        $props['mycolumn'] = array('type' => 'integer');
        $this->db->create_storage_unit('created_properly', $props, array());
    }
    
    // ///////////////////////////////////////////
    // ////// Storing and retrieving data ////////
    // ///////////////////////////////////////////
    private function make_table_for_mockdata()
    {
        $props = array();
        $props['id'] = array('type' => 'integer', 'autoincrement' => true);
        $props['inta'] = array('type' => 'integer', 'notnull' => true);
        $props['intb'] = array('type' => 'integer', 'notnull' => true);
        $props['texta'] = array('type' => 'text', 'notnull' => true, 'length' => 255);
        $props['textb'] = array('type' => 'text', 'notnull' => true);
        $props['bool'] = array('type' => 'boolean', 'notnull' => true);
        
        $indexes = array(); // more like "constraints", but ok
        $indexes[] = array('type' => 'unique', 'fields' => array('inta' => array()));
        $indexes[] = array('type' => 'unique', 'fields' => array('texta' => array()));
        $this->db->create_storage_unit('mockdata', $props, $indexes);
    }
    
    /*
     * Get any number of guaranteed-to-be-distinct values which conform to the constraints on this table. All values are
     * incremental so sorting on either of them (except id or bool) will return records in the same order as this
     * function returned them.
     */
    private function get_mockdata_objects($how_many)
    {
        $objects = array();
        for ($i = 0, $odd = false; $i < $how_many; ++ $i, $odd = ! $odd)
        {
            $obj = new MockDataObject();
            $obj->props = array(
                'id' => null, 
                'inta' => $i, 
                'intb' => $i * 2, 
                'texta' => 'a' . $i, 
                'textb' => 'b' . $i, 
                'bool' => $odd);
            $objects[] = $obj;
        }
        return $objects;
    }

    public function test_create_storage_unit_creates_a_table_seen_by_storage_unit_exists()
    {
        $this->assertFalse($this->db->storage_unit_exists('created_properly'));
        
        $this->make_table_for_create_and_drop_tests();
        
        $this->assertTrue($this->db->storage_unit_exists('created_properly'));
    }

    public function test_drop_storage_unit_drops_a_table()
    {
        $this->assertFalse($this->db->storage_unit_exists('created_properly'));
        
        $this->make_table_for_create_and_drop_tests();
        $this->assertTrue($this->db->storage_unit_exists('created_properly'));
        $this->db->drop_storage_unit('created_properly');
        
        $this->assertFalse($this->db->storage_unit_exists('created_properly'));
    }

    public function test_simple_object_can_be_created_and_retrieved_again()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        $this->db->create($obj_stored[0]);
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        
        $obj_retrieved = $this->db->retrieve_object('mockdata', $condition, array(), '\\common\\libraries\\MockDataObject');
        $this->assertEquals($obj_stored[0], $obj_retrieved);
    }

    public function test_simple_object_can_be_created_and_retrieved_again_with_retrieve_record()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        $this->db->create($obj_stored[0]);
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        
        $row_retrieved = $this->db->retrieve_record('mockdata', $condition, array());
        $this->assertEquals($obj_stored[0]->props, $row_retrieved);
    }

    public function test_simple_object_can_be_created_and_retrieved_again_with_retrieve_record_set()
    {
        $this->markTestSkipped("RecordResulSet doesn't exists anymore");
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        $this->db->create($obj_stored[0]);
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        
        $query = 'SELECT * FROM ' . $this->db->escape_table_name('mockdata') . ' AS ' . $this->db->get_alias('mockdata');
        $set_retrieved = $this->db->retrieve_record_set($query, 'mockdata', $condition);
        $this->assertEquals($obj_stored[0]->props, $set_retrieved->next_result());
        $this->assertNull($set_retrieved->next_result());
    }

    public function test_simple_object_can_be_created_and_retrieved_again_with_retrieve_row()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        $this->db->create($obj_stored[0]);
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        
        $query = 'SELECT * FROM ' . $this->db->escape_table_name('mockdata') . ' AS ' . $this->db->get_alias('mockdata');
        $row_retrieved = $this->db->retrieve_row($query, 'mockdata', $condition, array());
        $this->assertEquals($obj_stored[0]->props, $row_retrieved);
    }

    public function test_simple_object_can_be_created_and_retrieved_again_with_retrieve_object_set()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        $this->db->create($obj_stored[0]);
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        
        $query = 'SELECT * FROM ' . $this->db->escape_table_name('mockdata') . ' AS ' . $this->db->get_alias('mockdata');
        $set_retrieved = $this->db->retrieve_object_set(
            $query, 
            'mockdata', 
            $condition, 
            null, 
            null, 
            array(), 
            '\\common\\libraries\\MockDataObject');
        $this->assertEquals($obj_stored[0], $set_retrieved->next_result());
        $this->assertNull($set_retrieved->next_result());
    }

    public function test_simple_object_can_be_created_and_retrieved_again_with_retrieve_objects()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        $this->db->create($obj_stored[0]);
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        
        $set_retrieved = $this->db->retrieve_objects(
            'mockdata', 
            $condition, 
            null, 
            null, 
            array(), 
            '\\common\\libraries\\MockDataObject');
        $this->assertEquals($obj_stored[0], $set_retrieved->next_result());
        $this->assertNull($set_retrieved->next_result());
    }

    public function test_retrieve_object_correctly_translates_db_data_types_to_php_types()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        $this->db->create($obj_stored[0]);
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        
        $obj_retrieved = $this->db->retrieve_object('mockdata', $condition, array(), '\\common\\libraries\\MockDataObject');
        $this->assertInternalType('integer', $obj_retrieved->props['id']);
        $this->assertInternalType('integer', $obj_retrieved->props['inta']);
        $this->assertInternalType('integer', $obj_retrieved->props['intb']);
        $this->assertInternalType('string', $obj_retrieved->props['texta']);
        $this->assertInternalType('string', $obj_retrieved->props['textb']);
        $this->assertInternalType('boolean', $obj_retrieved->props['bool']);
    }

    public function test_retrieve_object_set_correctly_translates_db_data_types_to_php_types()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        $this->db->create($obj_stored[0]);
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        
        $query = 'SELECT * FROM ' . $this->db->escape_table_name('mockdata') . ' AS ' . $this->db->get_alias('mockdata');
        $set_retrieved = $this->db->retrieve_object_set(
            $query, 
            'mockdata', 
            $condition, 
            null, 
            null, 
            array(), 
            '\\common\\libraries\\MockDataObject');
        $obj_retrieved = $set_retrieved->next_result();
        $this->assertInternalType('integer', $obj_retrieved->props['id']);
        $this->assertInternalType('integer', $obj_retrieved->props['inta']);
        $this->assertInternalType('integer', $obj_retrieved->props['intb']);
        $this->assertInternalType('string', $obj_retrieved->props['texta']);
        $this->assertInternalType('string', $obj_retrieved->props['textb']);
        $this->assertInternalType('boolean', $obj_retrieved->props['bool']);
    }

    public function test_retrieve_record_correctly_translates_db_data_types_to_php_types()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        $this->db->create($obj_stored[0]);
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        
        $record_retrieved = $this->db->retrieve_record('mockdata', $condition, array());
        $this->assertInternalType('integer', $record_retrieved['id']);
        $this->assertInternalType('integer', $record_retrieved['inta']);
        $this->assertInternalType('integer', $record_retrieved['intb']);
        $this->assertInternalType('string', $record_retrieved['texta']);
        $this->assertInternalType('string', $record_retrieved['textb']);
        $this->assertInternalType('boolean', $record_retrieved['bool']);
    }

    public function test_retrieve_record_set_correctly_translates_db_data_types_to_php_types()
    {
        $this->markTestSkipped("RecordResulSet doesn't exists anymore");
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        $this->db->create($obj_stored[0]);
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        
        $query = 'SELECT * FROM ' . $this->db->escape_table_name('mockdata') . ' AS ' . $this->db->get_alias('mockdata');
        $set_retrieved = $this->db->retrieve_record_set(
            $query, 
            'mockdata', 
            $condition, 
            null, 
            null, 
            array(), 
            '\\common\\libraries\\MockDataObject');
        $record_retrieved = $set_retrieved->next_result();
        $this->assertInternalType('integer', $record_retrieved['id']);
        $this->assertInternalType('integer', $record_retrieved['inta']);
        $this->assertInternalType('integer', $record_retrieved['intb']);
        $this->assertInternalType('string', $record_retrieved['texta']);
        $this->assertInternalType('string', $record_retrieved['textb']);
        $this->assertInternalType('boolean', $record_retrieved['bool']);
    }

    public function test_retrieve_row_correctly_translates_db_data_types_to_php_types()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        $this->db->create($obj_stored[0]);
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        
        $query = 'SELECT * FROM ' . $this->db->escape_table_name('mockdata') . ' AS ' . $this->db->get_alias('mockdata');
        $record_retrieved = $this->db->retrieve_row($query, 'mockdata', $condition, array());
        $this->assertInternalType('integer', $record_retrieved['id']);
        $this->assertInternalType('integer', $record_retrieved['inta']);
        $this->assertInternalType('integer', $record_retrieved['intb']);
        $this->assertInternalType('string', $record_retrieved['texta']);
        $this->assertInternalType('string', $record_retrieved['textb']);
        $this->assertInternalType('boolean', $record_retrieved['bool']);
    }
    
    // The following two tests are stupid and shouldn't be here, but
    // MDB2's default behaviour is braindead and we should guard against
    // regressions to the wrong behaviour
    // This corresponds to the MDB2_PORTABILITY_RTRIM option
    public function test_string_values_arent_trimmed()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        
        $obj_stored[0]->props['texta'] = '   spaces surrounding a fixed-width varchar   ';
        $obj_stored[0]->props['textb'] = '   spaces surrounding a variable-width text field  ';
        $this->db->create($obj_stored[0]);
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        
        $obj_retrieved = $this->db->retrieve_object('mockdata', $condition, array(), '\\common\\libraries\\MockDataObject');
        $this->assertSame($obj_stored[0]->props['texta'], $obj_retrieved->props['texta']);
        $this->assertSame($obj_stored[0]->props['textb'], $obj_retrieved->props['textb']);
    }
    
    // This corresponds to the MDB2_PORTABILITY_EMPTY_TO_NULL option
    public function test_empty_string_values_arent_converted_to_nulls()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        
        $obj_stored[0]->props['texta'] = '';
        $obj_stored[0]->props['textb'] = '';
        $this->db->create($obj_stored[0]);
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        
        $obj_retrieved = $this->db->retrieve_object('mockdata', $condition, array(), '\\common\\libraries\\MockDataObject');
        $this->assertSame($obj_stored[0]->props['texta'], $obj_retrieved->props['texta']);
        $this->assertSame($obj_stored[0]->props['textb'], $obj_retrieved->props['textb']);
    }

    public function test_simple_object_can_be_updated()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        $this->db->create($obj_stored[0]);
        
        $obj_updated = $obj_stored[0];
        $obj_updated->props['intb'] = 42;
        $obj_updated->props['textb'] = 'updated';
        
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        $this->db->update($obj_updated, $condition);
        
        $obj_retrieved = $this->db->retrieve_object('mockdata', $condition, array(), '\\common\\libraries\\MockDataObject');
        $this->assertEquals($obj_updated, $obj_retrieved);
    }

    public function test_update_with_condition_affects_only_targeted_rows()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(2);
        $this->db->create($obj_stored[0]);
        $this->db->create($obj_stored[1]);
        
        $obj_updated = $obj_stored[0];
        $obj_updated->props['inta'] = 42;
        $obj_updated->props['texta'] = 'updated';
        
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        $this->db->update($obj_updated, $condition);
        
        $obj_retrieved = $this->db->retrieve_object('mockdata', $condition, array(), '\\common\\libraries\\MockDataObject');
        $this->assertEquals($obj_updated, $obj_retrieved);
        
        $condition = new EqualityCondition('id', $obj_stored[1]->props['id']);
        $obj_retrieved2 = $this->db->retrieve_object('mockdata', $condition, array(), '\\common\\libraries\\MockDataObject');
        $this->assertEquals($obj_stored[1], $obj_retrieved2);
    }

    public function test_update_without_condition_throws_exception()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1); // No need to actually store this
        
        $this->setExpectedException('Exception');
        $this->db->update($obj_stored[0]);
    }

    public function test_delete_without_conditions_truncates_table()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(2);
        $this->db->create($obj_stored[0]);
        $this->db->create($obj_stored[1]);
        
        $this->db->delete('mockdata', null);
        
        $this->assertSame(0, $this->db->count_objects('mockdata'));
    }

    public function test_delete_with_conditions_affects_only_targeted_rows()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(2);
        $this->db->create($obj_stored[0]);
        $this->db->create($obj_stored[1]);
        
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        $this->db->delete('mockdata', $condition);
        
        $objectset = $this->db->retrieve_objects(
            'mockdata', 
            null, 
            null, 
            null, 
            array(), 
            '\\common\\libraries\\MockDataObject');
        while ($res = $objectset->next_result())
        {
            $objects[] = $res;
        }
        $this->assertEquals(array($obj_stored[1]), $objects);
    }
    
    // ////////////////////////////////////////////////
    // ////// Bulk storing and retrieving data ////////
    // ////////////////////////////////////////////////
    public function test_retrieve_objects_without_condition_returns_all_records()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(2);
        $this->db->create($obj_stored[0]);
        $this->db->create($obj_stored[1]);
        
        $objectset = $this->db->retrieve_objects(
            'mockdata', 
            null, 
            null, 
            null, 
            array(), 
            '\\common\\libraries\\MockDataObject');
        while ($res = $objectset->next_result())
        {
            $objects[] = $res;
        }
        $this->assertSame(2, count($objects));
        $this->assertThat($obj_stored[0], $this->logicalOr($this->equalTo($objects[0]), $this->equalTo($objects[1])));
        $this->assertThat($obj_stored[1], $this->logicalOr($this->equalTo($objects[0]), $this->equalTo($objects[1])));
        $this->assertThat($objects[0], $this->logicalNot($this->equalTo($objects[1])));
    }

    public function test_count_objects_without_conditions_counts_all_rows()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(2);
        $this->db->create($obj_stored[0]);
        $this->db->create($obj_stored[1]);
        
        $this->assertSame(2, $this->db->count_objects('mockdata'));
    }

    public function test_count_objects_with_conditions_counts_only_targeted_rows()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(2);
        $this->db->create($obj_stored[0]);
        $this->db->create($obj_stored[1]);
        
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        $this->assertSame(1, $this->db->count_objects('mockdata', $condition));
    }

    public function test_count_distinct_without_conditions_counts_only_distinct_rows_for_target_column()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(3);
        
        $obj_stored[0]->props['intb'] = $obj_stored[1]->props['intb'] = 42; // Ensure they are equal
        $this->db->create($obj_stored[0]);
        $this->db->create($obj_stored[1]);
        $this->db->create($obj_stored[2]);
        
        $this->assertSame(3, $this->db->count_distinct('mockdata', 'id'));
        $this->assertSame(2, $this->db->count_distinct('mockdata', 'intb'));
    }

    public function test_count_distinct_with_conditions_counts_only_targeted_rows()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(3);
        
        $obj_stored[0]->props['intb'] = $obj_stored[1]->props['intb'] = 42; // Ensure they are equal
        $this->db->create($obj_stored[0]);
        $this->db->create($obj_stored[1]);
        $this->db->create($obj_stored[2]);
        
        // Exclude the one with a different "intb" value:
        $condition = new NotCondition(new EqualityCondition('id', $obj_stored[2]->props['id']));
        
        $this->assertSame(2, $this->db->count_distinct('mockdata', 'id', $condition));
        $this->assertSame(1, $this->db->count_distinct('mockdata', 'intb', $condition));
    }

    public function test_retrieve_objects_with_order_returns_sorted_objectset()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(2);
        $this->db->create($obj_stored[0]);
        $this->db->create($obj_stored[1]);
        
        $order = array(new ObjectTableOrder('inta', SORT_ASC));
        $objectset = $this->db->retrieve_objects(
            'mockdata', 
            null, 
            null, 
            null, 
            $order, 
            '\\common\\libraries\\MockDataObject');
        $obj_retrieved = array();
        while ($res = $objectset->next_result())
        {
            $obj_retrieved[] = $res;
        }
        $this->assertEquals($obj_stored, $obj_retrieved);
        
        $order = array(new ObjectTableOrder('inta', SORT_DESC));
        $objectset = $this->db->retrieve_objects(
            'mockdata', 
            null, 
            null, 
            null, 
            $order, 
            '\\common\\libraries\\MockDataObject');
        $obj_retrieved = array();
        while ($res = $objectset->next_result())
        {
            $obj_retrieved[] = $res;
        }
        $this->assertEquals(array_reverse($obj_stored), $obj_retrieved);
    }

    public function test_retrieve_objects_with_offset_is_error()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(2);
        $this->db->create($obj_stored[0]);
        $this->db->create($obj_stored[1]);
        
        $order = array(new ObjectTableOrder('inta', SORT_ASC));
        $this->setExpectedException('Exception');
        $this->db->retrieve_objects('mockdata', null, 2, null, $order, '\\common\\libraries\\MockDataObject');
    }

    public function test_retrieve_objects_with_max_objects_limits_resultset()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(2);
        $this->db->create($obj_stored[0]);
        $this->db->create($obj_stored[1]);
        
        $order = array(new ObjectTableOrder('inta', SORT_ASC));
        $objectset = $this->db->retrieve_objects(
            'mockdata', 
            null, 
            null, 
            1, 
            $order, 
            '\\common\\libraries\\MockDataObject');
        $obj_retrieved = array();
        while ($res = $objectset->next_result())
        {
            $obj_retrieved[] = $res;
        }
        $this->assertEquals(array($obj_stored[0]), $obj_retrieved);
    }

    public function test_retrieve_objects_with_max_objects_and_offset_limits_resultset_and_skips_records()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(3);
        $this->db->create($obj_stored[0]);
        $this->db->create($obj_stored[1]);
        $this->db->create($obj_stored[2]);
        
        $order = array(new ObjectTableOrder('inta', SORT_ASC));
        $objectset = $this->db->retrieve_objects('mockdata', null, 1, 1, $order, '\\common\\libraries\\MockDataObject');
        $objects = array();
        while ($res = $objectset->next_result())
        {
            $objects[] = $res;
        }
        $this->assertEquals(array($obj_stored[1]), $objects);
    }

    public function test_bulk_update_without_condition_affects_all_rows()
    {
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(2);
        $this->db->create($obj_stored[0]);
        $this->db->create($obj_stored[1]);
        
        $updated_props = array();
        $updated_props['intb'] = 42;
        $updated_props['textb'] = 'updated';
        foreach ($updated_props as &$value)
        {
            $value = $this->db->quote($value);
        }
        
        $this->db->update_objects('mockdata', $updated_props, null);
        
        $condition = new EqualityCondition('id', $obj_stored[0]->props['id']);
        $obj_retrieved = $this->db->retrieve_object('mockdata', $condition, array(), '\\common\\libraries\\MockDataObject');
        $obj_stored[0]->props['intb'] = 42;
        $obj_stored[0]->props['textb'] = 'updated';
        $this->assertEquals($obj_stored[0], $obj_retrieved);
        
        $condition = new EqualityCondition('id', $obj_stored[1]->props['id']);
        $obj_retrieved2 = $this->db->retrieve_object('mockdata', $condition, array(), '\\common\\libraries\\MockDataObject');
        $obj_stored[1]->props['intb'] = 42;
        $obj_stored[1]->props['textb'] = 'updated';
        $this->assertEquals($obj_stored[1], $obj_retrieved2);
    }
    
    // ////////////////////////////////////////////////
    // ////// Integrity and constraint checks /////////
    // ////////////////////////////////////////////////
    public function test_exception_thrown_on_invalid_integer_syntax()
    {
        $this->markTestSkipped('Exception is not yet expected behaviour');
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        $obj_stored[0]->props['inta'] = 'one';
        
        $this->setExpectedException('Exception');
        $this->db->create($obj_stored[0]);
    }

    public function test_exception_thrown_on_null_for_nonnullable_values()
    {
        $this->markTestSkipped('Exception is not yet expected behaviour');
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        $obj_stored[0]->props['inta'] = null;
        
        $this->setExpectedException('Exception');
        $this->db->create($obj_stored[0]);
    }

    public function test_exception_thrown_on_missing_nonnullable_values_without_default()
    {
        $this->markTestSkipped('Exception is not yet expected behaviour');
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(1);
        unset($obj_stored[0]->props['inta']);
        
        $this->setExpectedException('Exception');
        $this->db->create($obj_stored[0]);
    }

    public function test_exception_thrown_on_duplicate_entry_for_unique_values()
    {
        $this->markTestSkipped('Exception is not yet expected behaviour');
        $this->make_table_for_mockdata();
        $obj_stored = $this->get_mockdata_objects(2);
        $obj_stored[0]->props['inta'] = $obj_stored[1]->props['inta'] = 42;
        
        $this->db->create($obj_stored[0]);
        $this->setExpectedException('Exception');
        $this->db->create($obj_stored[1]);
    }
}
