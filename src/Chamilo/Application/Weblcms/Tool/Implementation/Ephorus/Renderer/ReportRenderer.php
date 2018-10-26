<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Renderer;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service\RequestManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Result;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Translation\Translation;

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
        return $this->twigRenderer->render(
            'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus:EphorusReport.html.twig',
            $this->getRequestParameters($request)
        );
    }

    /**
     * @param Request $request
     *
     * @return string[]
     */
    protected function getRequestParameters($request)
    {
        $results = $this->requestManager->findResultsForRequest($request);
        $chamiloResults = $localResults = $internetResults = $resultParameters = [];

        foreach ($results as $result)
        {
            $resultParameters[] = $this->getResultParameters($result);

            if ($result->get_original_guid() === null)
            {
                $internetResults[$result->get_url()] = $result;
                continue;
            }

            if (strlen($result->get_student_number()) == 0)
            {
                $localResults[$result->get_original_guid()] = $result;
                continue;
            }

            $chamiloResults[$result->get_original_guid()] = $result;
        }

        return [
            'CHAMILO_RESULTS' => $this->getChamiloResultsParameters($chamiloResults),
            'LOCAL_RESULTS' => $localResults,
            'INTERNET_RESULTS' => $internetResults,
            'RESULTS' => $resultParameters,
            'REQUEST' => $request,
            'REQUEST_USER_NAME' => $this->userService->getUserFullNameByIdentifier(
                $request->get_request_user_id()
            ),
            'AUTHOR' => $this->userService->getUserFullNameByIdentifier(
                $request->get_author_id()
            )
        ];
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
            $resultListParameters[] = [
                'RESULT_OBJECT' => $result,
                'ORIGINAL_OBJECT' => $hits[$result->get_original_guid()]
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

        $resultParameters = [
            'RESULT_OBJECT' => $result,
            'DIFF' => $xslt->transformToXml($diff)
        ];

        return $resultParameters;
    }
}