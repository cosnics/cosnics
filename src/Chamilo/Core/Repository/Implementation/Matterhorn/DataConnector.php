<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn;

use Chamilo\Core\Repository\Implementation\Matterhorn\DublinCore\MediaPackageDublinCore;
use Chamilo\Core\Repository\Implementation\Matterhorn\DublinCore\SeriesDublinCore;
use Chamilo\Core\Repository\Implementation\Matterhorn\Form\ExternalObjectForm;
use Chamilo\Core\Repository\Implementation\Matterhorn\Parser\MediaPackage\SimpleXmlMediaPackageParser;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Libraries\Utilities\UUID;
use Exception;
use GuzzleHttp\Post\PostFile;

/**
 *
 * @package core\repository\implementation\matterhorn
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{

    /**
     *
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     *
     * @param \core\repository\instance\storage\data_class\Instance $external_repository_instance
     */
    public function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $url = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'url',
            $this->get_external_repository_instance_id());

        $this->client = new \GuzzleHttp\Client(['base_url' => $url]);

        $enable_authentication = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'enable',
            $this->get_external_repository_instance_id());

        if ($enable_authentication)
        {
            $username = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
                'username',
                $this->get_external_repository_instance_id());
            $password = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
                'password',
                $this->get_external_repository_instance_id());

            $this->client->setDefaultOption('auth', [$username, $password, 'digest']);
            $this->client->setDefaultOption(
                'headers',
                ['X-Requested-Auth' => 'Digest', 'X-Opencast-Matterhorn-Authorization' => 'true']);
        }
    }

    /**
     *
     * @see \core\repository\external\DataConnector::retrieve_external_repository_objects()
     */
    public function retrieve_external_repository_objects($condition, $order_by, $offset, $count)
    {
        $order_by = $order_by[0];

        if ($order_by instanceof OrderBy && $order_by->get_property() instanceof PropertyConditionVariable)
        {
            switch ($order_by->get_property()->get_property())
            {
                case ExternalObject :: PROPERTY_TITLE :
                    $sort = 'TITLE';
                    break;
                case ExternalObject :: PROPERTY_CREATED :
                    $sort = 'DATE_CREATED';
                    break;
                default :
                    $sort = '';
                    break;
            }

            $sort .= $order_by->get_direction() == SORT_DESC ? '_DESC' : '';
        }
        else
        {
            $sort = '';
        }

        $response = $this->client->get(
            '/search/episode.xml',
            ['query' => ['limit' => $count, 'offset' => $offset, 'q' => $condition, 'sort' => $sort]]);

        $results = $response->xml();

        $objects = array();

        if ($results->result->count() > 0)
        {
            foreach ($results->result as $result)
            {
                $parser = new SimpleXmlMediaPackageParser($result);

                $object = $parser->process();
                $object->set_external_repository_id($this->get_external_repository_instance_id());
                $object->set_rights($this->determine_rights());

                $objects[] = $object;
            }
        }

        return new ArrayResultSet($objects);
    }

    /**
     *
     * @see \core\repository\external\DataConnector::retrieve_external_repository_object()
     */
    public function retrieve_external_repository_object($id)
    {
        $response = $this->retrieve_media_package($id);
        $results = $response->xml();

        if ($results->result->count() > 0)
        {
            $parser = new SimpleXmlMediaPackageParser($results->result[0]);

            $object = $parser->process();
            $object->set_external_repository_id($this->get_external_repository_instance_id());
            $object->set_rights($this->determine_rights());

            return $object;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @param string $id
     * @return \GuzzleHttp\Message\Response
     */
    public function retrieve_media_package($id)
    {
        return $this->client->get('/search/episode.xml', ['query' => ['id' => $id]]);
    }

    /**
     *
     * @see \core\repository\external\DataConnector::count_external_repository_objects()
     */
    public function count_external_repository_objects($condition)
    {
        $response = $this->client->get('/search/episode.xml', ['query' => ['limit' => 1, 'q' => $condition]]);
        return (int) $response->xml()->attributes()->total;
    }

    /**
     *
     * @param string[] $values
     * @param string $track_path
     * @return boolean \core\repository\implementation\matterhorn\ExternalObject
     */
    public function create_external_repository_object($values, $track_path = null)
    {
        // Get an empty media package container
        $response = $this->client->get('/ingest/createMediaPackage');
        $media_package_xml = $response->getBody()->getContents();

        // Add a track to it (the uploaded file or a file selected from the inbox)
        $request = $this->client->createRequest('POST', '/ingest/addTrack');
        $postBody = $request->getBody();
        $postBody->setField('flavor', $values[ExternalObjectForm :: PARAM_WORKFLOW]);
        $postBody->setField('mediaPackage', $media_package_xml);

        if ($values[ExternalObjectForm :: PARAM_UPLOAD] == 0)
        {
            $postBody->addFile(new PostFile('file', fopen($track_path, 'r')));
        }
        elseif ($values[ExternalObjectForm :: PARAM_UPLOAD] == 1)
        {
            $postBody->addFile(new PostFile('file', $values['inbox']));
        }
        else
        {
            return false;
        }

        $response = $this->client->send($request);
        $media_package_xml = $response->getBody()->getContents();

        // Add the media package dublin core metadata

        $media_package_dublin_core_identifier = UUID :: v4();
        $media_package_dublin_core = new MediaPackageDublinCore(
            $media_package_dublin_core_identifier,
            $values[ExternalObject :: PROPERTY_TITLE],
            $values[ExternalObject :: PROPERTY_OWNER_ID],
            $values[ExternalObject :: PROPERTY_CONTRIBUTORS],
            $values[ExternalObject :: PROPERTY_DESCRIPTION],
            $values[ExternalObject :: PROPERTY_SUBJECTS],
            $values[ExternalObject :: PROPERTY_LICENSE]);

        $request = $this->client->createRequest('POST', '/ingest/addDCCatalog');
        $postBody = $request->getBody();
        $postBody->setField('flavor', 'dublincore/episode');
        $postBody->setField('mediaPackage', $media_package_xml);
        $postBody->setField('dublinCore', $media_package_dublin_core->as_string());

        $response = $this->client->send($request);
        $media_package_xml = $response->getBody()->getContents();

        // If a new series was entered, create it. If not, use the one which was selected (possibly none)
        $new_series = $values[ExternalObjectForm :: NEW_SERIES];

        if ($new_series)
        {
            $series_identifier = UUID :: v4();
            $series_dublin_core = new SeriesDublinCore($series_identifier, $new_series, null, null, $new_series);

            $parameters = array('series' => $series_dublin_core->as_string(), 'acl' => $series_dublin_core->get_acl());

            $request = $this->client->createRequest('POST', '/series/');
            $postBody = $request->getBody();
            $postBody->setField('series', $series_dublin_core->as_string());
            $postBody->setField('acl', $series_dublin_core->get_acl());

            $response = $this->client->send($request);
            $series_xml = $response->getBody()->getContents();
        }
        else
        {
            $series_identifier = $values[ExternalObject :: PROPERTY_SERIES];
            $response = $this->client->get('/series/' . $series_identifier . '.xml');
            $series_xml = $response->getBody()->getContents();
        }

        // If a series was selected, add it to the media package
        if ($series_identifier)
        {
            $request = $this->client->createRequest('POST', '/ingest/addDCCatalog');
            $postBody = $request->getBody();
            $postBody->setField('flavor', 'dublincore/series');
            $postBody->setField('mediaPackage', $media_package_xml);
            $postBody->setField('dublinCore', $series_xml);

            $response = $this->client->send($request);
            $media_package_xml = $response->getBody()->getContents();
        }

        try
        {
            $response = $this->execute_workflow($media_package_xml, 'avilarts-html5');
            $parser = new SimpleXmlMediaPackageParser($response->xml());

            $object = $parser->process();
            $object->set_external_repository_id($this->get_external_repository_instance_id());
            $object->set_rights($this->determine_rights());

            return $object;
        }
        catch (\Exception $exception)
        {
            return false;
        }
    }

    /**
     *
     * @param string $media_package_xml
     * @param string $workflow_id
     * @return Ambigous <\GuzzleHttp\Message\FutureResponse, \GuzzleHttp\Message\ResponseInterface, NULL>
     */
    public function execute_workflow($media_package_xml, $workflow_id)
    {
        $request = $this->client->createRequest('POST', '/ingest/ingest/' . $workflow_id);
        $postBody = $request->getBody();
        $postBody->setField('MEDIAPACKAGE', $media_package_xml);

        return $this->client->send($request);
    }

    public function export_external_repository_object($object)
    {
        return true;
    }

    /**
     *
     * @return boolean[]
     */
    public function determine_rights()
    {
        $rights = array();
        $rights[ExternalObject :: RIGHT_USE] = true;
        $rights[ExternalObject :: RIGHT_EDIT] = false;
        $rights[ExternalObject :: RIGHT_DELETE] = true;
        $rights[ExternalObject :: RIGHT_DOWNLOAD] = false;
        return $rights;
    }

    /**
     *
     * @return \libraries\storage\ArrayResultSet
     */
    public function get_inbox_list()
    {
        $files = array();

        $response = $this->client->get('/files/list/inbox.json');
        $results = $response->json();

        foreach ($results as $result)
        {
            $file = new InboxFile();
            $file->set_path($result);
            $files[] = $file;
        }

        return new ArrayResultSet($files);
    }

    /**
     *
     * @param $query string
     * @return string
     */
    public static function translate_search_query($query)
    {
        return $query;
    }

    /**
     *
     * @param string $id
     * @return \core\repository\implementation\matterhorn\Series
     */
    public function get_series($id)
    {
        $series = new Series();

        if ($id)
        {
            $response = $this->client->get('/series/' . $id . '.xml');
            $result = $response->xml(['ns_is_prefix' => true, 'ns' => 'dcterms']);

            $series->set_id((string) $result->identifier);
            $series->set_title((string) $result->title);
            $series->set_description((string) $result->description);
        }

        return $series;
    }

    /**
     *
     * @return \libraries\storage\ArrayResultSet
     */
    public function get_all_series()
    {
        $collections = array();

        $response = $this->client->get('/series/series.xml', ['query' => ['sort' => 'TITLE']]);
        $result = $response->xml();

        foreach ($result->dublincore as $collection)
        {
            $properties = $collection->children('dcterms', true);
            $series = new Series();

            $series->set_id((string) $properties->identifier);
            $series->set_title((string) $properties->title);
            $series->set_description((string) $properties->description);

            $collections[] = $series;
        }

        return new ArrayResultSet($collections);
    }

    /**
     *
     * @return \libraries\storage\PropertyConditionVariable[]
     */
    public static function get_sort_properties()
    {
        $properties = array();
        $properties[] = new PropertyConditionVariable(ExternalObject :: class_name(), ExternalObject :: PROPERTY_TITLE);
        $properties[] = new PropertyConditionVariable(ExternalObject :: class_name(), ExternalObject :: PROPERTY_CREATED);
        return $properties;
    }

    /**
     *
     * @see \core\repository\external\DataConnector::delete_external_repository_object()
     */
    public function delete_external_repository_object($id)
    {
        $request = $this->client->createRequest('POST', '/episode/applyworkflow/');
        $postBody = $request->getBody();
        $postBody->setField('id', $id);
        $postBody->setField('definitionId', 'retract');

        try
        {
            $response = $this->client->send($request);
            return true;
        }
        catch (\Exception $exception)
        {
            return false;
        }
    }
}
