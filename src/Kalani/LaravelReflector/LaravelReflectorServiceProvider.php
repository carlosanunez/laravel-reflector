<?php namespace Kalani\LaravelReflector;

use Illuminate\Support\ServiceProvider;

class LaravelReflectorServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['laravel-reflector'] = $this->app->share(function($app){
			return new LaravelReflector($app, $app->make('config'));
		});

		$this->commands('laravel-reflector');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('LaravelReflector');
	}

}