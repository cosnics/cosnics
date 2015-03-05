<?php
namespace Chamilo\Core\Repository\Common\Includes\Type;

use Chamilo\Core\Repository\Common\Includes\ContentObjectIncludeParser;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\String\Text;

/**
 * $Id: include_wiki_parser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * 
 * @package repository.lib.includes
 */
class IncludeWikiParser extends ContentObjectIncludeParser
{

    public function parse_editor()
    {
        $form = $this->get_form();
        $form_type = $form->get_form_type();
        $values = $form->exportValues();
        $content_object = $form->get_content_object();
        
        $base_path = Path :: getInstance()->getRepositoryPath(true);
        $html_editors = $form->get_html_editors();
        
        if (! $html_editors)
        {
            return;
        }
        
        /*
         * need to be configured to work with wikitags
         */
        foreach ($html_editors as $html_editor)
        {
            if (isset($values[$html_editor]))
            {
                $tags = Text :: fetch_tag_into_array($values[$html_editor], '[wikilink=]'); // bvb wikilink
                
                if (! $tags)
                {
                    return;
                }
                
                foreach ($tags as $tag)
                {
                    $search_path = str_replace($base_path, '', $tag['src']);
                    
                    $condition = new EqualityCondition(
                        new PropertyConditionVariable(File :: class_name(), File :: PROPERTY_PATH), 
                        new StaticConditionVariable($search_path));
                    
                    $search_objects = DataManager :: retrieve_active_content_objects(File :: class_name(), $condition);
                    
                    while ($search_object = $search_objects->next_result())
                    {
                        $content_object->include_content_object($search_object->get_id());
                    }
                }
            }
        }
    }
}
