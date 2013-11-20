<?php

/**
 * (c) 2013 Telemundo Digital Media
 *
 * For the full copyright and license information, please view
 * the license file that was distributed with the source code.
 */

use Symfony\Component\HttpFoundation\Response;

require_once '../vendor/autoload.php';

/** Create the application. */
$app = new Silex\Application();
$app['debug'] = true;

/** Enable the Twig providers. */
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path' => __DIR__.'/../app/views'
));

/** Add custom Twig filters. */
$app['twig'] = $app->share($app->extend('twig', function($twig) {
    $twig->addFilter(new \Twig_SimpleFilter('class', function($object) {
        return get_class($object);
    }));
    $twig->addFilter(new \Twig_SimpleFilter('dump', function($object) {
        var_dump($object);
    }));
    $twig->addFilter(new \Twig_SimpleFilter('image', function($dir, $image) {
      if (stristr($dir, 'http://')) {
        return sprintf('%s404', $dir);
      } else {
        $imgdir = sprintf('%s/%s', trim($dir, '/'), $image);
        return sprintf('assets/%s', trim($imgdir, '/'));
      }
    }));
    $twig->addFilter(new \Twig_SimpleFilter('header', function($string) {
        return ucwords(str_replace('_', ' ', $string));
    }));

    return $twig;
}));

/** Default route. */
$app->get('/', function() use ($app) {
  $jsonfile = __DIR__ . '/assets/sitemap.json';
  if (file_exists($jsonfile)) {
    $jsondata = json_decode(file_get_contents($jsonfile));
    $records = array();
    foreach ($jsondata as $record) {
      $section = !empty($record->section) ? $record->section : 'homepage';
      if (!isset($records[$section])) {
        $records[$section] = array();
      }
      $records[$section][] = $record;
    }
  }

  return $app['twig']->render('pages/index.twig', array('records' => $records));
})->bind('index');

/** Dump sitemap route. */
$app->get('/dump', function() use ($app) {
  $jsonfile = __DIR__ . '/assets/sitemap.json';
  if (file_exists($jsonfile)) {
    $jsondata = json_decode(file_get_contents($jsonfile));
    $records = array();
    foreach ($jsondata as $record) {
      $section = !empty($record->section) ? $record->section : 'homepage';
      if (!isset($records[$section])) {
        $records[$section] = array();
      }
      $records[$section][] = $record;
    }
  }

  return $app['twig']->render('pages/dump.twig', array('records' => $records, 'root' => 'http://msnlatino.telemundo.com/'));
})->bind('dump');

/** Run! */
$app->run();

# vi: set ts=2 sw=2 :
