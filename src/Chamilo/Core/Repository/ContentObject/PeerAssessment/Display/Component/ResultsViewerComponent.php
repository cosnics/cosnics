<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass\PeerAssessment;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Enter description here .
 * ..get_
 *
 * @author admin
 */
class ResultsViewerComponent extends Manager
{

    private $attempt_is_allowed = null;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->publication_id = Request::get(self::PARAM_PUBLICATION);
        $this->group_id = Request::get(self::PARAM_GROUP);
        $this->root_content_object = $this->get_root_content_object();

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->render_action_bar();
        $html[] = $this->render_tabs();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * determines if results can be displayed for a certain user/attempt
     *
     * @param int $user_id
     * @param tint $attempt_id
     *
     * @return boolean
     */
    function display_results_allowed($user_id, $attempt_id)
    {
        // van_achter needs all scores for the attempt to be given
        if ($this->root_content_object->get_scale() == 'van_achter')
        {
            if (is_null($this->attempt_is_allowed))
            {
                $this->attempt_is_allowed = $this->get_root_content_object()->get_result_processor()->retrieve_scores(
                    $this, $user_id, $attempt_id
                );
            }

            return $this->attempt_is_allowed;
        }

        return true;
    }

    private function get_total($user_id, $status_array, $attempts)
    {
        $factor_sum = 0;

        foreach ($attempts as $a)
        {
            $w = $a->get_weight();
            $s = $status_array[$a->get_id()][$user_id];
            $weighted_factor = ((100 - $w) + ($w * $s->get_factor())) / 100;
            $factor_sum += $weighted_factor;
        }

        return $factor_sum / count($attempts);
    }

    public function render_action_bar()
    {
        if ($this->is_allowed(self::EDIT_RIGHT))
        {
            $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

            $buttonToolbar = $this->buttonToolbarRenderer->getButtonToolBar();
            $commonActions = new ButtonGroup();
            $commonActions->addButton(
                new Button(
                    Translation::get('Export', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('download'),
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_EXPORT_RESULT,
                            self::PARAM_EXPORT_TYPE => self::EXPORT_TYPE_EXCEL
                        )
                    )
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);

            return $this->buttonToolbarRenderer->render();
        }
    }

    private function render_details_link($status, $complete = true)
    {
        $item = new ToolbarItem(
            Translation::get('Details'), ($complete ? new FontAwesomeGlyph('info-circle') :
            new FontAwesomeGlyph('info-circle', array('text-muted'))), $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_VIEW_USER_RESULTS, self::PARAM_ATTEMPT => $status->get_attempt_id(),
                self::PARAM_USER => $status->get_user_id()
            )
        ), ToolbarItem::DISPLAY_ICON
        );

        return $item->as_html();
    }

    private function render_group($group_id)
    {
        $users = $this->get_group_users($group_id);
        if (!$this->is_allowed(self::EDIT_RIGHT))
        {

            $found = false;
            foreach ($users as $user)
            {
                if ($user->get_id() == $this->get_user()->get_id())
                {
                    $users = array($user);
                    $found = true;
                }
            }
            if (!$found)
            {
                $this->redirect(
                    Translation::get('NoGroupSubscription'), true,
                    array(self::PARAM_ACTION, self::ACTION_VIEW_USER_ATTEMPT_STATUS)
                );
            }
        }
        $attempts = $this->get_attempts($this->publication_id);

        $html = array();
        $html[] = '<table class="table table-striped table-bordered table-hover table-data" style="width: auto">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th>' . Translation::get('User') . '</th>';

        foreach ($attempts as $a)
        {
            $html[] = '<th>' . $a->get_title() . '<br />(' . Translation::get('Weight') . ':' . $a->get_weight() . ')' .
                '</th>';

            // calculate completeness of scores
            $show_detail[$a->get_id()] = true;
            foreach ($users as $u)
            {
                $status_array[$a->get_id()][$u->get_id()] = $status = $this->get_user_attempt_status(
                    $u->get_id(), $a->get_id()
                );

                if (is_null($status_array[$a->get_id()][$u->get_id()]->get_progress()) &&
                    $this->root_content_object->get_assessment_type() == PeerAssessment::TYPE_SCORES)
                {
                    $show_detail[$a->get_id()] = false;
                }
            }
        }

        if ($this->root_content_object->get_assessment_type() != PeerAssessment::TYPE_FEEDBACK)
        {
            $html[] = '<th>' . Translation::get('Total') . '</th>';
        }
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        $factor_title = $this->root_content_object->get_factor_title();

        foreach ($users as $u)
        {
            $show_factor = 0;

            $html[] = '<tr>';
            $html[] = '<td style="min-width: 200px">' . $u->get_firstname() . ' ' . $u->get_lastname() . '</td>';

            foreach ($attempts as $a)
            {

                $status = $status_array[$a->get_id()][$u->get_id()];

                $url = $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_VIEW_USER_RESULTS, self::PARAM_ATTEMPT => $a->get_id(),
                        self::PARAM_USER => $u->get_id()
                    )
                );

                $html[] = '<td style="min-width: 100px">';

                // only show details if attempt is closed and scores can be processed
                if ($status->get_closed() || $a->get_end_date() < time())
                {

                    if ($this->root_content_object->get_assessment_type() != PeerAssessment::TYPE_FEEDBACK)
                    {
                        $html[] = '<div>' . $factor_title . ': ' . round($status->get_factor(), 2) . '</div>';
                    }

                    if ($this->display_results_allowed($u->get_id(), $a->get_id()))
                    {
                        $html[] = '<div>' . $this->render_details_link($status) . '</div>';
                        $show_factor ++;
                    }
                    else
                    {
                        $html[] = '<div>' . $this->render_details_link($status, false) . '</div>';
                    }
                }
                else
                {
                    $html[] = '<div>' . Translation::get('NotClosed') . '</div>';
                    $html[] = '<div>' . $this->render_details_link($status, false) . '</div>';
                }
                $html[] = '</td>';
            }
            if ($this->root_content_object->get_assessment_type() != PeerAssessment::TYPE_FEEDBACK)
            {
                $html[] = '<td style="text-align: center">';
                if ($show_factor === count($attempts))
                {
                    $html[] = round($this->get_total($u->get_id(), $status_array, $attempts), 2);
                }
                else
                {
                    $html[] = Translation::get('FactorNotAvailable');
                }
                $html[] = '</td>';
            }
            $html[] = '</tr>';
        }

        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode(PHP_EOL, $html);
    }

    private function render_tabs()
    {
        if ($this->is_allowed(self::EDIT_RIGHT))
        {
            $groups = $this->get_groups($this->publication_id);
        }
        else
        {
            $user_group = $this->get_user_group($this->get_user()->get_id());

            if ($user_group)
            {
                $groups = array($user_group);
            }
        }

        if (!$groups)
        {
            if ($this->is_allowed(self::EDIT_RIGHT))
            {
                $this->redirect(null, true, array(self::PARAM_ACTION => null));
            }
            else
            {
                throw new NotAllowedException();
            }
        }

        $group_id = empty($this->group_id) ? $groups[0]->get_id() : $this->group_id;

        $tabs = new DynamicVisualTabsRenderer();

        // loop through the groups
        foreach ($groups as $g)
        {
            $url = $this->get_url(array(self::PARAM_GROUP => $g->get_id()));

            $tab = new DynamicVisualTab('tab_' + $g->get_id(), $g->get_name(), null, $url);

            if ($g->get_id() == $group_id)
            {
                $tab->set_selected(true);
                $tabs->set_content('tab');
                $tabs->set_content($this->render_group($g->get_id()));
            }

            $tabs->add_tab($tab);
        }

        return $tabs->render();
    }
}
