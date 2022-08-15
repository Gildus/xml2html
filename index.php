<?php

use App\InvoiceDom;

require_once 'vendor/autoload.php';

$xml = file_get_contents('xml/FILE.XML');

$doc = new DOMDocument;
$doc->loadxml($xml);
$rootNamespace = $doc->documentElement->namespaceURI;

$xpath = new DOMXPath($doc);
$xpath->registerNamespace('xt', $rootNamespace);

$data = (new InvoiceDom($xpath))->parseDocument();
header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);

//$templates = new \League\Plates\Engine('src/Views');

//echo $templates->render('invoice', ['doc' => (new InvoiceDom($xpath))->parseDocument()]);