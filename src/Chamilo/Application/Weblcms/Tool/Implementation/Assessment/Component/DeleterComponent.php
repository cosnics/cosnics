<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

class DeleterComponent extends Manager
{

    /**
     * Modified version of the default Deleter to allow for the feedback-functionality
     */
    public function run()
    {
        if ($this->getRequest()->query->has(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID))
        {
            $publication_ids =
                $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        }
        else
        {
            $publication_ids =
                $this->getRequest()->request->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        }

        if (!is_array($publication_ids))
        {
            $publication_ids = [$publication_ids];
        }

        $failures = 0;

        foreach ($publication_ids as $pid)
        {
            $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                ContentObjectPublication::class, $pid
            );

            $content_object = $publication->get_content_object();

            if ($content_object->getType() == Introduction::class)
            {
                $publication->ignore_display_order();
            }

            if ($this->is_allowed(WeblcmsRights::DELETE_RIGHT, $publication))
            {
                if ($publication->delete())
                {
                    $parameters = new DataClassRetrieveParameters(
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                Publication::class, Publication::PROPERTY_PUBLICATION_ID
                            ), new StaticConditionVariable($publication->get_id())
                        )
                    );
                    $assessment_publication = DataManager::retrieve(Publication::class, $parameters);
                    if (!$assessment_publication->delete())
                    {
                        $failures ++;
                    }
                }
                else
                {
                    $failures ++;
                }
            }
            else
            {
                $failures ++;
            }
        }

        if ($failures == 0)
        {
            if (count($publication_ids) > 1)
            {
                $message = htmlentities(Translation::get('ContentObjectPublicationsDeleted'));
            }
            else
            {
                $message = htmlentities(Translation::get('ContentObjectPublicationDeleted'));
            }
        }
        else
        {
            $message = htmlentities(Translation::get('ContentObjectPublicationsNotDeleted'));
        }

        $this->redirectWithMessage(
            $message, $failures !== 0,
            [\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => null, 'tool_action' => null]
        );
    }

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}
