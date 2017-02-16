<?php

namespace dees040\Laracrumbs;

use App\Library\Laracrumbs\Exceptions\BreadcrumbAlreadyExists;
use App\Library\Laracrumbs\Exceptions\BreadcrumbDoesNotExists;

class Laracrumbs
{
    /**
     * List of all breadcrumbs.
     *
     * @var array
     */
    protected $crumbs = [];

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $router;

    /**
     * The generator instance.
     *
     * @var \dees040\Laracrumbs\LaracrumbsGenerator
     */
    private $generator;

    /**
     * The view generator instance.
     *
     * @var \dees040\Laracrumbs\LaracrumbsView
     */
    private $view;

    /**
     * Laracrumbs constructor.
     *
     * @param  \dees040\Laracrumbs\LaracrumbsRoute  $router
     * @param  \dees040\Laracrumbs\LaracrumbsGenerator  $generator
     * @param  \dees040\Laracrumbs\LaracrumbsView  $view
     */
    public function __construct(LaracrumbsRoute $router, LaracrumbsGenerator $generator, LaracrumbsView $view)
    {
        $this->view = $view;
        $this->router = $router;
        $this->generator = $generator;
    }

    /**
     * @param  string  $name
     * @param  \Closure|string  $callable
     */
    public function register($name, $callable)
    {
        $this->breadcrumbShouldByUnique($name);

        $this->crumbs[$name] = $callable;
    }

    /**
     * Generate the breadcrumbs list.
     *
     * @param  string  $name
     * @return array
     */
    public function generate($name = null)
    {
        list($name, $params) = $this->parseParameters(func_get_args());

        $this->breadcrumbShouldExists($name);

        return $this->generateBreadcrumbArray($name, $params);
    }

    /**
     * Render the breadcrumbs to HTML.
     *
     * @param  string  $name
     * @return string
     */
    public function render($name = null)
    {
        list($name, $params) = $this->parseParameters(func_get_args());

        $this->breadcrumbShouldExists($name);

        return $this->view->render($this->generateBreadcrumbArray($name, $params));
    }

    /**
     * Generate an array with breadcrumbs.
     *
     * @param  string  $name
     * @param  array  $params
     * @return array
     */
    private function generateBreadcrumbArray($name, $params)
    {
        return $this->generator->handle($name, $params, $this->crumbs);
    }

    /**
     * Determine if the breadcrumb is unique.
     *
     * @param  string $name
     * @throws \dees040\Laracrumbs\Exceptions\BreadcrumbDoesNotExists
     */
    private function breadcrumbShouldByUnique($name)
    {
        if (array_key_exists($name, $this->crumbs)) {
            throw new BreadcrumbDoesNotExists();
        }
    }

    /**
     * Determine if the breadcrumb exists.
     *
     * @param  string $name
     * @throws \dees040\Laracrumbs\Exceptions\BreadcrumbAlreadyExists
     */
    private function breadcrumbShouldExists($name)
    {
        if (! array_key_exists($name, $this->crumbs)) {
            throw new BreadcrumbAlreadyExists();
        }
    }

    /**
     * Get the correct parameters.
     *
     * @param  array  $parameters
     * @return array
     */
    private function parseParameters($parameters)
    {
        if (is_array(head($parameters)) || count($parameters) === 0) {
            $name = $this->router->get();
        } else {
            $name = head($parameters);
        }

        $data = array_slice($parameters, 1);

        if (is_array(head($data))) {
            $data = head($data);
        }

        return [$name, $data];
    }
}
