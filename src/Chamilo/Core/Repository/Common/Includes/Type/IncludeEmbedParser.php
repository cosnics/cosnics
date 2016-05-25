<?php
namespace Chamilo\Core\Repository\Common\Includes\Type;

use Chamilo\Core\Repository\Common\Includes\ContentObjectIncludeParser;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Processor\HtmlEditorProcessor;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Utilities\String\Text;

/**
 * $Id: include_flash_parser.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.includes
 */
class IncludeEmbedParser extends ContentObjectIncludeParser
{

    public function parse_editor()
    {
        $form = $this->get_form();
        $form_type = $form->get_form_type();
        $values = $form->exportValues();
        $content_object = $form->get_content_object();

        $html_editors = $form->get_html_editors();

        foreach ($html_editors as $html_editor)
        {
            if (isset($values[$html_editor]))
            {
                $tags = Text :: parse_html_file($values[$html_editor], 'embed');

                foreach ($tags as $tag)
                {
                    $source = $tag->getAttribute('src');
                    $matches = preg_match(
                        HtmlEditorProcessor :: get_repository_document_display_matching_url(),
                        $source);

                    if ($matches === 1)
                    {
                        $source_components = parse_url($source);
                        $source_query_components = Text :: parse_query_string($source_components['query']);
                        $content_object_id = $source_query_components[Manager :: PARAM_CONTENT_OBJECT_ID];

                        if ($content_object_id)
                        {
                            $included_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                                ContentObject :: class_name(),
                                $content_object_id);

                            if ($included_object->is_flash() || $included_object->is_video() ||
                                 $included_object->is_audio())
                            {
                                $content_object->include_content_object($included_object->get_id());
                            }
                        }
                    }
                }
            }
        }
    }
}
