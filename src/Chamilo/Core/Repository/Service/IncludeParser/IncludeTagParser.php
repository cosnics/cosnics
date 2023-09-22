<?php
namespace Chamilo\Core\Repository\Service\IncludeParser;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Service\ContentObjectIncludeParser;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;

/**
 *
 * @package repository.lib.includes
 */
abstract class IncludeTagParser extends ContentObjectIncludeParser
{
    /**
     * @param $htmlEditorValue
     *
     * @return \DOMNodeList
     */
    abstract protected function findTags($htmlEditorValue);

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return boolean
     */
    abstract protected function isValidContentObject(ContentObject $contentObject);

    /**
     * @param string $source
     *
     * @return boolean
     */
    abstract protected function isValidSource($source);

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string $htmlEditorValue
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function parseHtmlEditorValue(ContentObject $contentObject, string $htmlEditorValue)
    {
        foreach ($this->findTags($htmlEditorValue) as $tag)
        {
            $source = $tag->getAttribute('src');

            if ($this->isValidSource($source))
            {
                $source_components = parse_url($source);
                parse_str($source_components['query'], $source_query_components);
                $content_object_id = $source_query_components[Manager::PARAM_CONTENT_OBJECT_ID];

                if ($content_object_id)
                {
                    $includedObject = DataManager::retrieve_by_id(
                        ContentObject::class, $content_object_id
                    );

                    if ($this->isValidContentObject($includedObject))
                    {
                        $contentObject->include_content_object($includedObject->getId());
                    }
                }
            }
        }
    }
}
