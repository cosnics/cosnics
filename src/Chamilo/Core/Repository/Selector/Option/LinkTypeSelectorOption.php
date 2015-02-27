<?php
namespace Chamilo\Core\Repository\Selector\Option;

use Chamilo\Core\Repository\Selector\TypeSelectorOption;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkTypeSelectorOption implements TypeSelectorOption
{

    /**
     *
     * @var string
     */
    private $context;

    /**
     *
     * @var string
     */
    private $type;

    /**
     *
     * @var string
     */
    private $url;

    /**
     *
     * @param string $context
     * @param string $type
     * @param string $label
     */
    public function __construct($context, $type, $url)
    {
        $this->context = $context;
        $this->type = $type;
        $this->url = $url;
    }

    /**
     *
     * @return string
     */
    public function get_context()
    {
        return $this->context;
    }

    /**
     *
     * @return string
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     *
     * @return string
     */
    public function get_url()
    {
        return $this->url;
    }
    
    /*
     * (non-PHPdoc) @see \core\repository\TypeSelectorOption::get_image_path()
     */
    public function get_image_path()
    {
        return Theme :: getInstance()->getImagesPath($this->get_context()) . '/type_selector/' . $this->get_type() .
             '.png';
    }
    
    /*
     * (non-PHPdoc) @see \core\repository\TypeSelectorOption::get_label()
     */
    public function get_label()
    {
        return Translation :: get(
            (string) StringUtilities :: getInstance()->createString($this->get_type())->upperCamelize(), 
            null, 
            $this->get_context());
    }
}