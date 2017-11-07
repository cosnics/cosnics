<?php
namespace Chamilo\Core\Repository\Selector\Option;

use Chamilo\Core\Repository\Selector\TypeSelectorOption;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

/**
 * An option in a TypeSelector
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectTypeSelectorOption implements TypeSelectorOption
{

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var int
     */
    private $template_registration_id;

    /**
     *
     * @param string $content_object_type
     * @param string $name
     * @param int $template_registration_id $param string $url
     */
    public function __construct($name, $template_registration_id)
    {
        $this->name = $name;
        $this->template_registration_id = $template_registration_id;
    }

    /**
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return int
     */
    public function get_template_registration_id()
    {
        return $this->template_registration_id;
    }

    /**
     *
     * @param number $template_registration_id
     */
    public function set_template_registration_id($template_registration_id)
    {
        $this->template_registration_id = $template_registration_id;
    }

    /**
     * Get the TemplateRegistration for the option
     * 
     * @return use core\repository\common\template\TemplateRegistration
     * @throws \Exception
     */
    public function get_template_registration()
    {
        if ($this->get_template_registration_id())
        {
            return \Chamilo\Core\Repository\Configuration::registration_by_id(
                (int) $this->get_template_registration_id());
        }
        else
        {
            throw new \Exception(Translation::get('NoTemplateRegistrationSelected'));
        }
    }

    /*
     * (non-PHPdoc) @see \core\repository\TypeSelectorOption::get_image_path()
     */
    public function get_image_path($imageSize = Theme :: ICON_BIG)
    {
        $namespace = $this->get_template_registration()->get_content_object_type();
        
        return Theme::getInstance()->getImagePath(
            $namespace, 
            'Logo/' . ($this->get_template_registration_id() ? 'Template/' .
                 $this->get_template_registration()->get_name() . '/' : '') . $imageSize);
    }

    /*
     * (non-PHPdoc) @see \core\repository\TypeSelectorOption::get_label()
     */
    public function get_label()
    {
        return $this->get_name();
    }
}