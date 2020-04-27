<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Form\FormValidator;

/**
 * @package Chamilo\Core\Repository\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class ContentObjectIncludeParser
{

    /**
     * @param string[] $values
     * @param string $htmlEditorName
     *
     * @return string
     */
    protected function getHtmlEditorValue(array $values, string $htmlEditorName)
    {
        $htmlEditorParts = explode('[', $htmlEditorName);

        $value = $values;
        foreach ($htmlEditorParts as $htmlEditorPart)
        {
            $part = str_replace(']', '', $htmlEditorPart);
            $value = $value[$part];
        }

        return $value;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string[][] $values
     */
    public function parse(ContentObject $contentObject, array $values)
    {
        $htmlEditors = $values[FormValidator::PROPERTY_HTML_EDITORS];

        foreach ($htmlEditors as $htmlEditor)
        {
            $htmlEditorValue = $this->getHtmlEditorValue($values, $htmlEditor);

            if (!empty($htmlEditorValue))
            {
                $this->parseHtmlEditorValue($contentObject, $htmlEditorValue);
            }
        }
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string[][] $values
     */
    public function parseAll(ContentObject $contentObject, array $values)
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

        $htmlEditors = $values[FormValidator::PROPERTY_HTML_EDITORS];

        foreach ($htmlEditors as $htmlEditor)
        {
            $htmlEditorValue = $this->getHtmlEditorValue($values, $htmlEditor);

            if (!empty($htmlEditorValue))
            {
                //                $this->parseImageReferences($contentObject, $htmlEditorValue);
                //                $this->parseEmbedReferences($contentObject, $htmlEditorValue);
                //                $this->parseYoutubeReferences($contentObject, $htmlEditorValue);
                //                $this->parseChamiloReferences($contentObject, $htmlEditorValue);
            }
        }
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string $htmlEditorValue
     */
    abstract protected function parseHtmlEditorValue(ContentObject $contentObject, string $htmlEditorValue);
}
