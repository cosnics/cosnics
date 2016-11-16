<?php
namespace Chamilo\Core\Lynx\Remote\DataClass;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Lynx\Source\DataClass\Source;
use Chamilo\Libraries\File\Path;
use DOMDocument;
use DOMXPath;

/**
 *
 * @package core.lynx.package
 * @author Hans De Bisschop
 */
class Package extends \Chamilo\Configuration\Package\Storage\DataClass\Package
{
    const PROPERTY_SOURCE_ID = 'source_id';

    /**
     *
     * @var \core\lynx\source\data_class\Source
     */
    private $source;

    /**
     *
     * @param multitype:string $extended_property_names
     * @return multitype:string
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(array(self::PROPERTY_SOURCE_ID));
    }

    /**
     * Returns the source_id of this Package.
     * 
     * @return the source_id.
     */
    public function get_source_id()
    {
        return $this->get_default_property(self::PROPERTY_SOURCE_ID);
    }

    /**
     * Sets the source_id of this Package.
     * 
     * @param source_id
     */
    public function set_source_id($source_id)
    {
        $this->set_default_property(self::PROPERTY_SOURCE_ID, $source_id);
    }

    /**
     *
     * @return \core\lynx\source\data_class\Source
     */
    public function get_source()
    {
        if (! isset($this->source))
        {
            $this->source = \Chamilo\Core\Lynx\Source\DataManager::retrieve_by_id(
                Source::class_name(), 
                (int) $this->get_source_id());
        }
        return $this->source;
    }

    /**
     *
     * @param \core\lynx\source\data_class\Source $source
     * @return multitype:\core\lynx\remote\Package
     */
    public static function collection(Source $source)
    {
        $dom_document = new DOMDocument('1.0', 'UTF-8');
        $dom_document->load($source->get_uri() . '/packages.xml');
        $dom_xpath = new DOMXPath($dom_document);
        
        $package_list = $dom_xpath->query('/packages/package');
        
        $packages = array();
        foreach ($package_list as $package_node)
        {
            $package = static::parse_package($dom_xpath, $package_node);
            $package->set_source_id($source->get_id());
            
            $packages[] = $package;
        }
        
        return $packages;
    }

    public function is_downloadable()
    {
        // Check whether there is a registration
        if (Configuration::is_registered($this->get_context()))
        {
            return false;
        }
        
        // Check whether the location already exists
        $path = Path::getInstance()->namespaceToFullPath($this->get_context());
        
        if (is_dir($path))
        {
            return false;
        }
        
        return true;
    }

    public function get_source_filename()
    {
        return $this->get_source()->get_uri() . $this->get_filename();
    }
}
