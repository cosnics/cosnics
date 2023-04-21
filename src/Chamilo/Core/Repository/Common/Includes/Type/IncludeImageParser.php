<?php
namespace Chamilo\Core\Repository\Common\Includes\Type;

use Chamilo\Core\Repository\Common\Includes\ContentObjectIncludeParser;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Processor\Ckeditor\Processor;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Utilities\String\Text;

/**
 *
 * @package repository.lib.includes
 */
class IncludeImageParser extends ContentObjectIncludeParser
{

    public function parseHtmlEditorField()
    {
        $values = $this->getValues();
        $contentObject = $this->getContentObject();

        $htmlEditors = $values[FormValidator::PROPERTY_HTML_EDITORS];

        foreach ($htmlEditors as $htmlEditor)
        {
            if (isset($values[$htmlEditor]))
            {
                $tags = Text::parse_html_file($values[$htmlEditor]);

                foreach ($tags as $tag)
                {
                    $source = $tag->getAttribute('src');
                    $matches = preg_match(Processor::get_repository_document_display_matching_url(), $source);

                    if ($matches === 1)
                    {
                        $source_components = parse_url($source);
                        $source_query_components = Text::parse_query_string($source_components['query']);
                        $contentObjectIdentifier = $source_query_components[Manager::PARAM_CONTENT_OBJECT_ID];

                        if ($contentObjectIdentifier)
                        {
                            /**
                             * @var \Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File $includedObject
                             */
                            $includedObject = DataManager::retrieve_by_id(
                                ContentObject::class, $contentObjectIdentifier
                            );

                            if ($includedObject->is_image())
                            {
                                $contentObject->include_content_object($includedObject->getId());
                            }
                        }
                    }
                }
            }
        }
    }
}
