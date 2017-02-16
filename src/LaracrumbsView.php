<?php

namespace dees040\Laracrumbs;

use Illuminate\Contracts\View\Factory as View;

class LaracrumbsView
{
    /**
     * The View Factory instance
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    private $factory;

    /**
     * The view to generate.
     *
     * @var string
     */
    private $view = 'laracrumbs::laracrumbs';

    /**
     * LaracrumbsView constructor.
     *
     * @param  \Illuminate\Contracts\View\Factory  $factory
     */
    public function __construct(View $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Generate and render the view.
     *
     * @param  array  $crumbs
     * @return string
     */
    public function render($crumbs)
    {
        return $this->factory->make($this->view, compact('crumbs'))->render();
    }

    /**
     * Set the view to render.
     *
     * @param  string  $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }
}