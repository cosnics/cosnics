<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\ComplexLearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\ComplexLearningPathItem;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PrerequisitesTranslator
{

    /**
     *
     * @var \core\repository\common\path\ComplexContentObjectPathNode
     */
    private $node;

    /**
     *
     * @param \core\repository\common\path\ComplexContentObjectPathNode $node
     */
    public function __construct(ComplexContentObjectPathNode $node)
    {
        $this->node = $node;
    }

    /**
     *
     * @return boolean
     */
    public function can_execute()
    {
        if ($this->node->get_complex_content_object_item() instanceof ComplexLearningPath)
        {
            return true;
        }
        else
        {
            if ($this->node->get_complex_content_object_item() instanceof ComplexLearningPathItem &&
                 $this->node->get_complex_content_object_item()->has_prerequisites())
            {
                return $this->verify();
            }
            else
            {
                return true;
            }
        }
    }

    /**
     *
     * @return boolean
     */
    public function verify()
    {
        $prerequisites = $this->node->get_complex_content_object_item()->get_prerequisites();
        
        $matches = $item_ids = array();
        $search_pattern = '/[^\(\)\&\|~]*/';
        preg_match_all($search_pattern, $prerequisites, $matches);
        rsort($matches[0], SORT_NUMERIC);
        
        foreach ($matches[0] as $match)
        {
            if ($match)
            {
                if (! in_array($match, $item_ids))
                {
                    $item_ids[] = $match;
                }
            }
        }
        
        foreach ($item_ids as $item_id)
        {
            // if an empty box was selected, the prerequisite is automatically completed
            if ($item_id == - 1)
            {
                $value = 1;
            }
            else
            {
                $value = 0;
                
                foreach ($this->node->get_siblings() as $sibling)
                {
                    if ($sibling->get_complex_content_object_item()->get_id() == $item_id)
                    {
                        if ($sibling->is_completed())
                        {
                            $value = 1;
                        }
                        
                        break;
                    }
                }
            }
            
            $prerequisites = str_replace($item_id, $value, $prerequisites);
        }
        
        $replacement_pattern = "/(\(\&)|(\(\|)/";
        $replacement = '(';
        
        $prerequisites = preg_replace($replacement_pattern, $replacement, $prerequisites);
        $prerequisites = str_replace('&', '&&', $prerequisites);
        $prerequisites = str_replace('|', '||', $prerequisites);
        $prerequisites = str_replace('~', '!', $prerequisites);
        $prerequisites = '$value = ' . $prerequisites . ';';
        eval($prerequisites);
        
        return $value;
    }
}
