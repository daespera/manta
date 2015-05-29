<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MantaServiceProvider extends ServiceProvider
{

    public function register()
    {

    	$this->app->singleton('App\Contracts\ConfigReaderContract', 'App\Contracts\Implementations\ConfigReader\Local');
    	$this->app->singleton('App\Contracts\ProcessorContract', 'App\Contracts\Implementations\Processor\Version1');
        $this->app->singleton('App\Contracts\YashaContract', 'App\Contracts\Implementations\Yasha\Local');
        $this->app->singleton('App\Contracts\PreprocessorContract', 'App\Contracts\Implementations\Preprocessor\Core');
        $this->app->singleton('App\Contracts\SyncContract', 'App\Contracts\Implementations\Sync\Cache');

		$this->app->singleton('Es\Client', function($app) {

			return new \Elasticsearch\Client([
				'hosts' => explode(',', env('ES_HOSTS'))
			]);

		});

    }

}
