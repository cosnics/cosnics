<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Renderer;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service\RequestManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Result;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Class that converts the database result to html
 *
 * @author Anthony Hurst (Hogeschool Gent) Pieterjan Broekaert Hogent
 */
class ReportRenderer
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
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * @var \Twig_Environment
     */
    protected $twigRenderer;

    /**
     * ResultToHtmlConverter constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service\RequestManager $requestManager
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Twig_Environment $twigRenderer
     */
    public function __construct(
        RequestManager $requestManager, UserService $userService, \Twig_Environment $twigRenderer
    )
    {
        $this->requestManager = $requestManager;
        $this->userService = $userService;
        $this->twigRenderer = $twigRenderer;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request $request
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderRequestReport(Request $request)
    {
        $results = $this->requestManager->findResultsForRequest($request);

        $resultParameters = [];
        foreach ($results as $result)
        {
            $resultParameters[] = $this->getResultParameters($result);
        }

        $parameters = $this->getSummaryParameters($request, $results);
        $parameters['RESULTS'] = $resultParameters;

        return $this->twigRenderer->render(
            'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus:EphorusReport.html.twig',
            $parameters
        );
    }

    protected function format_date($date)
    {
        return DatetimeUtilities::format_locale_date(
            Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
            $date
        );
    }

    /**
     *
     * @param Request $request
     * @param Result[] $results
     *
     * @return array
     */
    protected function getSummaryParameters($request, $results)
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
        $details_parameters['REQUESTERNAME'] = $this->userService->getUserFullNameById(
            $request->get_request_user_id()
        );

        // $request->get_request_user_id(); // Transform into Foreign property.
        $details_parameters['AUTHOR'] = $request->get_author()->get_fullname();
        $details_parameters['GUID'] = $request->get_guid();
        $details_parameters['PERCENTAGE'] = $request->get_percentage();
        $details_parameters['NUMBERSOURCES'] = count($chamilo) + count($local) + count($internet);

        $summaryParameters = [
            'REQUEST_DETAILS' => Translation::get('RequestDetails', $details_parameters),
            'CHAMILO_LIST' => $this->getChamiloResultsParameters($chamilo),
            'LOCAL_LIST' => $this->getLocalResultsParameters($local),
            'INTERNET_LIST' => $this->getInternetResultsParameters($internet)
        ];

        return $summaryParameters;
    }

    /**
     * @param Result[] $chamiloResults
     *
     * @return array
     */
    protected function getChamiloResultsParameters($chamiloResults)
    {
        $resultListParameters = [];

        $hits = $this->getRequestsFromChamiloByGuids(array_keys($chamiloResults));
        foreach ($chamiloResults as $result)
        {
            $hit = $hits[$result->get_original_guid()];
            $detals_parameters = array();

            if ($hit)
            {

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
                        'LOCAL_GUID' => $result->get_original_guid(),
                        'SOURCE' => $result->get_student_name()
                    )
                );
            }

            $resultListParameters[] = [
                'PERCENTAGE' => $result->get_percentage(),
                'DESCRIPTION' => $translation
            ];
        }

        return $resultListParameters;
    }

    /**
     *
     * @param int[] $guids
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject[]
     */
    protected function getRequestsFromChamiloByGuids($guids)
    {
        $hits_rs = $this->requestManager->findRequestsWithContentObjectsByGuids($guids);

        foreach ($hits_rs as $hit)
        {
            $hits[$hit->get_optional_property(Request::PROPERTY_GUID)] = $hit;
        }

        return $hits;
    }

    /**
     * @param Result[] $localResults
     *
     * @return array|string
     */
    protected function getLocalResultsParameters($localResults)
    {
        $resultListParameters = [];

        foreach ($localResults as $result)
        {
            $resultListParameters[] = [
                'PERCENTAGE' => $result->get_percentage(),
                'GUID' => $result->get_original_guid()
            ];
        }

        return $resultListParameters;
    }

    /**
     * @param Result[] $internetResults
     *
     * @return array
     */
    protected function getInternetResultsParameters($internetResults)
    {
        $resultListParameters = [];

        foreach ($internetResults as $result)
        {
            $resultListParameters[] = [
                'PERCENTAGE' => $result->get_percentage(),
                'URL' => $result->get_url()
            ];
        }

        return $resultListParameters;
    }

    /**
     *
     * @param Result $result
     *
     * @return array
     */
    protected function getResultParameters(Result $result)
    {
        $this->xslt_path = realpath(__DIR__ . '/../Resources/Xslt');

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

        if ($result->get_original_guid() == null)
        {
            $reportTitle = "<span class='report_table_header'>" . Translation::get(
                    'UrlReport',
                    array("URL" => $result->get_url())
                ) . "</span>";
        }
        else
        {
            $reportTitle = "<span class='report_table_header'>" . Translation::get(
                    'LocalReport',
                    array("LOCAL_GUID" => $result->get_original_guid(), "SOURCE" => $result->get_student_name())
                ) . "</span>";
        }

        $resultParameters = [
            'RESULT_TITLE' => $reportTitle,
            'DIFF' => $xslt->transformToXml($diff)
        ];

        return $resultParameters;
    }
}