<?php
namespace Chamilo\Core\Repository\Common\Template;

use DOMXPath;

/**
 *
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TemplateTranslation
{

    /**
     *
     * @var multitype:multitype:string
     */
    private $storage;

    public function __construct($storage = array())
    {
        $this->storage = $storage;
    }

    /**
     *
     * @return \core\repository\multitype:multitype:boolean
     */
    public function get_storage()
    {
        return $this->storage;
    }

    /**
     *
     * @param \core\repository\multitype:multitype:boolean $storage
     */
    public function set_storage($storage)
    {
        $this->storage = $storage;
    }

    /**
     *
     * @param string $language
     * @param string $variable
     * @return \core\repository\multitype:multitype:boolean
     */
    public function translate($language, $variable)
    {
        $storage = $this->get_storage();
        
        if (isset($storage[$language]) && isset($storage[$language][$variable]))
        {
            return $storage[$language][$variable];
        }
        else
        {
            return $storage;
        }
    }

    /**
     *
     * @param DOMXPath $dom_xpath
     * @return TemplateTranslation
     * @throws Exception
     */
    public static function get(DOMXPath $dom_xpath)
    {
        $storage = array();
        
        $variables = $dom_xpath->query('/template/translations/variable');
        
        foreach ($variables as $variable)
        {
            $value = $variable->getAttribute('value');
            $translations = $dom_xpath->query('translation', $variable);
            
            foreach ($translations as $translation)
            {
                $storage[$translation->getAttribute('language')][$value] = $translation->nodeValue;
            }
        }
        
        return new self($storage);
    }
}