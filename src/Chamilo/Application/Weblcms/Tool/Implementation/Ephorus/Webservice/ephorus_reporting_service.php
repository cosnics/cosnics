<?php
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Result;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataManager;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

require_once realpath(__DIR__ . '/../../../../../../../') . '/vendor/autoload.php';

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get('chamilo.libraries.architecture.bootstrap.bootstrap')->setup();

// Create a new soap server
$wsdl = new wsdl(__DIR__ . '/ephorus_reporting_service.wsdl');
$server = new nusoap_server($wsdl);
$server->soap_defencoding = 'UTF-8';
$server->decode_utf8 = true;

function report()
{
    global $rawPostData;

    $dom_document = new DOMDocument();
    $dom_document->loadXML($rawPostData);
    $dom_xpath = new DOMXPath($dom_document);
    $dom_xpath->registerNamespace('report', 'http://reporting.ephorus.org/');

    $guid = $dom_xpath->query('//report:document_guid')->item(0)->nodeValue;
    if (! $guid)
    {
        return new soap_fault('SERVER', '', 'document_guid can not be empty');
    }

    $request = DataManager::retrieve_request_by_guid($guid);
    if ($request)
    {
        $request->set_percentage($dom_xpath->query('//report:document_percentage')->item(0)->nodeValue);
        $request->set_duplicate_original_guid($dom_xpath->query('//report:document_original_guid')->item(0)->nodeValue);
        $request->set_duplicate_student_name($dom_xpath->query('//report:duplicate_student_name')->item(0)->nodeValue);
        $request->set_duplicate_student_number(
            $dom_xpath->query('//report:duplicate_student_number')->item(0)->nodeValue);
        $request->set_duplicate_original_guid($dom_xpath->query('//report:duplicate_original_guid')->item(0)->nodeValue);
        $request->set_status($dom_xpath->query('//report:status')->item(0)->nodeValue);
        $request->set_status_description($dom_xpath->query('//report:status_description')->item(0)->nodeValue);

        $summary_element = $dom_xpath->query('//report:summary')->item(0);
        if ($summary_element)
        {
            $summary_xml = $summary_element->ownerDocument->saveXML($summary_element);
            $request->set_summary($summary_xml);
        }

        $request->set_summary($summary_xml);

        if (! $request->update())
        {
            return new soap_fault('SERVER', '', 'report could not be stored');
        }

        $result_elements = $dom_xpath->query('//report:results/report:result');
        foreach ($result_elements as $result_element)
        {
            $result = new Result();
            $result->set_request_id($request->get_id());
            $result->set_url($dom_xpath->query('.//report:url', $result_element)->item(0)->nodeValue);
            $result->set_mimetype($dom_xpath->query('.//report:mimetype', $result_element)->item(0)->nodeValue);
            $result->set_type($dom_xpath->query('.//report:type', $result_element)->item(0)->nodeValue);
            $result->set_percentage($dom_xpath->query('.//report:percent', $result_element)->item(0)->nodeValue);
            $result->set_original_guid(
                $dom_xpath->query('.//report:original_guid', $result_element)->item(0)->nodeValue);
            $result->set_student_number(
                $dom_xpath->query('.//report:student_number', $result_element)->item(0)->nodeValue);
            $result->set_student_name($dom_xpath->query('.//report:student_name', $result_element)->item(0)->nodeValue);

            $diff_element = $dom_xpath->query('.//report:diff', $result_element)->item(0);
            $diff_xml = $diff_element->ownerDocument->saveXML($diff_element);

            $result->set_diff($diff_xml);

            if (! $result->create())
            {
                $request->truncate_results();

                return new soap_fault('SERVER', '', 'results could not be stored');
            }
        }
    }
}

// Use the request to (try to) invoke the service
$rawPostData = file_get_contents("php://input");
$server->service($rawPostData);
