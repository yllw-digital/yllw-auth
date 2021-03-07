<?php

namespace YllwDigital\YllwAuth\Tests;

use YllwDigital\YllwAuth\YllwAuthServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase {
  	public function setUp(): void {
		parent::setUp();

		$this->loadLaravelMigrations();
		$this->artisan('migrate', ['--database' => 'testbench'])->run();
	}

  	protected function getPackageProviders($app) {
		return [
      		YllwAuthServiceProvider::class,
		];
  	}

	protected function getEnvironmentSetUp($app) {
		$app['config']->set('database.default', 'testbench');
		$app['config']->set('database.connections.testbench', [
			'driver'   => 'sqlite',
			'database' => ':memory:',
			'prefix'   => '',
		]);
	}
}