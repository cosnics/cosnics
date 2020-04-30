<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class PeerAssessmentGroupForm extends FormValidator
{
    const FORM_NAME = 'peer_assessment_group_form';

    const FORM_TYPE_CREATE = 2;

    const FORM_TYPE_EDIT = 1;

    const PARAM_DESCRIPTION = 'description';

    const PARAM_GROUP = 'group';

    const PARAM_NAME = 'name';

    const PARAM_PLATFORM_GROUP = 'platform';

    const PARAM_USER = 'user';

    /**
     *
     * @var PeerAssessmentDisplayViewerComponent
     */
    private $viewer;

    private $group_id = null;

    private $enroll_errors = null;

    /**
     * Constructor
     *
     * @param PeerAssessmentDisplayViewerComponent $viewer
     */
    public function __construct($viewer, $group_id, $url, $type = self :: FORM_TYPE_CREATE)
    {
        $this->viewer = $viewer;
        $this->group_id = $group_id;

        parent::__construct(self::FORM_NAME, self::FORM_METHOD_POST, $url);

        $this->build_basic_form();

        if ($type == self::FORM_TYPE_CREATE)
        {
            $this->build_create_buttons();
        }
        else
        {
            $this->build_edit_buttons();
        }
    }

    private function build_basic_form()
    {
        $this->add_textfield(self::PARAM_NAME, Translation::get('Name', null, Utilities::COMMON_LIBRARIES));

        $value = Configuration::getInstance()->get_setting(
            array(\Chamilo\Core\Repository\Manager::context(), 'description_required')
        );

        $required = ($value == 1) ? true : false;
        $name =
            Translation::get('Description', array(), ClassnameUtilities::getInstance()->getNamespaceFromObject($this));
        $this->add_html_editor(self::PARAM_DESCRIPTION, $name, $required);

        $defaults = array();
        // set defaults if there are any
        if (!is_null($this->group_id))
        {
            $group_users = $this->viewer->get_group_users($this->group_id);
            $glyph = new FontAwesomeGlyph('user', array(), null, 'fas');

            foreach ($group_users as $user)
            {
                $element['classes'] = $glyph->getClassNamesString();
                $element['id'] = self::PARAM_USER . '_' . $user->get_id();
                $element['title'] = $user->get_firstname() . ' ' . $user->get_lastname();
                $defaults[] = $element;
            }
        }

        if (!$this->viewer->group_has_scores($this->group_id)) // only allow to change users if no scores are given
        {
            $attributes['defaults'] = $defaults;
            $attributes['exclude'] = array();
            $attributes['nodesSelectable'] = true;
            $attributes['locale']['Display'] = Translation::get('SelectUsersOrGroups');
            $attributes['locale']['Searching'] = Translation::get('Searching', null, Utilities::COMMON_LIBRARIES);
            $attributes['locale']['NoResults'] = Translation::get('NoResults', null, Utilities::COMMON_LIBRARIES);
            $attributes['locale']['Error'] = Translation::get('Error', null, Utilities::COMMON_LIBRARIES);
            $attributes['locale']['load_elements'] = true;

            $attributes['search_url'] = $this->viewer->get_group_feed_path();
            $attributes['options']['load_elements'] = true;

            $legend_items = array();
            $legend_items[] = new ToolbarItem(
                Translation::get('User'), new FontAwesomeGlyph('user'), null, ToolbarItem::DISPLAY_ICON_AND_LABEL,
                false, 'legend'
            );
            $legend_items[] = new ToolbarItem(
                Translation::get('Group'), new FontAwesomeGlyph('users'), null, ToolbarItem::DISPLAY_ICON_AND_LABEL,
                false, 'legend'
            );

            $legend = new Toolbar();
            $legend->set_items($legend_items);
            $legend->set_type(Toolbar::TYPE_HORIZONTAL);

            $element_finder = $this->createElement(
                'user_group_finder', Manager::PARAM_GROUP_USERS, Translation::get('SelectUsersOrGroups'),
                $attributes['search_url'], $attributes['locale'], $attributes['defaults'], $attributes['options']
            );
            $element_finder->excludeElements($attributes['exclude']);
            $this->addElement($element_finder);

            $this->addElement('static', null, null, $legend->as_html());
        }
        else
        {
            $this->addElement(
                'html', Translation::get('GroupBlockedBecauseOfScores') . ': <a href="' . $this->viewer->get_url(
                    array(
                        \Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Manager::ACTION_REMOVE_SCORES,
                        \Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Manager::PARAM_GROUP  => $this->group_id
                    )
                ) . '">' . Translation::get('RemoveScoresToUnblock') . '</a>'
            );
        }
    }

    private function build_create_buttons()
    {
        $this->addElement(
            'style_submit_button', FormValidator::PARAM_SUBMIT,
            Translation::get('Create', null, Utilities::COMMON_LIBRARIES)
        );
    }

    private function build_edit_buttons()
    {
        $this->addElement(
            'style_submit_button', FormValidator::PARAM_SUBMIT,
            Translation::get('Edit', null, Utilities::COMMON_LIBRARIES)
        );
    }

    public function get_enroll_errors()
    {
        return $this->enroll_errors;
    }

    public function set_group_id($group_id)
    {
        $this->group_id = $group_id;
    }

    /**
     * updates memberships of peer assessment group
     */
    public function update_group_memberships()
    {
        if (!is_null($this->group_id) && !$this->viewer->group_has_scores($this->group_id))
        {

            $group_users_arr = $this->viewer->get_group_users($this->group_id);
            $group_users = array();

            foreach ($group_users_arr as $user)
            {
                $group_users[$user->get_id()] = $user->get_id();
            }

            $values = $this->exportValue(
                Manager::PARAM_GROUP_USERS
            );

            foreach ($values as $type => $elements)
            {
                foreach ($elements as $id)
                {
                    if ($type == self::PARAM_USER)
                    {
                        // type = user
                        if (!in_array($id, $group_users))
                        {
                            if (!$this->viewer->user_is_enrolled_in_group($id))
                            {
                                $this->viewer->add_user_to_group($id, $this->group_id); // user can be enrolled in one
                                // PA-group/publication
                            }
                            else
                            {
                                $user = DataManager::retrieve_by_id(
                                    User::class, (int) $id
                                );
                                $already_enrolled[] = $user->get_firstname() . ' ' . $user->get_lastname();
                                unset($group_users[$id]);
                            }
                        }
                        else
                        {
                            unset($group_users[$id]);
                        }
                    }
                    // type = group
                    elseif ($type == self::PARAM_GROUP)
                    {
                        $context_group_users = $this->viewer->get_context_group_users($id);

                        foreach ($context_group_users as $user)
                        {
                            if (!in_array($user->get_id(), $group_users))
                            {
                                if (!$this->viewer->user_is_enrolled_in_group($user->get_id()))
                                {
                                    $this->viewer->add_user_to_group($user->get_id(), $this->group_id); // user can be
                                    // enrolled in
                                    // one
                                    // PA-group/publication
                                }
                                else
                                {
                                    $already_enrolled[] = $user->get_firstname() . ' ' . $user->get_lastname();
                                    unset($group_users[$user->get_id()]);
                                }
                            }
                            else
                            {
                                unset($group_users[$user->get_id()]);
                            }
                        }
                    }
                    elseif ($type == self::PARAM_PLATFORM_GROUP)
                    {
                        $context_group = \Chamilo\Core\Group\Storage\DataManager::getInstance()->retrieve_group($id);

                        if ($context_group)
                        {
                            $context_group_users = $context_group->get_users(true, true);

                            foreach ($context_group_users as $user_id)
                            {
                                if (!in_array($user_id, $group_users))
                                {
                                    if (!$this->viewer->user_is_enrolled_in_group($user_id))
                                    {
                                        $this->viewer->add_user_to_group($user_id, $this->group_id); // user can be
                                        // enrolled in one
                                        // PA-group/publication
                                    }
                                    else
                                    {
                                        $user = DataManager::retrieve_by_id(
                                            User::class, $user_id
                                        );
                                        $already_enrolled[] = $user->get_firstname() . ' ' . $user->get_lastname();
                                        unset($group_users[$user_id]);
                                    }
                                }
                                else
                                {
                                    unset($group_users[$user_id]);
                                }
                            }
                        }
                    }
                }
            }

            // remove remaining users
            foreach ($group_users as $user_id)
            {
                $this->viewer->remove_user_from_group($user_id, $this->group_id);
            }
        }

        if (count($already_enrolled) > 0)
        {
            $this->enroll_errors = implode(',', $already_enrolled) . ' ' . Translation::get('AlreadyEnrolled');
        }
    }
}
