<?php
namespace Chamilo\Core\Repository\Selector\Option;

use Chamilo\Core\Repository\Configuration;
use Chamilo\Core\Repository\Selector\TypeSelectorOption;
use Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Translation\Translation;
use Exception;

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

    public function get_image_path($imageSize = IdentGlyph::SIZE_BIG)
    {
        $templateRegistration = $this->get_template_registration();

        if ($templateRegistration instanceof TemplateRegistration && !$templateRegistration->get_default())
        {
            $glyphTitle = 'TypeName' . $templateRegistration->get_name();
            $glyphNamespace =
                $templateRegistration->get_content_object_type() . '\Template\\' . $templateRegistration->get_name();
        }
        else
        {
            $glyphTitle = null;
            $glyphNamespace = $templateRegistration->get_content_object_type();
        }

        return new NamespaceIdentGlyph($glyphNamespace, true, false, false, $imageSize, array('fa-fw'), $glyphTitle);
    }

    public function get_label()
    {
        return $this->get_name();
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
     * Get the TemplateRegistration for the option
     *
     * @return use core\repository\common\template\TemplateRegistration
     * @throws \Exception
     */
    public function get_template_registration()
    {
        if ($this->get_template_registration_id())
        {
            return Configuration::registration_by_id(
                (int) $this->get_template_registration_id()
            );
        }
        else
        {
            throw new Exception(Translation::get('NoTemplateRegistrationSelected'));
        }
    }

    /*
     * (non-PHPdoc) @see \core\repository\TypeSelectorOption::get_image_path()
     */

    /**
     *
     * @return int
     */
    public function get_template_registration_id()
    {
        return $this->template_registration_id;
    }

    /*
     * (non-PHPdoc) @see \core\repository\TypeSelectorOption::get_label()
     */

    /**
     *
     * @param number $template_registration_id
     */
    public function set_template_registration_id($template_registration_id)
    {
        $this->template_registration_id = $template_registration_id;
    }
}