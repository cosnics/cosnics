<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Renderer;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service\RequestManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Result;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataManager;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Class that converts the database result to html
 * 
 * @author Anthony Hurst (Hogeschool Gent) Pieterjan Broekaert Hogent
 */
class ResultRenderer
{
    const PERCENTAGE_GREEN = 10;
    const PERCENTAGE_ORANGE = 50;

    /**
     *
     * @var String
     */
    private $xslt_path;

    /**
     * @var RequestManager
     */
    protected $requestManager;

    /**
     * ResultToHtmlConverter constructor.
     */
    public function __construct(RequestManager $requestManager)
    {
        $this->requestManager;
    }

    public function convert_to_html($request_id)
    {
        $this->xslt_path = realpath(__DIR__ . '/../Resources/Xslt');
        $html = $this->get_html($request_id);
        
        return $html;
    }

    private function get_html($request_id)
    {
        $request = DataManager::retrieve_by_id(Request::class, $request_id);
        
        $html = array();
        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getResourcesPath(
                'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus',
                true) . 'Css/' . Theme::getInstance()->getTheme() . '/Report.css');
        
        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(
                'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus',
                true) . 'Report.js');
        $html[] = '<div class="ephorus-report-result" id="printable">';
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Result::class, Result::PROPERTY_REQUEST_ID),
            new StaticConditionVariable($request_id));
        $order_bys = array();
        $order_bys[] = new OrderBy(new PropertyConditionVariable(Result::class, Result::PROPERTY_PERCENTAGE));
        $parameters = new DataClassRetrievesParameters($condition, null, null, $order_bys);
        $results_rs = DataManager::retrieves(Result::class, $parameters);
        
        $results = $results_rs ? $results_rs->as_array() : array();
        
        $diffs = array();
        $html[] = $this->show_summary($request, $results);
        foreach ($results as $result)
        {
            $diffs[] = $this->show_result($result);
        }
        $html[] = implode(PHP_EOL, $diffs);
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }

    private function format_date($date)
    {
        return DatetimeUtilities::format_locale_date(
            Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES), 
            $date);
    }

    private function define_percentage_colour($percentage)
    {
        $colour = 'red';
        if ($percentage < self::PERCENTAGE_ORANGE)
        {
            $colour = 'orange';
        }
        if ($percentage < self::PERCENTAGE_GREEN)
        {
            $colour = 'green';
        }
        
        return '<span style="color: ' . $colour . '">' . $percentage . '%</span>';
    }

    private function generate_link($url)
    {
        return '<a href="' . $url . '">' . $url . '</a>';
    }

    /**
     *
     * @param Request $request
     * @param Result[] $results
     *
     * @return string
     */
    private function show_summary($request, $results)
    {
        $chamilo = array();
        $local = array();
        $internet = array();
        
        foreach ($results as $result)
        {
            if ($result->get_original_guid() === null)
            {
                $internet[$result->get_url()] = $result;
                continue;
            }
            if (strlen($result->get_student_number()) == 0)
            {
                $local[$result->get_original_guid()] = $result;
                continue;
            }
            $chamilo[$result->get_original_guid()] = $result;
        }
        
        $details_parameters = array();
        $details_parameters['REQUESTDATE'] = $this->format_date($request->get_request_time());
        $details_parameters['REQUESTERNAME'] = \Chamilo\Core\User\Storage\DataManager::get_fullname_from_user(
            $request->get_request_user_id());
        // $request->get_request_user_id(); // Transform into Foreign property.
        $details_parameters['AUTHOR'] = $request->get_author()->get_fullname();
        $details_parameters['GUID'] = $request->get_guid();
        $details_parameters['PERCENTAGE'] = $this->define_percentage_colour($request->get_percentage());
        $details_parameters['NUMBERSOURCES'] = count($chamilo) + count($local) + count($internet);
        
        $html = array();
        $html[] = "<table width='100%' cellpadding='0' cellspacing='0'>";
        $html[] = "<td height='19' colspan='2' class='tableTop minFont' style='PADDING-LEFT: 4px; PADDING-TOP: 0px'>";
        $html[] = "<span class='report_table_header'>" . Translation::get('Summary') . "</span>";
        $html[] = "</td>";
        $html[] = "<tr>";
        $html[] = "<td class='tableTop' style='padding-top: 5px; padding-bottom: 5px; padding-right: 5px;'>";
        $html[] = '<p>';
        $html[] = Translation::get('RequestDetails', $details_parameters);
        $html[] = '</p>';
        if (count($chamilo))
        {
            $html[] = '<p>' . Translation::get('ItemsChamilo') . '</p>';
            $html[] = $this->get_chamilo_list($chamilo);
        }
        if (count($local))
        {
            $html[] = '<p>' . Translation::get('ItemsLocal') . '</p>';
            $html[] = $this->get_local_list($local);
        }
        if (count($internet))
        {
            $html[] = '<p>' . Translation::get('ItemsInternet') . '</p>';
            $html[] = $this->get_internet_list($internet);
        }
        // $html[] = $xslt->transformToXml($summary);
        $html[] = "&nbsp;";
        $html[] = "</td>";
        $html[] = "</tr>";
        $html[] = "<tr>";
        $html[] = "<td class='tableBottom'>";
        $html[] = "&nbsp;";
        $html[] = "</td>";
        $html[] = "</tr>";
        $html[] = "</table>";
        
        return implode(PHP_EOL, $html);
    }

    private function get_chamilo_list($chamilo)
    {
        $hits = $this->get_chamilo_hits(array_keys($chamilo));
        
        /**
         *
         * @var Result $result
         * @var \core\repository\ContentObject $hit
         */
        $html = array();
        $html[] = '<ol>';
        foreach ($chamilo as $result)
        {
            $hit = $hits[$result->get_original_guid()];
            $detals_parameters = array();
            
            $percentage = $this->define_percentage_colour($result->get_percentage());
            
            // If no hit is found, the original document is not in the chamilo database
            if ($hit)
            {
                
                $detals_parameters['PERCENTAGE'] = $percentage;
                
                $detals_parameters['TITLE'] = $hit->get_title();
                
                $detals_parameters['AUTHOR'] = $result->get_student_name();
                $detals_parameters['STUDENTCODE'] = $result->get_student_number();
                $detals_parameters['CREATEDATE'] = $this->format_date($hit->get_creation_date());
                $detals_parameters['MODIFIEDDATE'] = $this->format_date($hit->get_modification_date());
                
                $translation = Translation::get('ResultDetails', $detals_parameters);
            }
            else
            {
                $translation = Translation::get(
                    "LocalHitNotFoundOnServer", 
                    array(
                        'PERCENTAGE' => $percentage, 
                        'LOCAL_GUID' => $result->get_original_guid(), 
                        'SOURCE' => $result->get_student_name()));
            }
            
            $html[] = '<li>';
            $html[] = $translation;
            $html[] = '</li>';
        }
        $html[] = '</ol>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param int[] $guids
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject[]
     */
    private function get_chamilo_hits($guids)
    {
        $hits_rs = $this->requestManager->findRequestsWithContentObjectsByGuids($guids);

        foreach($hits_rs as $hit)
        {
            $hits[$hit->get_optional_property(Request::PROPERTY_GUID)] = $hit;
        }
        
        return $hits;
    }

    private function get_local_list($local)
    {
        /**
         *
         * @var Result $result
         */
        $html = array();
        $html[] = '<ol>';
        foreach ($local as $result)
        {
            $html[] = '<li>';
            $html[] = $this->define_percentage_colour($result->get_percentage()) . ' ' . $result->get_original_guid();
            $html[] = '</li>';
        }
        $html[] = '</ol>';
        
        return implode(PHP_EOL, $html);
    }

    private function get_internet_list($internet)
    {
        /**
         *
         * @var Result $result
         */
        $html = array();
        $html[] = '<ol>';
        foreach ($internet as $result)
        {
            $html[] = '<li>';
            $html[] = $this->define_percentage_colour($result->get_percentage()) . ' ' .
                 $this->generate_link($result->get_url());
            $html[] = '</li>';
        }
        $html[] = '</ol>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param Request $request
     */
    private function show_results($request)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Result::class, Result::PROPERTY_REQUEST_ID),
            new StaticConditionVariable($request->get_id()));
        $parameters = new DataClassRetrievesParameters($condition);
        $results = DataManager::retrieves(Result::class, $parameters);
        
        $html = array();
        while ($results && $result = $results->next_result())
        {
            $html[] = $this->show_result($result);
        }
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param Result $result
     */
    private function show_result($result)
    {
        $string = str_replace(array('<diff>', '</diff>'), '', $result->get_diff());
        $diff = new \DOMDocument();
        $diff->loadXML($string);
        
        $stylesheet = new \DOMDocument();
        $stylesheet->load($this->xslt_path . '/diff.xslt');
        
        $xslt = new \XSLTProcessor();
        $xslt->setParameter('sablotron', 'xslt_base_dir', $this->xslt_path);
        $xslt->setParameter('', 'original', addslashes(Translation::get('OriginalText')));
        $xslt->setParameter('', 'found', addslashes(Translation::get('FoundByEphorus')));
        $xslt->registerPHPFunctions();
        $xslt->importStylesheet($stylesheet);
        
        $html = array();
        $html[] = "<table width='100%' cellpadding='0' cellspacing='0'>";
        $html[] = "<td height='19' class='tableTop minFont' style='PADDING-LEFT: 4px; PADDING-TOP: 0px'>";
        
        if ($result->get_original_guid() == null)
        {
            $html[] = "<span class='report_table_header'>" . Translation::get(
                'UrlReport', 
                array("URL" => $result->get_url())) . "</span>";
        }
        else
        {
            $html[] = "<span class='report_table_header'>" . Translation::get(
                'LocalReport', 
                array("LOCAL_GUID" => $result->get_original_guid(), "SOURCE" => $result->get_student_name())) . "</span>";
        }
        
        $html[] = "</td>";
        $html[] = "<td height='19' class='minFont'>";
        $html[] = "&nbsp;";
        $html[] = "</td>";
        $html[] = "<tr>";
        $html[] = "<td class='tableTop' style='padding-top: 5px; padding-bottom: 5px; padding-right: 5px;'>";
        $html[] = $xslt->transformToXml($diff);
        $html[] = "&nbsp;";
        $html[] = "</td>";
        $html[] = "</tr>";
        $html[] = "<tr>";
        $html[] = "<td class='tableBottom'>";
        $html[] = "&nbsp;";
        $html[] = "</td>";
        $html[] = "</tr>";
        $html[] = "</table>";
        
        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service\RequestManager
     */
    public function getRequestManager()
    {
        return $this->getService('chamilo.application.weblcms.tool.implementation.ephorus.service.request_manager');
    }
}