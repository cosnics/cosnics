<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Table\PropertiesTable;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager implements TableSupport
{

    private $buttonToolbarRenderer;

    public function run()
    {
        return $this->getTwig()->render(Manager::context() . ':EntryBrowser.html.twig', $this->getTemplateProperties());
    }

    /**
     *
     * @return string[]
     */
    protected function getTemplateProperties()
    {
        $entryCount = $this->getDataProvider()->countEntriesForEntityTypeAndId(
            $this->getEntityType(),
            $this->getEntityIdentifier()
        );

        $feedbackCount = $this->getDataProvider()->countDistinctFeedbackForEntityTypeAndId(
            $this->getEntityType(),
            $this->getEntityIdentifier()
        );

        $scoreCount = $this->getDataProvider()->countDistinctScoreForEntityTypeAndId(
            $this->getEntityType(),
            $this->getEntityIdentifier()
        );

        $averageScore = $this->getDataProvider()->getAverageScoreForEntityTypeAndId(
            $this->getEntityType(),
            $this->getEntityIdentifier()
        );

        $averageScoreValue = $averageScore ? round($averageScore, 2) . '%' : '-';

        return [
            'HEADER' => $this->render_header(),
            'FOOTER' => $this->render_footer(),
            'BUTTON_TOOLBAR' => $this->getButtonToolbarRenderer()->render(),
            'CONTENT_OBJECT_RENDITION' => $this->renderContentObject(),
            'ENTRY_COUNT' => $entryCount, 'FEEDBACK_COUNT' => $feedbackCount, 'SCORE_COUNT' => $scoreCount,
            'AVERAGE_SCORE' => $averageScoreValue,
            'ENTRY_TABLE' => $this->renderEntryTable()
        ];
    }

    /**
     *
     * @return string
     */
    private function renderContentObject()
    {
        $display = ContentObjectRenditionImplementation::factory(
            $this->get_root_content_object(),
            ContentObjectRendition::FORMAT_HTML,
            ContentObjectRendition::VIEW_FULL,
            $this
        );

        return $display->render();
    }

    /**
     *
     * @return string
     */
    private function renderEntryTable()
    {
        $table = $this->getDataProvider()->getEntryTableForEntityTypeAndId(
            $this,
            $this->getEntityType(),
            $this->getEntityIdentifier()
        );

        if (!empty($table))
        {
            return $table->render();
        }

        return '';
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Interfaces\TableSupport::get_table_condition()
     */
    public function get_table_condition($tableClassName)
    {
        return null;
    }

    protected function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolBar = new ButtonToolBar();

            $buttonToolBar->addButtonGroup(
                new ButtonGroup(
                    array(
                        new Button(
                            Translation::get('Download'),
                            new FontAwesomeGlyph('download'),
                            $this->get_url(
                                [
                                    self::PARAM_ACTION => self::ACTION_DOWNLOAD,
                                    self::PARAM_ENTITY_TYPE => $this->getEntityType(),
                                    self::PARAM_ENTITY_ID => $this->getEntityIdentifier()
                                ]
                            )
                        ),
                        new Button(
                            Translation::get('SubmissionSubmit'),
                            new FontAwesomeGlyph('plus'),
                            $this->get_url(
                                [
                                    self::PARAM_ACTION => self::ACTION_CREATE,
                                    self::PARAM_ENTITY_TYPE => $this->getEntityType(),
                                    self::PARAM_ENTITY_ID => $this->getEntityIdentifier()
                                ]
                            )
                        )
                    )
                )
            );

            $buttonToolBar->addItem(
                new Button(
                    Translation::get(
                        'BrowseEntities',
                        ['NAME' => strtolower($this->getDataProvider()->getEntityNameByType($this->getEntityType()))]
                    ),
                    new FontAwesomeGlyph('user'),
                    $this->get_url(
                        [
                            self::PARAM_ACTION => self::ACTION_VIEW
                        ]
                    )
                )
            );

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function get_additional_parameters()
    {
        return array(self::PARAM_ENTITY_ID, self::PARAM_ENTITY_TYPE);
    }
}
