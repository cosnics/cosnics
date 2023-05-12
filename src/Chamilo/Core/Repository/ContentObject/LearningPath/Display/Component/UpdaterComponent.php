<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Metadata\Service\InstanceService;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\GenericTabsRenderer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * @package core\repository\content_object\learning_path\display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdaterComponent extends BaseHtmlTreeComponent
{

    /**
     * Executes this component
     */
    public function build()
    {
        $this->validateSelectedTreeNodeData();

        if ($this->canEditCurrentTreeNode())
        {
            $content_object = $this->getCurrentContentObject();

            $form = ContentObjectForm::factory(
                ContentObjectForm::TYPE_EDIT, $this->getCurrentWorkspace(), $content_object, 'edit',
                FormValidator::FORM_METHOD_POST, $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                    self::PARAM_CHILD_ID => $this->getCurrentTreeNodeDataId()
                ]
            )
            );

            if ($form->validate())
            {
                $succes = $form->update_content_object();

                if ($succes)
                {
                    Event::trigger(
                        'Activity', Manager::CONTEXT, [
                            Activity::PROPERTY_TYPE => Activity::ACTIVITY_UPDATED,
                            Activity::PROPERTY_USER_ID => $this->get_user_id(),
                            Activity::PROPERTY_DATE => time(),
                            Activity::PROPERTY_CONTENT_OBJECT_ID => $content_object->get_id(),
                            Activity::PROPERTY_CONTENT => $content_object->get_title()
                        ]
                    );
                }

                if ($succes && $form->is_version())
                {
                    try
                    {
                        $treeNodeDataService = $this->getLearningPathService();
                        $treeNodeDataService->updateContentObjectInTreeNode(
                            $this->getCurrentTreeNode(), $content_object->get_latest_version()
                        );
                    }
                    catch (Exception $ex)
                    {
                        $succes = false;
                    }
                }

                $message = htmlentities(
                    Translation::get(
                        ($succes ? 'ObjectUpdated' : 'ObjectNotUpdated'),
                        ['OBJECT' => Translation::get('ContentObject')], StringUtilities::LIBRARIES
                    )
                );

                $params = [];
                $params[self::PARAM_ACTION] = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

                $values = $form->exportValues();
                $addMetadataSchema = $values[InstanceService::PROPERTY_METADATA_ADD_SCHEMA];
                if (isset($addMetadataSchema))
                {
                    $params[self::PARAM_ACTION] = self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM;
                    $params[GenericTabsRenderer::PARAM_SELECTED_TAB] = [
                        Manager::TABS_CONTENT_OBJECT => $form->getSelectedTabIdentifier()
                    ];

                    $filters = [];
                }
                else
                {
                    $filters = [self::PARAM_CONTENT_OBJECT_ID];
                }

                $this->redirectWithMessage($message, (!$succes), $params, $filters);
            }
            else
            {
                if ($this->getCurrentTreeNode()->isRootNode())
                {
                    $title = Translation::get('ChangeIntroduction');
                }
                else
                {
                    $title = Translation::get(
                        'EditContentObject', ['CONTENT_OBJECT' => $this->getCurrentContentObject()->get_title()]
                    );
                }

                $trail = BreadcrumbTrail::getInstance();
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(), $title
                    )
                );

                $html = [];

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            throw new NotAllowedException();
        }
    }

    protected function getCurrentWorkspace(): Workspace
    {
        return $this->getService('Chamilo\Core\Repository\CurrentWorkspace');
    }

    /**
     * @return string
     */
    public function render_header(string $pageTitle = ''): string
    {
        $html = [];

        $html[] = parent::render_header($pageTitle);
        $html[] = $this->renderRepoDragPanel();

        return implode(PHP_EOL, $html);
    }
}
