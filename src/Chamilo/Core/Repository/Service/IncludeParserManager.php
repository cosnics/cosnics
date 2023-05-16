<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * @package Chamilo\Core\Repository\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class IncludeParserManager
{
    /**
     * @var \Chamilo\Core\Repository\Service\ContentObjectIncludeParser[]
     */
    protected $parsers;

    /**
     * @param \Chamilo\Core\Repository\Service\ContentObjectIncludeParser $parser
     */
    public function addParser(ContentObjectIncludeParser $parser)
    {
        $this->parsers[] = $parser;
    }

    /**
     * @return \Chamilo\Core\Repository\Service\ContentObjectIncludeParser[]
     */
    protected function getParsers()
    {
        return $this->parsers;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string[][] $values
     */
    public function parseContentObjectValues(ContentObject $contentObject, array $values)
    {
        if ($contentObject->isIdentified())
        {
            /*
             * TODO: Make this faster by providing a function that matches the existing IDs against the ones that need
             * to be added, and attaches and detaches accordingly.
             */
            foreach ($contentObject->get_includes() as $included_object)
            {
                $contentObject->exclude_content_object($included_object->getId());
            }
        }

        foreach ($this->getParsers() as $parser)
        {
            $parser->parse($contentObject, $values);
        }
    }
}