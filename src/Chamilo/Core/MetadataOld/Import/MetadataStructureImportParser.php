<?php
namespace Chamilo\Core\MetadataOld\Import;

/**
 * Interface to determine the parser for the imported data
 * 
 * @package core\metadata
 */
interface MetadataStructureImportParser
{

    /**
     * Parses the imported data
     */
    public function parse();

    /**
     * Returns an array of the imported schemas
     * 
     * @return \Chamilo\Core\MetadataOld\schema\storage\data_class\Schema[]
     */
    public function get_schemas();

    /**
     * Returns an array of the imported controlled_vocabularies
     * 
     * @return \Chamilo\Core\MetadataOld\controlled_vocabulary\storage\data_class\ControlledVocabulary[]
     */
    public function get_controlled_vocabularies();
}