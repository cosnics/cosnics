<?php

namespace Chamilo\Core\Repository\Service\ContentObjectTemplate;

use Chamilo\Core\Repository\Common\Template\Template;
use Chamilo\Libraries\File\PathBuilder;
use Symfony\Component\Finder\Finder;

/**
 * Loads templates for content objects
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectTemplateLoader
{
    /**
     * @var PathBuilder
     */
    protected $pathBuilder;

    /**
     * ContentObjectTemplateLoader constructor.
     *
     * @param PathBuilder $pathBuilder
     */
    public function __construct(PathBuilder $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    /**
     * Loads templates for a given content object
     *
     * @param string $contentObjectNamespace
     *
     * @return Template[]
     *
     * @throws \Exception
     */
    public function loadTemplates($contentObjectNamespace)
    {
        return $this->loadTemplatesByNameOrPattern($contentObjectNamespace, '*');
    }

    /**
     * Loads a single template by a given name
     *
     * @param string $contentObjectNamespace
     * @param string $templateName
     *
     * @return Template
     *
     * @throws \Exception
     */
    public function loadTemplate($contentObjectNamespace, $templateName)
    {
        $templates = $this->loadTemplatesByNameOrPattern($contentObjectNamespace, $templateName);
        if(empty($templates))
        {
            throw new \RuntimeException('Could not load the template with the name ' . $templateName);
        }

        return array_shift($templateName);
    }

    /**
     * @param string $contentObjectNamespace
     * @param string $templateNameOrPattern
     *
     * @return Template[]
     *
     * @throws \Exception
     */
    protected function loadTemplatesByNameOrPattern($contentObjectNamespace, $templateNameOrPattern = '*')
    {
        $contentObjectPath = $this->pathBuilder->namespaceToFullPath($contentObjectNamespace);
        if (!file_exists($contentObjectPath) || !is_dir($contentObjectPath))
        {
            throw new \Exception(sprintf('The given content object path %s does not exist', $contentObjectPath));
        }

        $templatePath = $contentObjectPath . 'Template';

        if (!file_exists($templatePath) || !is_dir($templatePath))
        {
            throw new \Exception(
                sprintf('The given content object %s does not have a valid template path', $contentObjectPath)
            );
        }

        $finder = new Finder();

        $finder->files()
            ->in($templatePath)
            ->name($templateNameOrPattern . '.xml');

        $templates = [];
        
        /** @var \SplFileInfo $file */
        foreach($finder as $file)
        {
            $templateName = $file->getBasename('.xml');
            $templates[$templateName] = $this->parseTemplate($contentObjectNamespace, $file->getPathname());
        }

        return $templates;
    }

    /**
     * Parses a template
     *
     * @param $contentObjectNamespace
     * @param $templatePath
     *
     * @return Template
     *
     * @throws \Exception
     */
    protected function parseTemplate($contentObjectNamespace, $templatePath)
    {
        if (!file_exists($templatePath))
        {
            throw new \Exception(sprintf('The given template path %s does not exist', $templatePath));
        }

        $template_class_name = $contentObjectNamespace . '\Template\Template';

        $dom_document = new \DOMDocument('1.0', 'UTF-8');
        $dom_document->load($templatePath);
        $dom_xpath = new \DOMXPath($dom_document);

        if (! is_subclass_of($template_class_name, 'Chamilo\Core\Repository\Common\Template\TemplateParser'))
        {
            throw new \Exception(
                $template_class_name .
                ' doesn\'t seem to support parsing, please implement the' .
                ' Chamilo\Core\Repository\Common\Template\TemplateParser interface');
        }

        return $template_class_name::parse($dom_xpath);
    }
}