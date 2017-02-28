<?php
namespace Chamilo\Core\Repository\Common\Template;

use Chamilo\Core\Repository\Service\ContentObjectTemplate\ContentObjectTemplateLoader;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Template
{

    /**
     *
     * @var TemplateConfiguration
     */
    private $configuration;

    /**
     *
     * @var ContentObject
     */
    private $content_object;

    /**
     *
     * @var TemplateTranslation
     */
    private $translation;

    /**
     *
     * @param TemplateConfiguration $configuration
     * @param ContentObject $content_object
     * @param TemplateTranslation $translation
     */
    public function __construct(TemplateConfiguration $configuration, ContentObject $content_object, 
        TemplateTranslation $translation)
    {
        $this->set_configuration($configuration);
        $this->set_content_object($content_object);
        $this->set_translation($translation);
    }

    /**
     *
     * @return TemplateConfiguration
     */
    public function get_configuration()
    {
        return $this->configuration;
    }

    /**
     *
     * @param TemplateConfiguration $configuration
     */
    public function set_configuration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     *
     * @return ContentObject
     */
    public function get_content_object()
    {
        return $this->content_object;
    }

    /**
     *
     * @param ContentObject $content_object
     */
    public function set_content_object($content_object)
    {
        $this->content_object = $content_object;
    }

    /**
     *
     * @return TemplateTranslation
     */
    public function get_translation()
    {
        return $this->translation;
    }

    /**
     *
     * @param TemplateTranslation $translation
     */
    public function set_translation($translation)
    {
        $this->translation = $translation;
    }

    public function translate($variable)
    {
        $language = Translation::getInstance()->getLanguageIsocode();
        return $this->get_translation()->translate($language, $variable);
    }

    /**
     *
     * @param string $content_object_type
     * @param string $template_name
     * @return Template
     * @throws \Exception
     */
    public static function get($content_object_type, $template_name)
    {
        $contentObjectTemplateLoader = new ContentObjectTemplateLoader(PathBuilder::getInstance());
        return $contentObjectTemplateLoader->loadTemplate($content_object_type, $template_name);
    }
}