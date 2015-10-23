<?php
namespace Chamilo\Libraries\File\Properties;

use Chamilo\Libraries\Utilities\StringUtilities;

class WebpageProperties
{
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_CONTENT_TYPE = 'content_type';
    const PROPERTY_ENCODING = 'encoding';
    const PROPERTY_FILE_PROPERTIES = 'file_properties';

    /**
     *
     * @var multitype:string
     */
    private $properties;

    public function get_properties()
    {
        return $this->properties;
    }

    public function set_properties($properties)
    {
        $this->properties = $properties;
    }

    public function set_property($property, $value)
    {
        $this->properties[$property] = $value;
    }

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
        return $this->get_property(self :: PROPERTY_TITLE);
    }

    /**
     *
     * @param string $title
     */
    public function set_title($title)
    {
        $this->set_property(self :: PROPERTY_TITLE, $title);
    }

    /**
     *
     * @return string
     */
    public function get_description()
    {
        return $this->get_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     *
     * @param string $description
     */
    public function set_description($description)
    {
        $this->set_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     *
     * @return string
     */
    public function get_content_type()
    {
        return $this->get_property(self :: PROPERTY_CONTENT_TYPE);
    }

    /**
     *
     * @param string $content_type
     */
    public function set_content_type($content_type)
    {
        $this->set_property(self :: PROPERTY_CONTENT_TYPE, $content_type);
    }

    /**
     *
     * @return string
     */
    public function get_encoding()
    {
        return $this->get_property(self :: PROPERTY_ENCODING);
    }

    /**
     *
     * @param string $encoding
     */
    public function set_encoding($encoding)
    {
        $this->set_property(self :: PROPERTY_ENCODING, $encoding);
    }

    /**
     *
     * @return \libraries\file\FileProperties
     */
    public function get_file_properties()
    {
        return $this->get_property(self :: PROPERTY_FILE_PROPERTIES);
    }

    /**
     *
     * @param \libraries\file\FileProperties $file_properties
     */
    public function set_file_properties($file_properties)
    {
        $this->set_property(self :: PROPERTY_FILE_PROPERTIES, $file_properties);
    }

    /**
     *
     * @param multitype:string $file
     * @return \libraries\WebpageProperties
     */
    public static function from_upload($file)
    {
        return self :: determine_properties(FileProperties :: from_upload($file));
    }

    /**
     *
     * @param string $path
     * @return \libraries\WebpageProperties
     */
    public static function from_path($path)
    {
        return self :: determine_properties(FileProperties :: from_path($path));
    }

    /**
     *
     * @param \libraries\file\FileProperties $file_properties
     * @return \libraries\WebpageProperties
     */
    public static function determine_properties($file_properties)
    {
        $dom_document = new \DOMDocument();
        $dom_document->loadHTMLFile($file_properties->get_path());
        $dom_xpath = new \DOMXPath($dom_document);

        $properties = new self();
        $properties->set_file_properties($file_properties);

        $title = $dom_xpath->query('/html/head/title')->item(0)->nodeValue;
        if (StringUtilities :: getInstance()->hasValue(trim($title), true))
        {
            $properties->set_title(trim($title));
        }

        $description = $dom_xpath->query('/html/head/meta[@name="description"]')->item(0);
        if ($description instanceof \DOMNode)
        {
            if (StringUtilities :: getInstance()->hasValue(trim($description->getAttribute('content')), true))
            {
                $properties->set_description(trim($description->getAttribute('content')));
            }
        }

        $content_type = $dom_xpath->query('/html/head/meta[@http-equiv="content-type"]')->item(0);
        if ($content_type instanceof \DOMNode)
        {
            $content_type_parts = explode(';', $content_type->getAttribute('content'));
            $properties->set_content_type(trim($content_type_parts[0]));

            if (StringUtilities :: getInstance()->hasValue(trim($content_type_parts[1]), true))
            {
                $encoding_parts = explode('=', trim($content_type_parts[1]));
                if (StringUtilities :: getInstance()->hasValue(trim($encoding_parts[1]), true))
                {
                    $properties->set_encoding(trim($encoding_parts[1]));
                }
            }
        }

        $charset = $dom_xpath->query('/html/head/meta[@charset]')->item(0);
        if ($charset instanceof \DOMNode)
        {
            $properties->set_encoding($charset->getAttribute('charset'));
        }

        return $properties;
    }
}
