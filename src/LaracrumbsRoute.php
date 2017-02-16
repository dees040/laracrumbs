<?php

namespace dees040\Laracrumbs;

use Illuminate\Contracts\Routing\Registrar as Router;

class LaracrumbsRoute
{
    /**
     * The router instance.
     *
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * LaracrumbsRoute constructor.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Get the current route name.
     *
     * @return string
     */
    public function get()
    {
        $route = $this->router->current();

        if (is_null($route)) {
            return '';
        }

        $name = $route->getName();

        if (is_null($name)) {
            $this->routeNotNamed($route);
        }

        return $name;
    }

    /**
     * The current route has not been named. We will throw an error.
     *
     * @param  \Illuminate\Routing\Route $route
     * @throws \Exception
     */
    private function routeNotNamed($route)
    {
        $uri = head($route->methods()) . ' /' . $route->uri();

        throw new \Exception("The current route ($uri) is not named - please check routes.php for an \"as\" or \"name\" parameter");
    }
}