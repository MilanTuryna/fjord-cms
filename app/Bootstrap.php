<?php

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;


class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator;
		$appDir = dirname(__DIR__);

        define('TEMPLATE_COMMON', __DIR__ . '\Presenters\@Common');

		$configurator->enableTracy($appDir . '/log');
		$configurator->setDebugMode(true);

		$configurator->setTempDirectory($appDir . '/temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator->addConfig($appDir . '/config/common.neon');
		$configurator->addConfig($appDir . '/config/services.neon');
		$configurator->addConfig($appDir . '/config/local.neon');
        error_reporting( E_ALL & ~E_DEPRECATED &  ~E_USER_DEPRECATED);
		return $configurator;
	}
}
