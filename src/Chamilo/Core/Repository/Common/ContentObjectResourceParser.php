<?php
namespace Chamilo\Core\Repository\Common;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Exception;

/**
 * Class ContentObjectResourceParser
 */
class ContentObjectResourceParser
{
    /**
     * @param string|null $html
     * @param bool $parseFullHtml
     * @return \DOMDocument
     */
    public function getDomDocument(string $html = null, bool $parseFullHtml = false)
    {
        $domDocument = new DOMDocument();
        $domDocument->loadHTML('<?xml encoding="UTF-8">' . $html);
        $domDocument->removeChild($domDocument->firstChild);

        $domXPath = new DOMXPath($domDocument);

        if (!$parseFullHtml)
        {
            $this->replaceBodyWithChildren($domDocument, $domXPath);
        }
        else
        {
            $domDocument->removeChild($domDocument->firstChild);
        }

        return $domDocument;
    }

    /**
     * @param \DOMDocument $DOMDocument
     * @return \DOMXPath
     */
    public function getDomXPath(DOMDocument $DOMDocument)
    {
        return new DOMXPath($DOMDocument);
    }

    /**
     * @param \DOMElement $DOMElement
     * @return array
     */
    public function getContentObjectParametersFromDomElement(DOMElement $DOMElement, DOMXPath $domXPath)
    {
        $parameters_list = $domXPath->query('@*', $DOMElement);
        $parameters = array();

        foreach ($parameters_list as $parameter)
        {
            $parameters[$parameter->name] = $parameter->value;
        }

        return $parameters;
    }

    /**
     * @param \DOMElement $contentObjectDomElement
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass|null
     */
    public function getContentObjectFromDomElement(DOMElement $contentObjectDomElement)
    {
        /**
         * @var \DOMElement $contentObjectDomElement
         */
        $contentObjectId = $contentObjectDomElement->getAttribute('data-co-id');

        try
        {
            $contentObject = DataManager::retrieve_by_id(
                ContentObject::class,
                $contentObjectId);

            if (! $contentObject instanceof ContentObject)
            {
                return null;
            }

            $securityCode = $contentObjectDomElement->getAttribute('data-security-code');
            if (empty($securityCode) || $securityCode != $contentObject->calculate_security_code())
            {
                return null;
            }

            return $contentObject;
        }
        catch (Exception $exception)
        {
            return null;
        }
    }

    /**
     * @param string $html
     * @param bool $parseFullHtml
     * @return array
     */
    public function getContentObjects(string $html, bool $parseFullHtml = false)
    {
        $domDocument = $this->getDomDocument($html, $parseFullHtml);

        $contentObjectDomElements = $this->getContentObjectDomElements(new DOMXPath($domDocument));

        $contentObjects = [];

        foreach ($contentObjectDomElements as $contentObjectDomElement)
        {
            $contentObject = $this->getContentObjectFromDomElement($contentObjectDomElement);

            if(!empty($contentObject)){
                $contentObjects[] = $contentObject;
            }
        }

        return $contentObjects;
    }

    /**
     * @param \DOMDocument $domDocument
     * @param \DOMXPath $domXPath
     */
    protected function replaceBodyWithChildren(DOMDocument $domDocument, DOMXPath $domXPath)
    {
        $body_nodes = $domXPath->query('body/*');
        $fragment = $domDocument->createDocumentFragment();
        foreach ($body_nodes as $child)
        {
            $fragment->appendChild($child);
        }
        $domDocument->replaceChild($fragment, $domDocument->firstChild);
    }

    /**
     * @param \DOMXPath $domXPath
     * @return \DOMNodeList
     */
    public function getContentObjectDomElements(DOMXPath $domXPath)
    {
        return $domXPath->query('//*[@data-co-id]'); // select all elements with the data-co-id
    }
}