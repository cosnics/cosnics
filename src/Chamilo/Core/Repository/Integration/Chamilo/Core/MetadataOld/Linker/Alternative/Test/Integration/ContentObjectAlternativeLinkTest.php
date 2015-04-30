<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Alternative\Test\Integration;

use Chamilo\Core\Repository\ContentObject\Announcement\Storage\DataClass\Announcement;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Alternative\Storage\DataManager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Type\Storage\DataClass\ContentObjectRelMetadataElement;
use Chamilo\Libraries\Architecture\Test\Test;
use Chamilo\Libraries\Storage\Cache\RecordCache;

/**
 * This class tests several use cases to link (multiple) content objects together by metadata elements.
 * 
 * @package repository\integration\core\metadata\linker\alternative\test
 */
class ContentObjectAlternativeLinkTest extends Test
{
    const METADATA_ELEMENT_LANGUAGE = 'dc:language';
    const METADATA_ELEMENT_FORMAT = 'dc:format';
    const PARAM_BASE_OBJECT = 'base_object';
    const PARAM_LINKS = 'links';

    /**
     * The created dataclass objects per classname
     * 
     * @var \libraries\storage\DataClass[string]
     */
    private static $created_objects;

    /**
     * The metadata elements that are needed to execute this use_cases
     * 
     * @var \core\metadata\element\storage\data_class\Element[]
     */
    private static $metadata_elements;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        self :: create_announcement_content_objects();
        self :: retrieve_metadata_elements();
        self :: create_content_object_rel_metadata_element_links();
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass()
    {
        foreach (self :: $created_objects as $objects)
        {
            foreach ($objects as $object)
            {
                $object->delete();
            }
        }
    }

    /**
     * Creates the announcement content objects
     * 
     * @return ContentObject
     */
    protected static function create_announcement_content_objects()
    {
        $titles = array('Object A', 'Object B', 'Object C', 'Object D', 'Object E');
        
        foreach ($titles as $title)
        {
            $object = new Announcement();
            
            $object->set_title($title);
            $object->set_description($title);
            $object->set_owner_id(2);
            
            $object->create();
            
            self :: $created_objects[Announcement :: class_name()][] = $object;
        }
    }

    /**
     * Retrieves the metadata elements that are needed to execute this use cases
     */
    protected static function retrieve_metadata_elements()
    {
        $element_names = array(self :: METADATA_ELEMENT_LANGUAGE, self :: METADATA_ELEMENT_FORMAT);
        
        foreach ($element_names as $element_name)
        {
            self :: $metadata_elements[$element_name] = \Chamilo\Core\MetadataOld\Storage\DataManager :: retrieve_element_by_fully_qualified_element_name(
                $element_name);
        }
    }

    /**
     * Creates the content object rel metadata element links needed for this testing
     */
    protected static function create_content_object_rel_metadata_element_links()
    {
        foreach (self :: $metadata_elements as $metadata_element)
        {
            $content_object_rel_metadata_element = new ContentObjectRelMetadataElement();
            $content_object_rel_metadata_element->set_metadata_element_id($metadata_element->get_id());
            $content_object_rel_metadata_element->create();
            
            self :: $created_objects[ContentObjectRelMetadataElement :: class_name()][] = $content_object_rel_metadata_element;
        }
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        RecordCache :: reset();
    }

    /**
     * This function tests if two unlinked announcement objects can be linked to eachother Links "Object" A to "Object
     * B" by metadata element dc:language
     */
    public function test_link_two_unlinked_announcements_by_language()
    {
        $content_object_links = array();
        
        $content_object_links[] = array(self :: PARAM_BASE_OBJECT => 0, self :: PARAM_LINKS => array(1));
        
        $this->link_creator_tester($content_object_links);
    }

    /**
     * This function tests to link an unlinked announcement to an already linked announcement by language Links "Object
     * A" to the collection "Object B and Object C" by metadata element dc:language
     */
    public function test_link_unlinked_announcement_to_linked_announcement_by_language()
    {
        $content_object_links = array();
        
        $content_object_links[] = array(self :: PARAM_BASE_OBJECT => 1, self :: PARAM_LINKS => array(2));
        $content_object_links[] = array(self :: PARAM_BASE_OBJECT => 0, self :: PARAM_LINKS => array(1));
        
        $this->link_creator_tester($content_object_links);
    }

    /**
     * This function test to link an already linked announcement to an unlinked announcement by language Links the
     * collection "Object A and Object B" to "Object C" by metadata element dc:language
     */
    public function test_link_linked_announcement_to_unlinked_announcement_by_language()
    {
        $content_object_links = array();
        
        $content_object_links[] = array(self :: PARAM_BASE_OBJECT => 0, self :: PARAM_LINKS => array(1));
        $content_object_links[] = array(self :: PARAM_BASE_OBJECT => 0, self :: PARAM_LINKS => array(2));
        
        $this->link_creator_tester($content_object_links);
    }

    /**
     * This function tests to link an unlinked announcement to an already linked announcement by language Links the
     * collection "Object A and Object B" to the collection "Object C and Object D" by metadata element dc:language
     */
    public function test_link_linked_announcement_to_linked_announcement_by_language()
    {
        $content_object_links = array();
        
        $content_object_links[] = array(self :: PARAM_BASE_OBJECT => 0, self :: PARAM_LINKS => array(1));
        $content_object_links[] = array(self :: PARAM_BASE_OBJECT => 2, self :: PARAM_LINKS => array(3));
        $content_object_links[] = array(self :: PARAM_BASE_OBJECT => 0, self :: PARAM_LINKS => array(2));
        
        $this->link_creator_tester($content_object_links);
    }

    /**
     * This helper function test the creation of content object alternative links by a given array
     * 
     * @param array $content_object_links $content_object_links[$base_index] = $link_indexes (array)
     */
    protected function link_creator_tester($content_object_links = array())
    {
        $content_objects = self :: $created_objects[Announcement :: class_name()];
        $metadata_element_id = self :: $metadata_elements[self :: METADATA_ELEMENT_LANGUAGE]->get_id();
        
        $content_object_ids = array();
        
        foreach ($content_object_links as $content_object_link)
        {
            $base_content_object_id = $content_objects[$content_object_link[self :: PARAM_BASE_OBJECT]]->get_id();
            $content_object_ids[] = $base_content_object_id;
            
            $link_content_object_ids = array();
            
            foreach ($content_object_link[self :: PARAM_LINKS] as $link_index)
            {
                $link_content_object_id = $content_objects[$link_index]->get_id();
                $content_object_ids[] = $link_content_object_ids[] = $link_content_object_id;
            }
            
            DataManager :: create_content_object_alternatives(
                $base_content_object_id, 
                $link_content_object_ids, 
                $metadata_element_id);
        }
        
        RecordCache :: reset();
        
        $content_object_ids = array_unique($content_object_ids);
        
        $base_link_number = 0;
        $same_link_number = true;
        
        foreach ($content_object_ids as $content_object_id)
        {
            $link_number = DataManager :: get_link_number_for_content_object_and_metadata_element(
                $content_object_id, 
                $metadata_element_id);
            
            if (! $base_link_number)
            {
                $base_link_number = $link_number;
            }
            else
            {
                if ($link_number != $base_link_number)
                {
                    $same_link_number = false;
                    break;
                }
            }
        }
        
        $this->assertTrue($same_link_number);
        
        DataManager :: delete_content_object_alternatives_by_link_number($base_link_number);
    }

    /**
     * Tests the delete content object alternative with one link, the other link should also be deleted because it's the
     * only one left Deletes the link object for both "Object A" and "Object B"
     */
    public function test_delete_content_object_alternatives_with_one_link()
    {
        $link_number = $this->delete_content_object_alternative_links_helper(2);
        $this->assertEquals(DataManager :: count_content_object_alternatives_by_link_number($link_number), 0);
    }

    /**
     * Tests the delete content object alternative with multiple link, the other links should not be deleted Deletes the
     * link object for "Object A" and not for "Object B and Object C"
     */
    public function test_delete_content_object_alternatives_with_multiple_links()
    {
        $link_number = $this->delete_content_object_alternative_links_helper(3);
        $this->assertEquals(DataManager :: count_content_object_alternatives_by_link_number($link_number), 2);
        
        DataManager :: delete_content_object_alternatives_by_link_number($link_number);
    }

    /**
     * Helper functionality to create content object alternative links and delete the first one and returns the link
     * number of the collection
     * 
     * @param int $number_of_content_objects_to_link
     *
     * @return int
     */
    protected function delete_content_object_alternative_links_helper($number_of_content_objects_to_link)
    {
        $content_objects = self :: $created_objects[Announcement :: class_name()];
        $metadata_element_id = self :: $metadata_elements[self :: METADATA_ELEMENT_LANGUAGE]->get_id();
        
        $base_content_object_id = $content_objects[0]->get_id();
        $link_content_object_ids = array();
        
        for ($i = 1; $i < $number_of_content_objects_to_link; $i ++)
        {
            $link_content_object_ids[] = $content_objects[$i]->get_id();
        }
        
        DataManager :: create_content_object_alternatives(
            $base_content_object_id, 
            $link_content_object_ids, 
            $metadata_element_id);
        
        RecordCache :: reset();
        
        $link_number = DataManager :: get_link_number_for_content_object_and_metadata_element(
            $base_content_object_id, 
            $metadata_element_id);
        
        $content_object_alternative = DataManager :: retrieve_content_object_alternative_by_content_object_and_metadata_element(
            $base_content_object_id, 
            $metadata_element_id);
        
        $content_object_alternative->delete();
        
        RecordCache :: reset();
        
        return $link_number;
    }

    /**
     * Tests the update of a content object alternative to a new metadata element to make sure that the link numbers are
     * correct.
     * Start with a collection of 3 content objects that are connected by "language" (Object A, Object B,
     * Object C) Change the connection between (Object A, Object B) from "language" to "format". Check that there are
     * two new collections: Language collection (Object A, Object C) Format collection (Object A, Object B)
     */
    public function test_update_content_object_alternative_metadata_element()
    {
        $content_objects = self :: $created_objects[Announcement :: class_name()];
        $language_metadata_element_id = self :: $metadata_elements[self :: METADATA_ELEMENT_LANGUAGE]->get_id();
        $format_metadata_element_id = self :: $metadata_elements[self :: METADATA_ELEMENT_FORMAT]->get_id();
        
        $object_a_id = $content_objects[0]->get_id();
        $object_b_id = $content_objects[1]->get_id();
        $object_c_id = $content_objects[2]->get_id();
        
        DataManager :: create_content_object_alternatives(
            $object_a_id, 
            array($object_b_id, $object_c_id), 
            $language_metadata_element_id);
        
        RecordCache :: reset();
        
        $object_b_content_object_alternative = DataManager :: retrieve_content_object_alternative_by_content_object_and_metadata_element(
            $object_b_id, 
            $language_metadata_element_id);
        
        DataManager :: update_content_object_alternative_to_new_metadata_element(
            $object_b_content_object_alternative, 
            $object_a_id, 
            $format_metadata_element_id);
        
        RecordCache :: reset();
        
        $object_a_language_link_number = DataManager :: get_link_number_for_content_object_and_metadata_element(
            $object_a_id, 
            $language_metadata_element_id);
        
        $object_c_language_link_number = DataManager :: get_link_number_for_content_object_and_metadata_element(
            $object_c_id, 
            $language_metadata_element_id);
        
        $object_a_format_link_number = DataManager :: get_link_number_for_content_object_and_metadata_element(
            $object_a_id, 
            $format_metadata_element_id);
        
        $object_b_format_link_number = DataManager :: get_link_number_for_content_object_and_metadata_element(
            $object_b_id, 
            $format_metadata_element_id);
        
        $this->assertTrue(
            $object_a_language_link_number == $object_c_language_link_number &&
                 $object_a_language_link_number != $object_a_format_link_number &&
                 $object_a_format_link_number == $object_b_format_link_number);
        
        DataManager :: delete_content_object_alternatives_by_link_number($object_a_language_link_number);
        DataManager :: delete_content_object_alternatives_by_link_number($object_a_format_link_number);
    }
}