<?php
namespace Chamilo\Core\Repository\Common;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use DOMDocument;
use DOMXPath;

class ContentObjectResourceRenderer
{

    private $context;

    private $description;

    /**
     * Handle description as full html or not
     *
     * @var bool
     */
    private $full_html;

    public function __construct($context, $description, $full_html = false)
    {
        $this->context = $context;
        $this->description = $description;
        $this->full_html = $full_html;
    }

    public function run()
    {
        $description = $this->description;

        $this->dom_document = new DOMDocument();
        $this->dom_document->loadHTML('<?xml encoding="UTF-8">' . $description);
        $this->dom_document->removeChild($this->dom_document->firstChild);
        $this->dom_xpath = new DOMXPath($this->dom_document);

        if (! $this->full_html)
        {
            $body_nodes = $this->dom_xpath->query('body/*');
            $fragment = $this->dom_document->createDocumentFragment();
            foreach ($body_nodes as $child)
            {
                $fragment->appendChild($child);
            }
            $this->dom_document->replaceChild($fragment, $this->dom_document->firstChild);
        }
        else
        {
            $this->dom_document->removeChild($this->dom_document->firstChild);
        }

        $resources = $this->dom_xpath->query('//resource');
        foreach ($resources as $resource)
        {
            $source = $resource->getAttribute('source');
            // $type = $resource->getAttribute('type');
            // if (! $type)
            // {
            $type = ContentObjectRendition :: VIEW_INLINE;
            // }

            $parameters_list = $this->dom_xpath->query('@*', $resource);
            $parameters = array();
            foreach ($parameters_list as $parameter)
            {
                $parameters[$parameter->name] = $parameter->value;
            }

            try
            {
                $object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                    ContentObject :: class_name(),
                    $source);

                if (! $object instanceof ContentObject)
                {
                    continue;
                }
            }
            catch (\Exception $exception)
            {
                continue;
            }

            $rendition = new DOMDocument();
            $rendition->loadHTML(
                ContentObjectRenditionImplementation :: factory(
                    $object,
                    ContentObjectRendition :: FORMAT_HTML,
                    $type,
                    $this)->render($parameters));

            $rendition_xpath = new DOMXPath($rendition);

            $body_nodes = $rendition_xpath->query('body/*');
            $fragment = $rendition->createDocumentFragment();

            foreach ($body_nodes as $child)
            {
                $fragment->appendChild($child);
            }

            $fragment = $this->dom_document->importNode($fragment, true);

            $resource->parentNode->insertBefore($fragment, $resource);
            $resource->parentNode->removeChild($resource);
        }

        return $this->dom_document->saveHTML();
    }
}
