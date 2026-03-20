<?php

declare(strict_types=1);

namespace NSeo\DI;

use NSeo\SeoManager;
use NSeo\SitemapGenerator;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

/**
 * Nette DI Extension for NSeo.
 *
 * Config:
 *   seo:
 *       siteName: 'MyShop'
 *       baseUrl: 'https://myshop.com'
 */
final class NSeoExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'siteName' => Expect::string(''),
			'baseUrl' => Expect::string(''),
		]);
	}


	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		/** @var \stdClass $config */
		$config = $this->config;

		$seo = $builder->addDefinition($this->prefix('manager'))
			->setFactory(SeoManager::class);

		if ($config->siteName !== '') {
			$seo->addSetup('setSiteName', [$config->siteName]);
		}

		if ($config->baseUrl !== '') {
			$builder->addDefinition($this->prefix('sitemap'))
				->setFactory(SitemapGenerator::class, [$config->baseUrl]);
		}
	}
}
