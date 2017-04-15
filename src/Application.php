<?php

namespace PhpDoxinizer;

class Application extends \Silex\Application
{
    public function boot()
    {
        $this->registerServices();
        $this->registerRoutes();

        parent::boot();
    }

    public function registerServices()
    {
        $this['project_service'] = new ProjectService();
    }

    public function registerRoutes()
    {
        $this->match('/api/build/{projectName}/{branch}', Controller::class . '::buildAction');
    }
}
