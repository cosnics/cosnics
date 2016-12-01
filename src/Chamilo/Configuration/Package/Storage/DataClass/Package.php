<?php
namespace Chamilo\Configuration\Package\Storage\DataClass;

use Chamilo\Configuration\Package\Properties\Authors\Author;
use Chamilo\Configuration\Package\Properties\Dependencies\Dependencies;
use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use DOMDocument;
use DOMXPath;
use Exception;

/**
 *
 * @author Hans De Bisschop
 */
class Package extends DataClass
{
    
    /**
     * Package properties
     */
    const PROPERTY_CODE = 'code';
    const PROPERTY_CONTEXT = 'context';
    const PROPERTY_NAME = 'name';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_CATEGORY = 'category';
    const PROPERTY_AUTHORS = 'authors';
    const PROPERTY_VERSION = 'version';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_EXTRA = 'extra';
    
    // Dependencies
    const PROPERTY_PRE_DEPENDS = 'pre_depends';
    const PROPERTY_DEPENDS = 'depends';
    const PROPERTY_RECOMMENDS = 'recommends';
    const PROPERTY_SUGGESTS = 'suggests';
    const PROPERTY_ENHANCES = 'enhances';

    /**
     * Get the default properties
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_CODE;
        $extended_property_names[] = self::PROPERTY_CONTEXT;
        $extended_property_names[] = self::PROPERTY_NAME;
        $extended_property_names[] = self::PROPERTY_TYPE;
        $extended_property_names[] = self::PROPERTY_CATEGORY;
        $extended_property_names[] = self::PROPERTY_AUTHORS;
        $extended_property_names[] = self::PROPERTY_VERSION;
        $extended_property_names[] = self::PROPERTY_DESCRIPTION;
        $extended_property_names[] = self::PROPERTY_PRE_DEPENDS;
        $extended_property_names[] = self::PROPERTY_DEPENDS;
        $extended_property_names[] = self::PROPERTY_RECOMMENDS;
        $extended_property_names[] = self::PROPERTY_SUGGESTS;
        $extended_property_names[] = self::PROPERTY_ENHANCES;
        $extended_property_names[] = self::PROPERTY_EXTRA;
        
        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * Returns the extra of this Package.
     * 
     * @return the code.
     */
    public function get_extra()
    {
        return $this->get_default_property(self::PROPERTY_EXTRA);
    }

    /**
     * Sets the extra of this Package.
     * 
     * @param extra
     */
    public function set_extra($extra)
    {
        $this->set_default_property(self::PROPERTY_EXTRA, $extra);
    }

    /**
     * Returns the code of this Package.
     * 
     * @return the code.
     */
    public function get_code()
    {
        return $this->get_default_property(self::PROPERTY_CODE);
    }

    /**
     * Sets the code of this Package.
     * 
     * @param code
     */
    public function set_code($code)
    {
        $this->set_default_property(self::PROPERTY_CODE, $code);
    }

    /**
     * Returns the context of this Package.
     * 
     * @return the context.
     */
    public function get_context()
    {
        return $this->get_default_property(self::PROPERTY_CONTEXT);
    }

    /**
     * Sets the context of this Package.
     * 
     * @param context
     */
    public function set_context($context)
    {
        $this->set_default_property(self::PROPERTY_CONTEXT, $context);
    }

    /**
     * Returns the name of this Package.
     * 
     * @return the name.
     */
    public function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    /**
     * Sets the name of this Package.
     * 
     * @param name
     */
    public function set_name($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    /**
     * Returns the type of this Package.
     * 
     * @return the type.
     */
    public function get_type()
    {
        return $this->get_default_property(self::PROPERTY_TYPE);
    }

    /**
     * Sets the type of this Package.
     * 
     * @param type
     */
    public function set_type($type)
    {
        $this->set_default_property(self::PROPERTY_TYPE, $type);
    }

    /**
     * Returns the category of this Package.
     * 
     * @return the category.
     */
    public function get_category()
    {
        return $this->get_default_property(self::PROPERTY_CATEGORY);
    }

    /**
     * Sets the category of this Package.
     * 
     * @param category
     */
    public function set_category($category)
    {
        $this->set_default_property(self::PROPERTY_CATEGORY, $category);
    }

    /**
     * Returns the authors of this Package.
     * 
     * @return the authors.
     */
    public function get_authors()
    {
        return unserialize($this->get_default_property(self::PROPERTY_AUTHORS));
    }

    /**
     * Sets the authors of this Package.
     * 
     * @param authors
     */
    public function set_authors($authors)
    {
        $this->set_default_property(self::PROPERTY_AUTHORS, serialize($authors));
    }

    public function add_author($author)
    {
        $this->authors[] = $author;
    }

    /**
     * Returns the version of this Package.
     * 
     * @return the version.
     */
    public function get_version()
    {
        return $this->get_default_property(self::PROPERTY_VERSION);
    }

    /**
     * Sets the version of this Package.
     * 
     * @param version
     */
    public function set_version($version)
    {
        $this->set_default_property(self::PROPERTY_VERSION, $version);
    }

    /**
     * Returns the description of this Package.
     * 
     * @return the description.
     */
    public function get_description()
    {
        return $this->get_default_property(self::PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this Package.
     * 
     * @param description
     */
    public function set_description($description)
    {
        $this->set_default_property(self::PROPERTY_DESCRIPTION, $description);
    }

    /**
     *
     * @return Dependencies Dependency
     */
    public function get_pre_depends()
    {
        return unserialize($this->get_default_property(self::PROPERTY_PRE_DEPENDS));
    }

    /**
     *
     * @param $pre_depends Dependencies|Dependency
     */
    public function set_pre_depends($pre_depends)
    {
        $this->set_default_property(self::PROPERTY_PRE_DEPENDS, serialize($pre_depends));
    }

    /**
     *
     * @return Dependencies Dependency
     */
    public function get_depends()
    {
        return unserialize($this->get_default_property(self::PROPERTY_DEPENDS));
    }

    /**
     *
     * @param $depends Dependencies|Dependency
     */
    public function set_depends($depends)
    {
        $this->set_default_property(self::PROPERTY_DEPENDS, serialize($depends));
    }

    /**
     *
     * @return Dependencies Dependency
     */
    public function get_recommends()
    {
        return unserialize($this->get_default_property(self::PROPERTY_RECOMMENDS));
    }

    /**
     *
     * @param $recommends Dependencies|Dependency
     */
    public function set_recommends($recommends)
    {
        $this->set_default_property(self::PROPERTY_RECOMMENDS, serialize($recommends));
    }

    /**
     *
     * @return Dependencies Dependency
     */
    public function get_suggests()
    {
        return unserialize($this->get_default_property(self::PROPERTY_SUGGESTS));
    }

    /**
     *
     * @param $suggests Dependencies|Dependency
     */
    public function set_suggests($suggests)
    {
        $this->set_default_property(self::PROPERTY_SUGGESTS, serialize($suggests));
    }

    /**
     *
     * @return Dependencies Dependency
     */
    public function get_enhances()
    {
        return unserialize($this->get_default_property(self::PROPERTY_ENHANCES));
    }

    /**
     *
     * @param $enhances Dependencies|Dependency
     */
    public function set_enhances($enhances)
    {
        $this->set_default_property(self::PROPERTY_ENHANCES, serialize($enhances));
    }

    public function has_dependencies()
    {
        return (! is_null($this->get_pre_depends()) || ! is_null($this->get_depends()) || ! is_null(
            $this->get_recommends()) || ! is_null($this->get_suggests()) || ! is_null($this->get_enhances()));
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public static function exists($context)
    {
        $path = Path::getInstance()->namespaceToFullPath($context) . 'package.info';
        
        if (file_exists($path))
        {
            return $path;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @param string $context
     * @throws Exception
     * @return \configuration\package\storage\data_class\Package
     */
    public static function get($context)
    {
        $path = self::exists($context);
        
        if (! $path)
        {
            throw new Exception(Translation::get('InvalidPackageContext', array('CONTEXT' => $context)));
        }
        
        $dom_document = new DOMDocument('1.0', 'UTF-8');
        $dom_document->load($path);
        $dom_xpath = new DOMXPath($dom_document);
        
        $package_list = $dom_xpath->query('/packages/package');
        
        if ($package_list->length > 1)
        {
            throw new Exception(Translation::get('MultipackageFileNotAllowed', array('CONTEXT' => $context)));
        }
        return self::parse_package($dom_xpath, $package_list->item(0));
    }

    /**
     *
     * @param \DOMXPath $dom_xpath
     * @param \DOMElement $package_node
     * @return \configuration\package\storage\data_class\Package
     */
    public static function parse_package(\DOMXPath $dom_xpath, \DOMElement $package_node)
    {
        $package = new static();
        
        // Simple properties, containing a singular string or integer
        $simple_properties = array(
            self::PROPERTY_CODE, 
            self::PROPERTY_CONTEXT, 
            self::PROPERTY_NAME, 
            self::PROPERTY_TYPE, 
            self::PROPERTY_CATEGORY, 
            self::PROPERTY_VERSION, 
            self::PROPERTY_DESCRIPTION);
        
        foreach ($simple_properties as $simple_property)
        {
            $node = $dom_xpath->query($simple_property, $package_node)->item(0);
            
            if ($node instanceof \DOMNode && $node->hasChildNodes())
            {
                $package->set_default_property($simple_property, trim($node->nodeValue));
            }
            else
            {
                $package->set_default_property($simple_property, null);
            }
        }
        
        $extra = $dom_xpath->query('extra/*', $package_node);
        $extras = array();
        foreach ($extra as $extra_node)
        {
            $extras[$extra_node->nodeName] = $extra_node->nodeValue;
        }
        $package->set_extra($extras);
        
        // Authors
        $author_nodes = $dom_xpath->query('authors/author', $package_node);
        foreach ($author_nodes as $author_node)
        {
            $name = $dom_xpath->query('name', $author_node)->item(0);
            $email = $dom_xpath->query('email', $author_node)->item(0);
            $company = $dom_xpath->query('company', $author_node)->item(0);
            
            $package->add_author(
                new Author(
                    $name instanceof \DOMNode && $name->hasChildNodes() ? $name->nodeValue : null, 
                    $email instanceof \DOMNode && $email->hasChildNodes() ? $email->nodeValue : null, 
                    $company instanceof \DOMNode && $company->hasChildNodes() ? $company->nodeValue : null));
        }
        
        // Dependencies
        $package->set_pre_depends(
            self::parse_dependencies(
                $dom_xpath, 
                $dom_xpath->query('pre-depends/dependencies | pre-depends/dependency', $package_node)->item(0)));
        
        $package->set_depends(
            self::parse_dependencies(
                $dom_xpath, 
                $dom_xpath->query('depends/dependencies | depends/dependency', $package_node)->item(0)));
        
        $package->set_recommends(
            self::parse_dependencies(
                $dom_xpath, 
                $dom_xpath->query('recommends/dependencies | recommends/dependency', $package_node)->item(0)));
        
        $package->set_suggests(
            self::parse_dependencies(
                $dom_xpath, 
                $dom_xpath->query('suggests/dependencies | suggests/dependency', $package_node)->item(0)));
        
        $package->set_enhances(
            self::parse_dependencies(
                $dom_xpath, 
                $dom_xpath->query('enhances/dependencies | enhances/dependency', $package_node)->item(0)));
        
        return $package;
    }

    /**
     *
     * @param \DOMXPath $dom_xpath
     * @param \DOMElement $dom_node
     * @return void \configuration\package\Dependencies
     */
    private static function parse_dependencies(\DOMXPath $dom_xpath, \DOMElement $dom_node = null)
    {
        if (is_null($dom_node))
        {
            return null;
        }
        
        if ($dom_node->tagName == 'dependencies')
        {
            $dependencies = new Dependencies($dom_node->getAttribute('operator'));
            $child_nodes = $dom_xpath->query('dependencies | dependency', $dom_node);
            
            foreach ($child_nodes as $child_node)
            {
                $dependencies->add_dependency(self::parse_dependencies($dom_xpath, $child_node));
            }
            
            return $dependencies;
        }
        elseif ($dom_node->tagName == 'dependency')
        {
            return Dependency::from_dom_node($dom_xpath, $dom_node);
        }
    }
}
