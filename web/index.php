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

    return $twig;
}));

/** Default route. */
$app->get('/', function() use ($app) {
  return $app['twig']->render('pages/index.twig', array('bodyclass' => 'index'));
})->bind('index');

/** Run! */
$app->run();
