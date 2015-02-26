<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Php\Lib\Manager\BuilderWizard;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Php\Lib\Manager\BuilderWizardPage;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass\PeerAssessment;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

abstract class Manager extends \Chamilo\Core\Repository\Builder\Manager
{
    const ACTION_BROWSE_ATTEMPTS = 'attempt_browser';
    const PARAM_ATTEMPT = 'attempt';
    const PARAM_GROUP = 'group';
    const PARAM_WIZARD_PAGE = 'wizard_page';
    const ACTION_CREATE_ATTEMPT = 'attempt_creator';
    const ACTION_EDIT_ATTEMPT = 'attempt_updater';
    const ACTION_DELETE_ATTEMPT = 'attempt_deleter';
    const ACTION_BROWSE_GROUPS = 'group_browser';
    const ACTION_VIEW_GROUPS = 'group_viewer';
    const ACTION_CREATE_GROUP = 'group_creator';
    const ACTION_EDIT_GROUP = 'group_updater';
    const ACTION_DELETE_GROUP = 'group_deleter';
    const ACTION_BROWSE = 'browser';
    const ACTION_EDIT_SETTINGS = 'settings_editor';

    /**
     *
     * @todo move to proper place
     */
    const VIEW_RIGHT = 1;
    const EDIT_RIGHT = 2;

    private $wizard;

    function __construct($user, $parent)
    {
        parent :: __construct($user, $parent);

        $this->wizard = new BuilderWizard($this);
        // if (method_exists($this->get_parent(), 'get_parent'))
        // $this->init_wizard();
    }

    function init_wizard()
    {
        $root_content_object = $this->get_root_content_object();
        $assessment_type = $root_content_object->get_assessment_type();

        if (($assessment_type == PeerAssessment :: TYPE_BOTH || $assessment_type == PeerAssessment :: TYPE_SCORES) &&
             ! $this->publication_has_scores())
        {
            $wizard_page = new BuilderWizardPage();
            $wizard_page->set_id(self :: DEFAULT_ACTION);
            $wizard_page->set_title('BuildIndicators');
            $wizard_page->set_params(array(self :: PARAM_ACTION => self :: DEFAULT_ACTION));
            $wizard_page->set_repeats(true);
            $wizard_page->set_show_menu(count($this->get_indicators()));
            $wizard_page->set_completed(count($this->get_indicators()));
            $this->wizard->add_page($wizard_page);
        }

        $wizard_page = new BuilderWizardPage();
        $wizard_page->set_id(self :: ACTION_EDIT_ATTEMPT);
        $wizard_page->set_title('PeerAssessmentBuilderAttemptCreatorComponent');
        $wizard_page->set_params(array(self :: PARAM_ACTION => self :: ACTION_CREATE_ATTEMPT));
        $wizard_page->set_show_menu(false);
        $wizard_page->set_completed(count($this->get_attempts($this->get_publication_id())));
        $this->wizard->add_page($wizard_page);

        $wizard_page = new BuilderWizardPage();
        $wizard_page->set_id(self :: ACTION_BROWSE_ATTEMPTS);
        $wizard_page->set_title('PeerAssessmentBuilderAttemptBrowserComponent');
        $wizard_page->set_params(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ATTEMPTS));
        $wizard_page->set_repeats(true);
        $wizard_page->set_completed(count($this->get_attempts($this->get_publication_id())));
        $this->wizard->add_page($wizard_page);

        $wizard_page = new BuilderWizardPage();
        $wizard_page->set_id(self :: ACTION_CREATE_GROUP);
        $wizard_page->set_title('PeerAssessmentBuilderGroupCreatorComponent');
        $wizard_page->set_params(array(self :: PARAM_ACTION => self :: ACTION_CREATE_GROUP));
        $wizard_page->set_show_menu(false);
        $wizard_page->set_completed(count($this->get_groups($this->get_publication_id())));
        $this->wizard->add_page($wizard_page);

        $wizard_page = new BuilderWizardPage();
        $wizard_page->set_id(self :: ACTION_BROWSE_GROUPS);
        $wizard_page->set_title('PeerAssessmentBuilderGroupBrowserComponent');
        $wizard_page->set_params(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_GROUPS));
        $wizard_page->set_repeats(true);
        $wizard_page->set_completed(count($this->get_groups($this->get_publication_id())));
        $this->wizard->add_page($wizard_page);

        if (! $this->publication_has_scores())
        {
            $wizard_page = new BuilderWizardPage();
            $wizard_page->set_id(self :: ACTION_EDIT_SETTINGS);
            $wizard_page->set_title('PeerAssessmentBuilderSettingsEditorComponent');
            $wizard_page->set_params(array(self :: PARAM_ACTION => self :: ACTION_EDIT_SETTINGS));
            $wizard_page->set_show_menu(false);
            $settings = $this->get_settings($this->get_publication_id());
            $wizard_page->set_completed(
                $this->get_settings($this->get_publication_id())->get_min_group_members() === null ? false : true);
            $this->wizard->add_page($wizard_page);
        }

        $complete = count($this->get_indicators());
        $complete &= count($this->get_groups($this->get_publication_id()));
        $complete &= count($this->get_attempts($this->get_publication_id()));
        $complete &= $this->get_settings($this->get_publication_id())->get_min_group_members() === null ? false : true;
        $this->wizard->set_complete($complete);
    }

    /**
     * adds wizard menu to footer
     */
    function render_footer()
    {
        $html = array();

        $html[] = $this->wizard->display(Request :: get(self :: PARAM_ACTION), self :: DEFAULT_ACTION);
        $html[] = parent :: render_footer();

        return implode("\n", $html);
    }

    /**
     * adds wizard menu to header
     */
    function render_header()
    {
        $html = array();

        $html[] = parent :: render_header();
        $html[] = $this->wizard->display(Request :: get(self :: PARAM_ACTION), self :: DEFAULT_ACTION);

        return implode("\n", $html);
    }

    function get_action_bar(\Chamilo\Core\Repository\Storage\DataClass\ContentObject $content_object)
    {
        $repository_context = $this->get_parent() instanceof \Chamilo\Core\Repository\Component\BuilderComponent;

        $this->action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $display_action = Request :: get(self :: PARAM_ACTION);

        if (! $repository_context)
        {
            if ($display_action != self :: ACTION_EDIT_SETTINGS && ! $this->publication_has_scores())
                $this->action_bar->add_tool_action(
                    new ToolbarItem(
                        Translation :: get('PeerAssessmentBuilderSettingsEditorComponent'),
                        Theme :: getInstance()->getCommonImagePath() . 'action_config.png',
                        $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_SETTINGS))));
            if ($display_action != self :: ACTION_BROWSE_ATTEMPTS)
                $this->action_bar->add_tool_action(
                    new ToolbarItem(
                        Translation :: get('PeerAssessmentBuilderAttemptBrowserComponent'),
                        Theme :: getInstance()->getCommonImagePath() . 'action_period.png',
                        $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ATTEMPTS))));
            if ($display_action != self :: ACTION_BROWSE_GROUPS)
                $this->action_bar->add_tool_action(
                    new ToolbarItem(
                        Translation :: get('PeerAssessmentBuilderGroupCreatorComponent'),
                        Theme :: getInstance()->getCommonImagePath() . 'treemenu/group.png',
                        $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_GROUPS))));

            if ($display_action != self :: ACTION_BROWSE && ! $this->publication_has_scores())
            {
                $root_content_object = $this->get_root_content_object();
                $assessment_type = $root_content_object->get_assessment_type();
                if ($assessment_type == PeerAssessment :: TYPE_BOTH || $assessment_type == PeerAssessment :: TYPE_SCORES)
                {
                    $this->action_bar->add_tool_action(
                        new ToolbarItem(
                            Translation :: get('PeerAssessmentBuilderBrowserComponent'),
                            Theme :: getInstance()->getCommonImagePath() . 'action_build.png',
                            $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE))));
                }
            }

            $this->action_bar->add_tool_action(
                new ToolbarItem(
                    Translation :: get('ToolComplexDisplay'),
                    Theme :: getInstance()->getCommonImagePath() . 'action_browser.png',
                    $this->get_url($this->get_complex_display_params())));
        }
        return $this->action_bar;
    }

    function get_complex_display_params()
    {
        return $this->get_parent()->get_parent()->get_complex_display_params();
    }

    public function get_settings($publication_id)
    {
        return $this->get_parent()->get_parent()->get_settings($publication_id);
    }

    public function get_attempts($publication_id)
    {
        return $this->get_parent()->get_parent()->get_attempts($publication_id);
    }

    public function get_attempt($id = null)
    {
        return $this->get_parent()->get_parent()->get_attempt($id);
    }

    /**
     * asks publication id to parent
     */
    public function get_publication_id()
    {
        return $this->get_parent()->get_parent()->get_publication_id();
    }

    public function has_scores($attempt_id = null)
    {
        return $this->get_parent()->get_parent()->has_scores($attempt_id);
    }

    /**
     * checks if current user has correct rights
     */
    public function is_allowed($right)
    {
        return $this->get_parent()->is_allowed($right);
    }

    public function is_allowed_to_view_content_object()
    {
        return $this->get_parent()->is_allowed_to_view_content_object();
    }

    public function is_allowed_to_edit_content_object()
    {
        return $this->get_parent()->is_allowed_to_edit_content_object();
    }

    public function is_allowed_to_add_child()
    {
        return $this->get_parent()->is_allowed_to_add_child();
    }

    public function is_allowed_to_delete_child()
    {
        return $this->get_parent()->is_allowed_to_delete_child();
    }

    public function is_allowed_to_delete_feedback()
    {
        return $this->get_parent()->is_allowed_to_delete_feedback();
    }

    public function is_allowed_to_edit_feedback()
    {
        return $this->get_parent()->is_allowed_to_edit_feedback();
    }

    public function delete_attempt($id)
    {
        return $this->get_parent()->get_parent()->delete_attempt($id);
    }

    public function get_group($id)
    {
        return $this->get_parent()->get_parent()->get_group($id);
    }

    public function get_groups($publication_id)
    {
        return $this->get_parent()->get_parent()->get_groups($publication_id);
    }

    public function delete_group($id)
    {
        return $this->get_parent()->get_parent()->delete_group($id);
    }

    public function add_user_to_group($user_id, $group_id)
    {
        return $this->get_parent()->get_parent()->add_user_to_group($user_id, $group_id);
    }

    public function remove_user_from_group($user_id, $group_id)
    {
        return $this->get_parent()->get_parent()->remove_user_from_group($user_id, $group_id);
    }

    public function user_is_enrolled_in_group($user_id)
    {
        return $this->get_parent()->get_parent()->user_is_enrolled_in_group($user_id);
    }

    /**
     * Get the groups in which the current user is subscribed
     *
     * @param integer $user_id
     * @return array The groups
     * @deprecated use get_user_group() instead
     */
    public function get_user_groups($user_id)
    {
        $this->display_warning_message('Deprecated, use get_user_group() instead');

        return array($this->get_parent()->get_parent()->get_user_group($user_id));
    }

    public function get_user_group($user_id = null)
    {
        return $this->get_parent()->get_parent()->get_user_group($user_id);
    }

    public function get_group_users($group_id)
    {
        return $this->get_parent()->get_parent()->get_group_users($group_id);
    }

    public function count_group_users($group_id)
    {
        return $this->get_parent()->get_parent()->count_group_users($group_id);
    }

    /**
     * checks if a pa group has scores
     *
     * @param int $group_id
     * @return boolean
     */
    function group_has_scores($group_id)
    {
        return $this->get_parent()->get_parent()->group_has_scores($group_id);
    }

    public function publication_has_scores()
    {
        $groups = $this->get_groups($this->get_publication_id());
        foreach ($groups as $group)
        {
            if ($this->group_has_scores($group->get_id()))
            {
                return true;
            }
        }
        return false;
    }

    public function get_user_attempt_status($user_id, $attempt_id)
    {
        return $this->get_parent()->get_parent()->get_user_attempt_status($user_id, $attempt_id);
    }

    public function get_group_feed_path()
    {
        return $this->get_parent()->get_parent()->get_group_feed_path();
    }

    public function get_indicators($publication_id)
    {
        return $this->get_parent()->get_parent()->get_indicators($publication_id);
    }

    public function get_context_group_users($context_group_id)
    {
        return $this->get_parent()->get_parent()->get_context_group_users($context_group_id);
    }

    public function get_context_group($context_group_id)
    {
        return $this->get_parent()->get_parent()->get_context_group($context_group_id);
    }

    public function get_course_setting($setting)
    {
        return $this->get_parent()->get_parent()->get_course_setting($setting);
    }
}
