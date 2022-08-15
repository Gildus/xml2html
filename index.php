<?php

use App\InvoiceDom;

require_once 'vendor/autoload.php';

$xml = file_get_contents('xml/FACTURAE001-31420115672232.XML');

$doc = new DOMDocument;
$doc->loadxml($xml);
$rootNamespace = $doc->documentElement->namespaceURI;

$xpath = new DOMXPath($doc);
$xpath->registerNamespace('xt', $rootNamespace);

$templates = new \League\Plates\Engine('src/Views');

echo $templates->render('invoice', ['doc' => (new InvoiceDom($xpath))->parseDocument()]);