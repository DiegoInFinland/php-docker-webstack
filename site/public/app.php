<?php

use Twig\TwigFunction;
use Twig\Error\LoaderError;

use PhpUnitsOfMeasure\PhysicalQuantity\Length;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/config.php';


$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'cache' =>  false,
]);

$section = $_GET['section'] ?? 'start';
$result = null;
$unit = null;
$feed = null;
$feed_w = null;
$error = null;
$content = '';
$title = 'Apps';


// value: meter, converted_value: feets
function output_results($value, $converted_value)
{
    if (isset($value)) {
        $meter = new Length($value, 'm');
        return round($meter->toUnit($converted_value), 2);
    }
}

$twig->addFunction(new TwigFunction('output_results', 'output_results'));


switch ($section) {
    case 'check_security':
        try {
            $rss = new readFeed('https://www.kyberturvallisuuskeskus.fi/sites/default/files/rss/vulns.xml');
            $feed = $rss->getFeed();
        } catch (Exception $e) {
            $error = "Something went wrong";
            error_log($e->getMessage());
        }
        break;

    case 'weather':
        try {
            $rss = new readFeed('https://alerts.fmi.fi/cap/feed/rss_en-GB.rss');
            $feed = $rss->getFeed();
        } catch (Exception $e) {
            $error = "Something went wrong";
            error_log($e->getMessage());
        }
        break;

    case 'news':
        try {
            // TODO
            // to load news from more than a source an array() is needed. Then loop it.
            //$rss = new readFeed('https://rss.dw.com/rdf/rss-en-all');
            $rss = new readFeed('https://feeds.washingtonpost.com/rss/world');
            $feed = $rss->getFeed();
        } catch (Exception $e) {
            $error = "Something went wrong";
            error_log($e->getMessage());
        }
        break;

    case 'converter':
        $value = $_POST['value'] ?? null;
        $unit = $_POST['to_unit'] ?? null;

        $result = output_results($value, $unit);
        break;

    default:
        http_response_code(404);
        $content = 'Page not found';
        break;
}

try {
    echo $twig->render(
        'app.twig',
        [
            'title' => $title,
            'result' => $result,
            'unit' => $unit,
            'feed' => $feed,
            'error' => $error,
            'section' => $section,
            'content' => $content,
        ]
    );
} catch (LoaderError $e) {
    echo 'Something went wrong...';
}
