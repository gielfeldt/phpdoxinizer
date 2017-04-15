<?php

namespace PhpDoxinizer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

class Controller
{
    public function buildAction($projectName, $branch, Application $app, Request $request)
    {
        $token = $request->query->get('token');
        $app['project_service']->authenticate($projectName, $token);
        $app['project_service']->build($projectName, $branch);
        return $app->json(['message' => 'ok']);
    }
}
