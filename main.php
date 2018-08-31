<?php

/**
 * Прогоняет входной HTML через веб-сервис Типографа.
 */

$inputFile = "./sample.html";
$outputFile = "./out.html";

//-------------------------------

$htmlIn = file_get_contents($inputFile);
$htmlIn = "<div>$htmlIn</div>";

$doc = new DOMDocument();
$doc->loadHTML("<?xml encoding=\"utf-8\" ?>" . $htmlIn, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
$xpath = new DOMXPath($doc);
$nodeList = $xpath->query("//text()[string-length(normalize-space(.))>0]");

require "remotetypograf.php";

$remoteTypograf = new RemoteTypograf();

$remoteTypograf->htmlEntities();
$remoteTypograf->br (false);
$remoteTypograf->p (false);
$remoteTypograf->nobr (3);
$remoteTypograf->quotA ('laquo raquo');
$remoteTypograf->quotB ('bdquo ldquo');

for ($i = 0; $i < $nodeList->count(); $i++) {
  $item = $nodeList->item($i);
  $item->textContent = $remoteTypograf->processText ($item->textContent);
}

$htmlOut = $doc->saveXML($doc->documentElement);
$htmlOut = html_entity_decode(trim(mb_substr($htmlOut, mb_strlen("<div>"), mb_strlen($htmlOut) - mb_strlen("<div></div>") - 1)));
file_put_contents($outputFile, $htmlOut);