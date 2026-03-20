<?php

declare(strict_types=1);

namespace NSeo;

use Nette\Application\Routers\RouteList;

/**
 * Sitemap generator — produces XML sitemap for search engines.
 *
 * Usage:
 *   $sitemap = new SitemapGenerator('https://myshop.com');
 *   $sitemap->addUrl('/products', priority: 0.8, changeFreq: 'daily');
 *   $sitemap->addUrl('/about', lastMod: new \DateTime('2024-01-01'));
 *   echo $sitemap->generate();
 */
final class SitemapGenerator
{
	/** @var list<array{loc: string, lastmod: ?string, changefreq: ?string, priority: ?float}> */
	private array $urls = [];


	public function __construct(
		private readonly string $baseUrl,
	) {
	}


	/**
	 * Add a URL to the sitemap.
	 */
	public function addUrl(
		string $path,
		?\DateTimeInterface $lastMod = null,
		?string $changeFreq = null,
		?float $priority = null,
	): self {
		$this->urls[] = [
			'loc' => rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/'),
			'lastmod' => $lastMod?->format('Y-m-d'),
			'changefreq' => $changeFreq,
			'priority' => $priority,
		];
		return $this;
	}


	/**
	 * Generate XML sitemap string.
	 */
	public function generate(): string
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		foreach ($this->urls as $url) {
			$xml .= "  <url>\n";
			$xml .= '    <loc>' . htmlspecialchars($url['loc']) . "</loc>\n";

			if ($url['lastmod'] !== null) {
				$xml .= '    <lastmod>' . $url['lastmod'] . "</lastmod>\n";
			}
			if ($url['changefreq'] !== null) {
				$xml .= '    <changefreq>' . $url['changefreq'] . "</changefreq>\n";
			}
			if ($url['priority'] !== null) {
				$xml .= '    <priority>' . number_format($url['priority'], 1) . "</priority>\n";
			}

			$xml .= "  </url>\n";
		}

		$xml .= "</urlset>\n";
		return $xml;
	}


	/**
	 * Get count of URLs in the sitemap.
	 */
	public function count(): int
	{
		return count($this->urls);
	}
}
