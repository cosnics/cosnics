<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\FrequentlyAskedQuestions\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\FrequentlyAskedQuestions\Manager;
use Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\FrequentlyAskedQuestionsDisplaySupport;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: wiki_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.wiki.component
 */
class ComplexDisplayComponent extends Manager implements FrequentlyAskedQuestionsDisplaySupport
{

    private $publication;

    public function run()
    {
        $publication_id = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, $publication_id);
        
        $this->publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $publication_id);
        if (! $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication))
        {
            $this->redirect(
                Translation::get("NotAllowed", null, Utilities::COMMON_LIBRARIES), 
                true, 
                array(), 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID));
        }
        
        $context = $this->publication->get_content_object()->package() . '\Display';
        
        $factory = new ApplicationFactory(
            $context, 
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }

    public function get_root_content_object()
    {
        return $this->publication->get_content_object();
    }

    public function get_publication()
    {
        return $this->publication;
    }
    
    // METHODS FOR COMPLEX DISPLAY RIGHTS
    public function is_allowed_to_edit_content_object()
    {
        return $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication);
    }

    public function is_allowed_to_view_content_object()
    {
        return $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication);
    }

    public function is_allowed_to_add_child()
    {
        return $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication);
    }

    public function is_allowed_to_delete_child()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_delete_feedback()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_edit_feedback()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\FrequentlyAskedQuestionsDisplaySupport::get_frequently_asked_questions_tree_menu_url()
     */
    public function get_frequently_asked_questions_tree_menu_url()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\FrequentlyAskedQuestionsDisplaySupport::get_frequently_asked_questions_additional_tabs()
     */
    public function get_frequently_asked_questions_additional_tabs()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\FrequentlyAskedQuestionsDisplaySupport::is_own_frequently_asked_questions()
     */
    public function is_own_frequently_asked_questions()
    {
        // TODO Auto-generated method stub
    }
}
