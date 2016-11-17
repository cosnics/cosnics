<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath\LearningPathAttemptProgressBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath\LearningPathAttemptProgressInformationBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * $Id: learning_path_attempt_progress_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.reporting.templates
 */

/**
 * This template shows an overview of the progress in a learning path of a user.
 * If this template is viewed from the
 * reporting tool, it adds an extra block with some information about the learning path.
 * 
 * @author Michael Kyndt
 * @author Bert De Clercq (Hogeschool Gent)
 */
class LearningPathAttemptProgressTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent::__construct($parent);
        $this->initialize_parameters();
        
        if ($this->tool == 'Reporting')
        {
            $lp = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                ContentObjectPublication::class_name(), 
                $this->publication_id)->get_content_object();
            
            $params = array();
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TEMPLATE_ID] = LearningPathAttemptsTemplate :: class_name();
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_USERS] = null;
            $params[\Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager :: PARAM_ATTEMPT_ID] = null;
            $params[\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID] = null;
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $this->publication_id;

            $custom_breadcrumbs = array();
            $custom_breadcrumbs[] = new Breadcrumb($this->get_url($params), $lp->get_title());
            
            if ($this->user_id)
            {
                $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
                    (int) $this->user_id);
                
                $custom_breadcrumbs[] = new Breadcrumb($this->get_url(), $user->get_fullname());
            }
            
            $this->set_custom_breadcrumb_trail($custom_breadcrumbs);
            
            $this->add_reporting_block($this->get_learning_path_progress_information());
        }
        
        $this->add_reporting_block($this->get_learning_path_progress());
    }

    private function initialize_parameters()
    {
        $this->publication_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);
        $this->user_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);
        $this->tool = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_TOOL);
        $course_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
        if ($course_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE, $course_id);
        }
        
        $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_TOOL, $this->tool);
        $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_USERS, $this->user_id);
        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, $this->publication_id);
        
        $attempt_id = Request::get(
            \Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::PARAM_ATTEMPT_ID);
        if ($attempt_id)
        {
            $this->set_parameter(
                \Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::PARAM_ATTEMPT_ID, 
                $attempt_id);
        }
        else
        {
            $this->set_parameter(
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_SHOW_PROGRESS, 
                'true');
        }
    }

    public function get_learning_path_progress()
    {
        $course_weblcms_block = new LearningPathAttemptProgressBlock($this);
        return $course_weblcms_block;
    }

    public function get_learning_path_progress_information()
    {
        $course_weblcms_block = new LearningPathAttemptProgressInformationBlock($this);
        return $course_weblcms_block;
    }
}
