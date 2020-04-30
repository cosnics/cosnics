<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Ajax\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseModule;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Application\Weblcms\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BlockSortComponent extends Manager
{

    function unserialize_jquery($jquery)
    {
        $block_data = explode('&', $jquery);
        $blocks = array();
        
        foreach ($block_data as $block)
        {
            $block_split = explode('=', $block);
            $blocks[] = $block_split[1];
        }
        
        return $blocks;
    }

    public function run()
    {
        $section_id = explode($_POST['id']);
        $blocks = $this->unserialize_jquery($_POST['order']);
        
        $i = 1;
        foreach ($blocks as $block_id)
        {
            $block = DataManager::retrieve_by_id(CourseModule::class, $block_id);
            $block->set_sort($i);
            $block->update();
            $i ++;
        }
        
        $json_result['success'] = '1';
        $json_result['message'] = Translation::get(
            'ObjectAdded', 
            array('OBJECT' => Translation::get('Block')), 
            Utilities::COMMON_LIBRARIES);
        
        // Return a JSON object
        echo json_encode($json_result);
    }
}