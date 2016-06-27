<?php
namespace Chamilo\Core\Repository\Common\Import\Youtube;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\ContentObject\Youtube\Storage\DataClass\Youtube;
use Chamilo\Core\Repository\External\DataConnector;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class YoutubeContentObjectImportController extends ContentObjectImportController
{
    const FORMAT = 'Chamilo\Core\Repository\ContentObject\Youtube\Storage\DataClass\Youtube';
    const REPOSITORY_TYPE = 'Chamilo\Core\Repository\Implementation\Youtube';

    public function run()
    {
        $url = $this->get_parameters()->get_url();

        if (self :: is_available())
        {
            $url_parts = parse_url($url);

            parse_str($url_parts['query'], $url_query);

            if (strpos($url_parts['host'], 'youtu.be') !== false)
            {
                $external_id = substr($url_parts['path'], 1);
            }
            elseif (strpos($url_parts['host'], 'youtube.com') !== false)
            {
                $external_id = $url_query['v'];
            }
            else
            {
                $this->add_message(Translation :: get('ObjectNotImported'), self :: TYPE_ERROR);
            }

            if (! $this->has_messages(self :: TYPE_ERROR))
            {
                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance :: class_name(),
                        \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance :: PROPERTY_TYPE),
                    new StaticConditionVariable(self :: REPOSITORY_TYPE));
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance :: class_name(),
                        \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance :: PROPERTY_ENABLED),
                    new StaticConditionVariable(1));
                $condition = new AndCondition($conditions);

                $external_repositories = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieves(
                    \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance :: class_name(),
                    new DataClassRetrievesParameters($condition));

                $external_repository = $external_repositories->next_result();
                $youtube_connector = DataConnector :: get_instance($external_repository);
                $external_object = $youtube_connector->retrieve_external_repository_object($external_id);

                $youtube = ContentObject :: factory(Youtube :: class_name());
                $youtube->set_title($external_object->get_title());
                $youtube->set_description($external_object->get_description());
                $youtube->set_owner_id($this->get_parameters()->get_user());
                $youtube->set_parent_id($this->determine_parent_id());

                if ($youtube->create())
                {
                    $this->process_workspace($youtube);

                    \Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData :: quicksave(
                        $youtube,
                        $external_object,
                        $external_repository->get_id());
                    $this->add_message(Translation :: get('ObjectImported'), self :: TYPE_CONFIRM);
                    return array($youtube->get_id());
                }
                else
                {
                    $this->add_message(Translation :: get('ObjectNotImported'), self :: TYPE_ERROR);
                }
            }
        }
        else
        {
            $this->add_message(Translation :: get('YouTubeObjectNotAvailable'), self :: TYPE_WARNING);
        }
    }

    public static function is_available()
    {
        $youtube_object_available = in_array(self :: FORMAT, DataManager :: get_registered_types(true));

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance :: class_name(),
                \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance :: PROPERTY_TYPE),
            new StaticConditionVariable(self :: REPOSITORY_TYPE));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance :: class_name(),
                \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance :: PROPERTY_ENABLED),
            new StaticConditionVariable(1));
        $condition = new AndCondition($conditions);

        $external_repositories = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieves(
            \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance :: class_name(),
            new DataClassRetrievesParameters($condition));
        $youtube_connector_available = $external_repositories->size() == 1;

        return $youtube_object_available && $youtube_connector_available;
    }

    /**
     *
     * @return integer
     */
    public function determine_parent_id()
    {
        if ($this->get_parameters()->getWorkspace() instanceof PersonalWorkspace)
        {
            return $this->get_parameters()->get_category();
        }
        else
        {
            return 0;
        }
    }

    /**
     *
     * @param ContentObject $contentObject
     */
    public function process_workspace(ContentObject $contentObject)
    {
        if ($this->get_parameters()->getWorkspace() instanceof Workspace)
        {
            $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());
            $contentObjectRelationService->createContentObjectRelation(
                $this->get_parameters()->getWorkspace()->getId(),
                $contentObject->getId(),
                $this->get_parameters()->get_category());
        }
    }
}
