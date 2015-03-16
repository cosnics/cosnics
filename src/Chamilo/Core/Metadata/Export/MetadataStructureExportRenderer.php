<?php
namespace Chamilo\Core\Metadata\Export;

/**
 * Interface to describe the render component of the metadata structure exporter
 * 
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface MetadataStructureExportRenderer
{

    /**
     * Renders the metadata structure export
     * 
     * @param Schema[]   $schemas
     * @param ControlledVocabulary[] $controlled_vocabulary
     *
     * @return string
     */
    public function render(array $schemas, array $controlled_vocabulary);
}