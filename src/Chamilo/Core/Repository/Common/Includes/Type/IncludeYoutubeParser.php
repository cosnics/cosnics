<?php
namespace Chamilo\Core\Repository\Common\Includes\Type;

use Chamilo\Core\Repository\Common\Includes\ContentObjectIncludeParser;
use Chamilo\Core\Repository\ContentObject\Youtube\Storage\DataClass\Youtube;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Utilities\String\Text;

/**
 *
 * @package repository.lib.includes
 */
class IncludeYoutubeParser extends ContentObjectIncludeParser
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
                $tags = Text::parse_html_file($values[$htmlEditor], 'embed');

                foreach ($tags as $tag)
                {
                    $source = $tag->getAttribute('src');

                    if (stripos($source, 'http://www.youtube.com/v/') !== false)
                    {
                        $source_components = parse_url($source);
                        $source_query_components = Text::parse_query_string($source_components['query']);
                        $content_object_id = $source_query_components[Manager::PARAM_CONTENT_OBJECT_ID];

                        if ($content_object_id)
                        {
                            $includedObject = DataManager::retrieve_by_id(
                                ContentObject::class, $content_object_id
                            );

                            if ($includedObject instanceof Youtube)
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
