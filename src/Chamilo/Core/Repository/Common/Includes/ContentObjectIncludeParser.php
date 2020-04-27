<?php
namespace Chamilo\Core\Repository\Common\Includes;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib
 */
abstract class ContentObjectIncludeParser
{

    /**
     * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    private $contentObject;

    /**
     * @var string[][]
     */
    private $values;

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string[][] $values
     */
    public function __construct(ContentObject $contentObject, array $values)
    {
        $this->contentObject = $contentObject;
        $this->values = $values;
    }

    /**
     * @param string $type
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string[][] $values
     *
     * @return \Chamilo\Core\Repository\Common\Includes\ContentObjectIncludeParser
     */
    public static function factory($type, ContentObject $contentObject, array $values)
    {
        $class =
            __NAMESPACE__ . '\Type\Include' . StringUtilities::getInstance()->createString($type)->upperCamelize() .
            'Parser';

        return new $class($contentObject, $values);
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getContentObject(): ContentObject
    {
        return $this->contentObject;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     */
    public function setContentObject(ContentObject $contentObject): void
    {
        $this->contentObject = $contentObject;
    }

    /**
     * @return string[][]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param string[][] $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    /**
     * @return string[]
     */
    public static function get_include_types()
    {
        return array('image', 'embed', 'youtube', 'chamilo');
    }

    abstract public function parseHtmlEditorField();

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string[][] $values
     */
    public static function parse_includes(ContentObject $contentObject, array $values)
    {
        if ($contentObject->isIdentified())
        {
            /*
             * TODO: Make this faster by providing a function that matches the existing IDs against the ones that need
             * to be added, and attaches and detaches accordingly.
             */
            foreach ($contentObject->get_includes() as $included_object)
            {
                $contentObject->exclude_content_object($included_object->getId());
            }
        }

        foreach (self::get_include_types() as $include_type)
        {
            $parser = self::factory($include_type, $contentObject, $values);
            $parser->parseHtmlEditorField();
        }
    }
}
