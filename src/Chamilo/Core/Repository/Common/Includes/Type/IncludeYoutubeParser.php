<?php
namespace Chamilo\Core\Repository\Common\Includes\Type;

use Chamilo\Core\Repository\Common\Includes\ContentObjectIncludeParser;
use Chamilo\Core\Repository\ContentObject\Youtube\Storage\DataClass\Youtube;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Utilities\String\Text;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * $Id: include_image_parser.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.includes
 */
class IncludeYoutubeParser extends ContentObjectIncludeParser
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

                    if (stripos($source, 'http://www.youtube.com/v/') !== false)
                    {
                        $source_components = parse_url($source);
                        $source_query_components = Text :: parse_query_string($source_components['query']);
                        $content_object_id = $source_query_components[Manager :: PARAM_CONTENT_OBJECT_ID];

                        if ($content_object_id)
                        {
                            $included_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                                ContentObject :: class_name(),
                                $content_object_id);

                            if ($included_object->get_type() == Youtube :: get_type_name())
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
