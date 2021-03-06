<?php


class NoHashServiceProvider extends Illuminate\Support\ServiceProvider {

	/**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app['hash'] = $this->app->share(function () {
            return new NoHasher();
        });
    }

     /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return array('hash');
    }

}