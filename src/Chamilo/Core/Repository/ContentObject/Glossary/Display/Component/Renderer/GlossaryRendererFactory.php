<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer;

use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * Factory class for the GlossaryRenderer
 * 
 * @package repository\content_object\glossary
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GlossaryRendererFactory
{
    const TYPE_LIST = 'list';
    const TYPE_TABLE = 'table';

    /**
     * Initializes the GlossaryRenderer
     * 
     * @param string $type
     * @param mixed $component
     * @param Glossary $glossary
     * @param string $search_query
     *
     * @throws \Exception
     *
     * @return GlossaryRenderer
     */
    public static function factory($type, $component, $glossary, $search_query = null)
    {
        $class = __NAMESPACE__ . '\Type\\' . StringUtilities::getInstance()->createString($type)->upperCamelize() .
             'GlossaryRenderer';
        if (! class_exists($class))
        {
            throw new Exception('Could not find a glossary renderer of type ' . $type);
        }
        
        return new $class($component, $glossary, $search_query);
    }

    /**
     * Creates an instance of the type and renders the glossary
     * 
     * @param string $type
     * @param mixed $component
     * @param Glossary $glossary
     * @param string $search_query
     *
     * @return string
     */
    public static function launch($type, $component, $glossary, $search_query = null)
    {
        return self::factory($type, $component, $glossary, $search_query)->render();
    }
}