<?php

declare(strict_types=1);

namespace NSeo;

/**
 * SEO meta tag manager for Nette.
 *
 * Collects meta tags, Open Graph, JSON-LD and renders them in <head>.
 *
 * Usage:
 *   $seo = $this->seoManager;
 *   $seo->setTitle('Product Name — MyShop');
 *   $seo->setDescription('Best product ever');
 *   $seo->setCanonical('https://myshop.com/product/123');
 *   $seo->setOg('image', 'https://myshop.com/img/product.jpg');
 *   $seo->addJsonLd(['@type' => 'Product', 'name' => 'Widget']);
 *   // In template: {$seo->renderHead()|noescape}
 */
final class SeoManager
{
	private string $title = '';
	private string $description = '';
	private string $canonical = '';
	private string $robots = 'index, follow';
	private string $siteName = '';

	/** @var array<string, string> */
	private array $og = [];

	/** @var array<string, string> */
	private array $twitter = [];

	/** @var array<string, string> */
	private array $meta = [];

	/** @var list<array<string, mixed>> */
	private array $jsonLd = [];


	public function setTitle(string $title): self
	{
		$this->title = $title;
		return $this;
	}

	public function getTitle(): string { return $this->title; }

	public function setDescription(string $desc): self
	{
		$this->description = mb_substr(strip_tags($desc), 0, 160);
		return $this;
	}

	public function getDescription(): string { return $this->description; }

	public function setCanonical(string $url): self
	{
		$this->canonical = $url;
		return $this;
	}

	public function setRobots(string $robots): self
	{
		$this->robots = $robots;
		return $this;
	}

	public function setSiteName(string $name): self
	{
		$this->siteName = $name;
		return $this;
	}

	public function setOg(string $property, string $content): self
	{
		$this->og[$property] = $content;
		return $this;
	}

	public function setTwitter(string $name, string $content): self
	{
		$this->twitter[$name] = $content;
		return $this;
	}

	public function setMeta(string $name, string $content): self
	{
		$this->meta[$name] = $content;
		return $this;
	}

	/**
	 * Add JSON-LD structured data block.
	 * @param array<string, mixed> $data
	 */
	public function addJsonLd(array $data): self
	{
		if (!isset($data['@context'])) {
			$data = ['@context' => 'https://schema.org'] + $data;
		}
		$this->jsonLd[] = $data;
		return $this;
	}


	/**
	 * Render all SEO tags as HTML for <head>.
	 */
	public function renderHead(): string
	{
		$lines = [];

		if ($this->title !== '') {
			$lines[] = '<title>' . htmlspecialchars($this->title) . '</title>';
		}

		if ($this->description !== '') {
			$lines[] = '<meta name="description" content="' . htmlspecialchars($this->description) . '">';
		}

		if ($this->canonical !== '') {
			$lines[] = '<link rel="canonical" href="' . htmlspecialchars($this->canonical) . '">';
		}

		$lines[] = '<meta name="robots" content="' . htmlspecialchars($this->robots) . '">';

		// Custom meta
		foreach ($this->meta as $name => $content) {
			$lines[] = '<meta name="' . htmlspecialchars($name) . '" content="' . htmlspecialchars($content) . '">';
		}

		// Open Graph
		if ($this->title !== '' && !isset($this->og['title'])) {
			$this->og['title'] = $this->title;
		}
		if ($this->description !== '' && !isset($this->og['description'])) {
			$this->og['description'] = $this->description;
		}
		if ($this->siteName !== '' && !isset($this->og['site_name'])) {
			$this->og['site_name'] = $this->siteName;
		}
		foreach ($this->og as $prop => $content) {
			$lines[] = '<meta property="og:' . htmlspecialchars($prop) . '" content="' . htmlspecialchars($content) . '">';
		}

		// Twitter
		if (!isset($this->twitter['card'])) {
			$this->twitter = ['card' => 'summary_large_image'] + $this->twitter;
		}
		foreach ($this->twitter as $name => $content) {
			$lines[] = '<meta name="twitter:' . htmlspecialchars($name) . '" content="' . htmlspecialchars($content) . '">';
		}

		// JSON-LD
		foreach ($this->jsonLd as $data) {
			$lines[] = '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
		}

		return implode("\n", $lines);
	}


	/**
	 * Reset all values (useful between requests).
	 */
	public function reset(): void
	{
		$this->title = '';
		$this->description = '';
		$this->canonical = '';
		$this->robots = 'index, follow';
		$this->og = [];
		$this->twitter = [];
		$this->meta = [];
		$this->jsonLd = [];
	}
}
