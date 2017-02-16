<?php

namespace dees040\Laracrumbs;

use Illuminate\Contracts\Support\Arrayable;
use App\Library\Laracrumbs\Exceptions\BreadcrumbDoesNotExists;

class LaracrumbsGenerator implements Arrayable
{
    /**
     * A list of all the breadcrumbs created by the developer.
     *
     * @var array
     */
    protected $crumbs = [];

    /**
     * A list of all the generated breadcrumbs.
     *
     * @var array
     */
    protected $generated = [];

    /**
     * Handle the breadcrumbs generation.
     *
     * @param  string  $name
     * @param  array  $params
     * @param  array  $crumbs
     * @return array
     */
    public function handle($name, $params, $crumbs)
    {
        $this->crumbs = $crumbs;

        $this->call($name, $params);

        return $this->setLastCrumbToTrue($this->toArray());
    }

    /**
     * Call the breadcrumb callback.
     *
     * @param  string  $name
     * @param  array  $params
     * @throws \dees040\Laracrumbs\Exceptions\BreadcrumbDoesNotExists
     */
    private function call($name, $params)
    {
        if (! $this->breadcrumbExists($name)) {
            throw new BreadcrumbDoesNotExists();
        }

        $this->addGeneratorToParameters($params);

        // Call the breadcrumb callback with the necessary parameters.
        call_user_func_array($this->crumbs[$name], $params);
    }

    /**
     * Push the breadcrumb to the generated breadcrumb list.
     *
     * @param  string  $title
     * @param  string  $url
     * @param  array  $data
     */
    public function push($title, $url, array $data = [])
    {
        $last = false;

        $this->generated[] = (object) array_merge(compact('title', 'url', 'last'), $data);
    }

    /**
     * Add parent to breadcrumb.
     *
     * @param  string  $name
     */
    public function parent($name)
    {
        $params = $this->getParametersFromParent(func_get_args());

        $this->call($name, $params);
    }

    /**
     * Determine is the breadcrumb exists.
     *
     * @param  string  $name
     * @return bool
     */
    private function breadcrumbExists($name)
    {
        return array_key_exists($name, $this->crumbs);
    }

    /**
     * Add the breadcrumb generator to the parameter list.
     *
     * @param  array  $params
     * @return void
     */
    private function addGeneratorToParameters(&$params)
    {
        array_unshift($params, $this);
    }

    /**
     * Get the parameter list from the parent call.
     *
     * @param  array  $params
     * @return array
     */
    private function getParametersFromParent($params)
    {
        // Remove the name from the parameters.
        $parameters = array_slice($params, 1);

        // We can return the first element of the array if it is
        // an array.
        if (is_array(head($parameters))) {
            return head($parameters);
        }

        return $parameters;
    }

    /**
     * Update the last breadcrumb.
     *
     * @param  array  $crumbs
     * @return array
     */
    private function setLastCrumbToTrue($crumbs)
    {
        $crumbs[count($crumbs) - 1]->last = true;

        return $crumbs;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->generated;
    }
}