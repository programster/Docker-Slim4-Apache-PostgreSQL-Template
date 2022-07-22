<?php


require_once(__DIR__ . '/../bootstrap.php');

// Manually include/require any classes here that may potentially be stored in the session.
//require_once(__DIR__ . '/../models/MyModel.php');

// start the session here instead of bootstrap, because sessions only apply to web, not scripts.
session_start();


$app = Slim\Factory\AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addMiddleware(new MiddlewareAuth());
$app->addRoutingMiddleware();
$app->addMiddleware(new MiddlewareTrailingSlash()); // this must be last (which means it executes first).

// register the error middleware. This must be registered last so that it gets executed first.
$errorMiddleware = $app->addErrorMiddleware(
    displayErrorDetails: (ENVIRONMENT === "dev" || ENVIRONMENT === "staging"),
    logErrors: true,
    logErrorDetails: true
);

// Set the error middlewares 404 handler
$errorMiddleware->setErrorHandler(\Slim\Exception\HttpNotFoundException::class, function (
    \Psr\Http\Message\ServerRequestInterface $request,
    \Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) {
    $response = new \Slim\Psr7\Response();
    $body = new View404(); // change this View404 file to your custom 404 page.
    $response->getBody()->write($view->render());
    return $response->withStatus(404);
});

// Register all of your controllers here. Preferably in alphabetical order.
HomeController::registerRoutes($app);

$app->run();
