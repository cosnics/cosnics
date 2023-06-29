<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Table;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assessment\Builder\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AnswerFeedbackTypeTableRenderer extends DataClassListTableRenderer
    implements TableRowActionsSupport, TableActionsSupport
{
    public const PROPERTY_FEEDBACK_TYPE = 'FeedbackType';
    public const PROPERTY_TYPE = 'Type';

    public const TABLE_IDENTIFIER = Manager::PARAM_COMPLEX_QUESTION_ID;

    protected StringUtilities $stringUtilities;

    public function __construct(
        StringUtilities $stringUtilities, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->stringUtilities = $stringUtilities;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    /**
     * @throws \Exception
     */
    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $types = [
            Configuration::ANSWER_FEEDBACK_TYPE_NONE,
            Configuration::ANSWER_FEEDBACK_TYPE_GIVEN,
            Configuration::ANSWER_FEEDBACK_TYPE_GIVEN_CORRECT,
            Configuration::ANSWER_FEEDBACK_TYPE_GIVEN_WRONG,
            Configuration::ANSWER_FEEDBACK_TYPE_CORRECT,
            Configuration::ANSWER_FEEDBACK_TYPE_WRONG,
            Configuration::ANSWER_FEEDBACK_TYPE_ALL
        ];

        foreach ($types as $type)
        {
            $actions->addAction(
                new TableAction(
                    $this->getUrlGenerator()->fromRequest(
                        [
                            Manager::PARAM_ACTION => Manager::ACTION_ANSWER_FEEDBACK_TYPE,
                            Manager::PARAM_ANSWER_FEEDBACK_TYPE => $type
                        ]
                    ), Configuration::answer_feedback_string($type)
                )
            );
        }

        return $actions;
    }

    protected function initializeColumns(): void
    {
        $translator = $this->getTranslator();

        $glyph = new FontAwesomeGlyph('folder', [], $translator->trans('Type', [], Manager::CONTEXT));
        $this->addColumn(new StaticTableColumn(self::PROPERTY_TYPE, $glyph->render()));

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_TITLE
            )
        );

        $glyph = new FontAwesomeGlyph('comment', [], $translator->trans('FeedbackType', [], Manager::CONTEXT));
        $this->addColumn(new StaticTableColumn(self::PROPERTY_FEEDBACK_TYPE, $glyph->render()));
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem $complexContentObjectItem
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $complexContentObjectItem
    ): string
    {
        /**
         * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
         */
        $contentObject = $complexContentObjectItem->get_ref_object();

        switch ($column->get_name())
        {
            case self::PROPERTY_TYPE :
                return $contentObject->get_icon_image(IdentGlyph::SIZE_MINI);
            case ContentObject::PROPERTY_TITLE :
                $title = parent::renderCell($column, $resultPosition, $contentObject);

                return $this->getStringUtilities()->truncate($title, 53, false);
            case self::PROPERTY_FEEDBACK_TYPE :
                $glyph = Configuration::answerFeedbackGlyph($complexContentObjectItem->get_show_answer_feedback());

                return $glyph->render();
        }

        return parent::renderCell($column, $resultPosition, $complexContentObjectItem);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem $complexContentObjectItem
     *
     * @throws \Exception
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $complexContentObjectItem): string
    {

        $toolbar = new Toolbar();

        $types = [
            Configuration::ANSWER_FEEDBACK_TYPE_NONE,
            Configuration::ANSWER_FEEDBACK_TYPE_GIVEN,
            Configuration::ANSWER_FEEDBACK_TYPE_GIVEN_CORRECT,
            Configuration::ANSWER_FEEDBACK_TYPE_GIVEN_WRONG,
            Configuration::ANSWER_FEEDBACK_TYPE_CORRECT,
            Configuration::ANSWER_FEEDBACK_TYPE_WRONG,
            Configuration::ANSWER_FEEDBACK_TYPE_ALL
        ];

        foreach ($types as $type)
        {
            if ($complexContentObjectItem->get_show_answer_feedback() != $type)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Configuration::answer_feedback_string($type), Configuration::answerFeedbackGlyph($type),
                        $this->getUrlGenerator()->fromRequest(
                            [
                                Manager::PARAM_ANSWER_FEEDBACK_TYPE => $type,
                                Manager::PARAM_COMPLEX_QUESTION_ID => $complexContentObjectItem->getId()
                            ]
                        ), ToolbarItem::DISPLAY_ICON
                    )
                );
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Configuration::answer_feedback_string($type), Configuration::answerFeedbackGlyph($type, true),
                        null, ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }

        return $toolbar->render();
    }
}
