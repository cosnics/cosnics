<?php
namespace Chamilo\Core\Repository\Common;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use DOMDocument;
use DOMDocumentFragment;
use DOMNodeList;
use DOMXPath;
use Exception;

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

    /**
     *
     * @var DOMXPath $dom_xpath
     */
    private $dom_xpath;

    /**
     *
     * @var DOMDocument $dom_document
     */
    private $dom_document;

    /**
     * @var ContentObjectResourceParser
     */
    protected $contentObjectResourceParser;

    public function __construct($context, $description, $full_html = false)
    {
        $this->context = $context;
        $this->description = $description;
        $this->full_html = $full_html;

        $this->contentObjectResourceParser = new ContentObjectResourceParser(); //todo: injection
    }

    public function run()
    {
        $description = $this->description;

        $this->dom_document = $this->contentObjectResourceParser->getDomDocument($description, $this->full_html);
        $this->dom_xpath = $this->contentObjectResourceParser->getDomXPath($this->dom_document);

        $this->processResources();

        $this->processContentObjectPlaceholders();

        return $this->dom_document->saveHTML();
    }

    /**
     * Add given nodes to the given document fragment
     *
     * @param \DOMDocumentFragment $documentFragment
     * @param \DOMNodeList $nodes
     */
    protected function addNodesToDocumentFragment(DOMDocumentFragment $documentFragment, DOMNodeList $nodes)
    {
        foreach ($nodes as $node)
        {
            $documentFragment->appendChild($node);
        }
    }

    protected function processContentObjectPlaceholders()
    {
        $placeholders = $this->contentObjectResourceParser->getContentObjectDomElements($this->dom_xpath);
        foreach ($placeholders as $placeholder)
        {
            /**
             * @var \DOMElement $placeholder
             */
            $object = $this->contentObjectResourceParser->getContentObjectFromDomElement($placeholder);

            if(empty($object)) {
                continue;
            }

            $parameters = $this->contentObjectResourceParser->getContentObjectParametersFromDomElement($placeholder, $this->dom_xpath);

            $fragment = $this->getFragment($object, $parameters);

            $fragment = $this->dom_document->importNode($fragment, true);

            if ($placeholder->tagName == 'div')
            {
                $placeholder->appendChild($fragment);
            }
            else
            {
                $placeholder->parentNode->insertBefore($fragment, $placeholder);
                $placeholder->parentNode->removeChild($placeholder);
            }
        }
    }

    /**
     *
     * @deprecated
     *
     */
    protected function processResources()
    {
        $resources = $this->dom_xpath->query('//resource');
        foreach ($resources as $resource)
        {
            $source = $resource->getAttribute('source');

            $parameters_list = $this->dom_xpath->query('@*', $resource);
            $parameters = array();
            foreach ($parameters_list as $parameter)
            {
                $parameters[$parameter->name] = $parameter->value;
            }

            try
            {
                $object = DataManager::retrieve_by_id(
                    ContentObject::class_name(),
                    $source);

                if (! $object instanceof ContentObject)
                {
                    continue;
                }
            }
            catch (Exception $exception)
            {
                continue;
            }

            $fragment = $this->getFragment($object, $parameters);

            $fragment = $this->dom_document->importNode($fragment, true);

            $resource->parentNode->insertBefore($fragment, $resource);
            $resource->parentNode->removeChild($resource);
        }
    }

    /**
     * @param $object
     * @param $parameters
     * @return \DOMDocumentFragment
     */
    protected function getFragment($object, $parameters)
    {
        $renderResourceInline = $parameters['data-render-inline'];
        if(isset($renderResourceInline) && $renderResourceInline != '') {
            $renderResourceInline = filter_var(    $renderResourceInline, FILTER_VALIDATE_BOOLEAN);
        } else {
            $renderResourceInline = true;
        }

        if(!$renderResourceInline) {
            $type = ContentObjectRendition::VIEW_FULL_THUMBNAIL;
        } else {
            $type = ContentObjectRendition::VIEW_INLINE;
        }

        $descriptionRendition = ContentObjectRenditionImplementation::factory(
            $object,
            ContentObjectRendition::FORMAT_HTML,
            $type,
            $this->context)->render($parameters);

        $rendition = new DOMDocument();
        $rendition->loadHTML($descriptionRendition);

        $rendition_xpath = new DOMXPath($rendition);

        $fragment = $rendition->createDocumentFragment();

        $this->addNodesToDocumentFragment($fragment, $rendition_xpath->query('//script'));
        $this->addNodesToDocumentFragment($fragment, $rendition_xpath->query('//link'));
        $this->addNodesToDocumentFragment($fragment, $rendition_xpath->query('body/*'));

        return $fragment;
    }
}
