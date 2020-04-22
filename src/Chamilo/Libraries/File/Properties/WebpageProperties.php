<?php
namespace Chamilo\Libraries\File\Properties;

use Chamilo\Libraries\Utilities\StringUtilities;
use DOMDocument;
use DOMNode;
use DOMXPath;

/**
 *
 * @package Chamilo\Libraries\File\Properties
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WebpageProperties
{
    const PROPERTY_CONTENT_TYPE = 'content_type';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_ENCODING = 'encoding';
    const PROPERTY_FILE_PROPERTIES = 'file_properties';
    const PROPERTY_TITLE = 'title';

    /**
     *
     * @var string[]
     */
    private $properties;

    /**
     *
     * @param \Chamilo\Libraries\File\Properties\FileProperties $fileProperties
     *
     * @return \Chamilo\Libraries\File\Properties\WebpageProperties
     */
    public static function determine_properties($fileProperties)
    {
        $domDocument = new DOMDocument();
        $domDocument->loadHTMLFile($fileProperties->get_path());
        $domXpath = new DOMXPath($domDocument);

        $properties = new self();
        $properties->set_file_properties($fileProperties);

        $title = $domXpath->query('/html/head/title')->item(0)->nodeValue;

        if (StringUtilities::getInstance()->hasValue(trim($title), true))
        {
            $properties->set_title(trim($title));
        }

        $description = $domXpath->query('/html/head/meta[@name="description"]')->item(0);

        if ($description instanceof DOMNode)
        {
            if (StringUtilities::getInstance()->hasValue(trim($description->getAttribute('content')), true))
            {
                $properties->set_description(trim($description->getAttribute('content')));
            }
        }

        $contentType = $domXpath->query('/html/head/meta[@http-equiv="content-type"]')->item(0);

        if ($contentType instanceof DOMNode)
        {
            $contentTypeParts = explode(';', $contentType->getAttribute('content'));
            $properties->set_content_type(trim($contentTypeParts[0]));

            if (StringUtilities::getInstance()->hasValue(trim($contentTypeParts[1]), true))
            {
                $encodingParts = explode('=', trim($contentTypeParts[1]));

                if (StringUtilities::getInstance()->hasValue(trim($encodingParts[1]), true))
                {
                    $properties->set_encoding(trim($encodingParts[1]));
                }
            }
        }

        $charset = $domXpath->query('/html/head/meta[@charset]')->item(0);

        if ($charset instanceof DOMNode)
        {
            $properties->set_encoding($charset->getAttribute('charset'));
        }

        return $properties;
    }

    /**
     *
     * @param string $path
     *
     * @return \Chamilo\Libraries\File\Properties\WebpageProperties
     */
    public static function from_path($path)
    {
        return self::determine_properties(FileProperties::from_path($path));
    }

    /**
     *
     * @param string[] $fileDescription
     *
     * @return \Chamilo\Libraries\File\Properties\WebpageProperties
     */
    public static function from_upload($fileDescription)
    {
        return self::determine_properties(FileProperties::from_upload($fileDescription));
    }

    /**
     *
     * @return string
     */
    public function get_content_type()
    {
        return $this->get_property(self::PROPERTY_CONTENT_TYPE);
    }

    /**
     *
     * @return string
     */
    public function get_description()
    {
        return $this->get_property(self::PROPERTY_DESCRIPTION);
    }

    /**
     *
     * @return string
     */
    public function get_encoding()
    {
        return $this->get_property(self::PROPERTY_ENCODING);
    }

    /**
     *
     * @return \Chamilo\Libraries\File\Properties\WebpageProperties
     */
    public function get_file_properties()
    {
        return $this->get_property(self::PROPERTY_FILE_PROPERTIES);
    }

    /**
     *
     * @return string[]
     */
    public function get_properties()
    {
        return $this->properties;
    }

    /**
     *
     * @param string[] $properties
     */
    public function set_properties($properties)
    {
        $this->properties = $properties;
    }

    /**
     *
     * @param string $property
     *
     * @return string
     */
    public function get_property($property)
    {
        return $this->properties[$property];
    }

    /**
     *
     * @return string
     */
    public function get_title()
    {
        return $this->get_property(self::PROPERTY_TITLE);
    }

    /**
     *
     * @param string $contentType
     */
    public function set_content_type($contentType)
    {
        $this->set_property(self::PROPERTY_CONTENT_TYPE, $contentType);
    }

    /**
     *
     * @param string $description
     */
    public function set_description($description)
    {
        $this->set_property(self::PROPERTY_DESCRIPTION, $description);
    }

    /**
     *
     * @param string $encoding
     */
    public function set_encoding($encoding)
    {
        $this->set_property(self::PROPERTY_ENCODING, $encoding);
    }

    /**
     *
     * @param \Chamilo\Libraries\File\Properties\WebpageProperties
     */
    public function set_file_properties($fileProperties)
    {
        $this->set_property(self::PROPERTY_FILE_PROPERTIES, $fileProperties);
    }

    /**
     *
     * @param string $property
     * @param string $value
     */
    public function set_property($property, $value)
    {
        $this->properties[$property] = $value;
    }

    /**
     *
     * @param string $title
     */
    public function set_title($title)
    {
        $this->set_property(self::PROPERTY_TITLE, $title);
    }
}
