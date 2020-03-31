<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder;

use Chamilo\Core\Repository\Component\BuilderComponent;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Php\Lib\Manager\BuilderWizard;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Php\Lib\Manager\BuilderWizardPage;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass\PeerAssessment;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

abstract class Manager extends \Chamilo\Core\Repository\Builder\Manager
{
    const ACTION_BROWSE = 'Browser';

    const ACTION_BROWSE_ATTEMPTS = 'AttemptBrowser';

    const ACTION_BROWSE_GROUPS = 'GroupBrowser';

    const ACTION_CREATE_ATTEMPT = 'AttemptCreator';

    const ACTION_CREATE_GROUP = 'GroupCreator';

    const ACTION_DELETE_ATTEMPT = 'AttemptDeleter';

    const ACTION_DELETE_GROUP = 'GroupDeleter';

    const ACTION_EDIT_ATTEMPT = 'AttemptUpdater';

    const ACTION_EDIT_GROUP = 'GroupUpdater';

    const ACTION_EDIT_SETTINGS = 'SettingsEditor';

    const ACTION_VIEW_GROUPS = 'GroupViewer';

    const EDIT_RIGHT = 2;

    const PARAM_ATTEMPT = 'attempt';

    const PARAM_GROUP = 'group';

    const PARAM_WIZARD_PAGE = 'wizard_page';

    /**
     *
     * @todo move to proper place
     */
    const VIEW_RIGHT = 1;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    protected $buttonToolbarRenderer;

    private $wizard;

    function __construct($user, $parent)
    {
        parent::__construct($user, $parent);
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $this->wizard = new BuilderWizard($this);
        // if (method_exists($this->get_parent(), 'get_parent'))
        // $this->init_wizard();
    }

    public function add_user_to_group($user_id, $group_id)
    {
        return $this->get_parent()->get_parent()->add_user_to_group($user_id, $group_id);
    }

    public function count_group_users($group_id)
    {
        return $this->get_parent()->get_parent()->count_group_users($group_id);
    }

    public function delete_attempt($id)
    {
        return $this->get_parent()->get_parent()->delete_attempt($id);
    }

    public function delete_group($id)
    {
        return $this->get_parent()->get_parent()->delete_group($id);
    }

    function getButtonToolbarRenderer()
    {
        $repository_context = $this->get_parent() instanceof BuilderComponent;

        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $toolActions = new ButtonGroup();

            $display_action = Request::get(self::PARAM_ACTION);

            if (!$repository_context)
            {
                if ($display_action != self::ACTION_EDIT_SETTINGS && !$this->publication_has_scores())
                {
                    $toolActions->addButton(
                        new Button(
                            Translation::get('PeerAssessmentBuilderSettingsEditorComponent'),
                            new FontAwesomeGlyph('cog'),
                            $this->get_url(array(self::PARAM_ACTION => self::ACTION_EDIT_SETTINGS))
                        )
                    );
                }
                if ($display_action != self::ACTION_BROWSE_ATTEMPTS)
                {
                    $toolActions->addButton(
                        new Button(
                            Translation::get('PeerAssessmentBuilderAttemptBrowserComponent'),
                            new FontAwesomeGlyph('clock'),
                            $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_ATTEMPTS))
                        )
                    );
                }
                if ($display_action != self::ACTION_BROWSE_GROUPS)
                {
                    $toolActions->addButton(
                        new Button(
                            Translation::get('PeerAssessmentBuilderGroupCreatorComponent'),
                            new FontAwesomeGlyph('users'),
                            $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_GROUPS))
                        )
                    );
                }

                if ($display_action != self::ACTION_BROWSE && !$this->publication_has_scores())
                {
                    $root_content_object = $this->get_root_content_object();
                    $assessment_type = $root_content_object->get_assessment_type();
                    if ($assessment_type == PeerAssessment::TYPE_BOTH ||
                        $assessment_type == PeerAssessment::TYPE_SCORES)
                    {
                        $toolActions->addButton(
                            new Button(
                                Translation::get('PeerAssessmentBuilderBrowserComponent'),
                                new FontAwesomeGlyph('cubes'),
                                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE))
                            )
                        );
                    }
                }

                $toolActions->addButton(
                    new Button(
                        Translation::get('ToolComplexDisplay'), new FontAwesomeGlyph('desktop'),
                        $this->get_url($this->get_complex_display_params())
                    )
                );
            }
            $buttonToolbar->addButtonGroup($toolActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function get_attempt($id = null)
    {
        return $this->get_parent()->get_parent()->get_attempt($id);
    }

    public function get_attempts($publication_id)
    {
        return $this->get_parent()->get_parent()->get_attempts($publication_id);
    }

    function get_complex_display_params()
    {
        return $this->get_parent()->get_parent()->get_complex_display_params();
    }

    public function get_context_group($context_group_id)
    {
        return $this->get_parent()->get_parent()->get_context_group($context_group_id);
    }

    public function get_context_group_users($context_group_id)
    {
        return $this->get_parent()->get_parent()->get_context_group_users($context_group_id);
    }

    public function get_course_setting($setting)
    {
        return $this->get_parent()->get_parent()->get_course_setting($setting);
    }

    public function get_group($id)
    {
        return $this->get_parent()->get_parent()->get_group($id);
    }

    public function get_group_feed_path()
    {
        return $this->get_parent()->get_parent()->get_group_feed_path();
    }

    public function get_group_users($group_id)
    {
        return $this->get_parent()->get_parent()->get_group_users($group_id);
    }

    public function get_groups($publication_id)
    {
        return $this->get_parent()->get_parent()->get_groups($publication_id);
    }

    public function get_indicators($publication_id)
    {
        return $this->get_parent()->get_parent()->get_indicators($publication_id);
    }

    /**
     * asks publication id to parent
     */
    public function get_publication_id()
    {
        return $this->get_parent()->get_parent()->get_publication_id();
    }

    public function get_settings($publication_id)
    {
        return $this->get_parent()->get_parent()->get_settings($publication_id);
    }

    public function get_user_attempt_status($user_id, $attempt_id)
    {
        return $this->get_parent()->get_parent()->get_user_attempt_status($user_id, $attempt_id);
    }

    public function get_user_group($user_id = null)
    {
        return $this->get_parent()->get_parent()->get_user_group($user_id);
    }

    /**
     * Get the groups in which the current user is subscribed
     *
     * @param integer $user_id
     *
     * @return array The groups
     * @deprecated use get_user_group() instead
     */
    public function get_user_groups($user_id)
    {
        $this->display_warning_message('Deprecated, use get_user_group() instead');

        return array($this->get_parent()->get_parent()->get_user_group($user_id));
    }

    /**
     * checks if a pa group has scores
     *
     * @param int $group_id
     *
     * @return boolean
     */
    function group_has_scores($group_id)
    {
        return $this->get_parent()->get_parent()->group_has_scores($group_id);
    }

    public function has_scores($attempt_id = null)
    {
        return $this->get_parent()->get_parent()->has_scores($attempt_id);
    }

    function init_wizard()
    {
        $root_content_object = $this->get_root_content_object();
        $assessment_type = $root_content_object->get_assessment_type();

        if (($assessment_type == PeerAssessment::TYPE_BOTH || $assessment_type == PeerAssessment::TYPE_SCORES) &&
            !$this->publication_has_scores())
        {
            $wizard_page = new BuilderWizardPage();
            $wizard_page->set_id(self::DEFAULT_ACTION);
            $wizard_page->set_title('BuildIndicators');
            $wizard_page->set_params(array(self::PARAM_ACTION => self::DEFAULT_ACTION));
            $wizard_page->set_repeats(true);
            $wizard_page->set_show_menu(count($this->get_indicators()));
            $wizard_page->set_completed(count($this->get_indicators()));
            $this->wizard->add_page($wizard_page);
        }

        $wizard_page = new BuilderWizardPage();
        $wizard_page->set_id(self::ACTION_EDIT_ATTEMPT);
        $wizard_page->set_title('PeerAssessmentBuilderAttemptCreatorComponent');
        $wizard_page->set_params(array(self::PARAM_ACTION => self::ACTION_CREATE_ATTEMPT));
        $wizard_page->set_show_menu(false);
        $wizard_page->set_completed(count($this->get_attempts($this->get_publication_id())));
        $this->wizard->add_page($wizard_page);

        $wizard_page = new BuilderWizardPage();
        $wizard_page->set_id(self::ACTION_BROWSE_ATTEMPTS);
        $wizard_page->set_title('PeerAssessmentBuilderAttemptBrowserComponent');
        $wizard_page->set_params(array(self::PARAM_ACTION => self::ACTION_BROWSE_ATTEMPTS));
        $wizard_page->set_repeats(true);
        $wizard_page->set_completed(count($this->get_attempts($this->get_publication_id())));
        $this->wizard->add_page($wizard_page);

        $wizard_page = new BuilderWizardPage();
        $wizard_page->set_id(self::ACTION_CREATE_GROUP);
        $wizard_page->set_title('PeerAssessmentBuilderGroupCreatorComponent');
        $wizard_page->set_params(array(self::PARAM_ACTION => self::ACTION_CREATE_GROUP));
        $wizard_page->set_show_menu(false);
        $wizard_page->set_completed(count($this->get_groups($this->get_publication_id())));
        $this->wizard->add_page($wizard_page);

        $wizard_page = new BuilderWizardPage();
        $wizard_page->set_id(self::ACTION_BROWSE_GROUPS);
        $wizard_page->set_title('PeerAssessmentBuilderGroupBrowserComponent');
        $wizard_page->set_params(array(self::PARAM_ACTION => self::ACTION_BROWSE_GROUPS));
        $wizard_page->set_repeats(true);
        $wizard_page->set_completed(count($this->get_groups($this->get_publication_id())));
        $this->wizard->add_page($wizard_page);

        if (!$this->publication_has_scores())
        {
            $wizard_page = new BuilderWizardPage();
            $wizard_page->set_id(self::ACTION_EDIT_SETTINGS);
            $wizard_page->set_title('PeerAssessmentBuilderSettingsEditorComponent');
            $wizard_page->set_params(array(self::PARAM_ACTION => self::ACTION_EDIT_SETTINGS));
            $wizard_page->set_show_menu(false);
            $settings = $this->get_settings($this->get_publication_id());
            $wizard_page->set_completed(
                $this->get_settings($this->get_publication_id())->get_min_group_members() === null ? false : true
            );
            $this->wizard->add_page($wizard_page);
        }

        $complete = count($this->get_indicators());
        $complete &= count($this->get_groups($this->get_publication_id()));
        $complete &= count($this->get_attempts($this->get_publication_id()));
        $complete &= $this->get_settings($this->get_publication_id())->get_min_group_members() === null ? false : true;
        $this->wizard->set_complete($complete);
    }

    /**
     * checks if current user has correct rights
     */
    public function is_allowed($right)
    {
        return $this->get_parent()->is_allowed($right);
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

    public function is_allowed_to_edit_content_object()
    {
        return $this->get_parent()->is_allowed_to_edit_content_object();
    }

    public function is_allowed_to_edit_feedback()
    {
        return $this->get_parent()->is_allowed_to_edit_feedback();
    }

    public function is_allowed_to_view_content_object()
    {
        return $this->get_parent()->is_allowed_to_view_content_object();
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

    public function remove_user_from_group($user_id, $group_id)
    {
        return $this->get_parent()->get_parent()->remove_user_from_group($user_id, $group_id);
    }

    /**
     * adds wizard menu to footer
     */
    function render_footer()
    {
        $html = array();

        $html[] = $this->wizard->display(Request::get(self::PARAM_ACTION), self::DEFAULT_ACTION);
        $html[] = parent::render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * adds wizard menu to header
     */
    function render_header()
    {
        $html = array();

        $html[] = parent::render_header();
        $html[] = $this->wizard->display(Request::get(self::PARAM_ACTION), self::DEFAULT_ACTION);

        return implode(PHP_EOL, $html);
    }

    public function user_is_enrolled_in_group($user_id)
    {
        return $this->get_parent()->get_parent()->user_is_enrolled_in_group($user_id);
    }
}
