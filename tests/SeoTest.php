<?php

declare(strict_types=1);

use Tester\Assert;
use NSeo\SeoManager;
use NSeo\SitemapGenerator;

require __DIR__ . '/../../../vendor/autoload.php';
Tester\Environment::setup();

// SeoManager tests
$seo = new SeoManager();
$seo->setTitle('Test Page');
$seo->setDescription('This is a test description for SEO purposes');
$seo->setCanonical('https://example.com/test');
$seo->setSiteName('TestShop');
$seo->setOg('image', 'https://example.com/img.jpg');
$seo->addJsonLd(['@type' => 'WebPage', 'name' => 'Test']);

$html = $seo->renderHead();
Assert::contains('<title>Test Page</title>', $html);
Assert::contains('name="description"', $html);
Assert::contains('rel="canonical"', $html);
Assert::contains('og:title', $html);
Assert::contains('og:image', $html);
Assert::contains('og:site_name', $html);
Assert::contains('twitter:card', $html);
Assert::contains('application/ld+json', $html);
Assert::contains('"@type": "WebPage"', $html);

// Test reset
$seo->reset();
Assert::same('', $seo->getTitle());
Assert::same('', $seo->getDescription());

// SitemapGenerator tests
$sitemap = new SitemapGenerator('https://example.com');
$sitemap->addUrl('/products', priority: 0.8, changeFreq: 'daily');
$sitemap->addUrl('/about', lastMod: new DateTime('2024-06-15'));
$sitemap->addUrl('/contact');

$xml = $sitemap->generate();
Assert::contains('<urlset xmlns', $xml);
Assert::contains('https://example.com/products', $xml);
Assert::contains('<priority>0.8</priority>', $xml);
Assert::contains('<changefreq>daily</changefreq>', $xml);
Assert::contains('2024-06-15', $xml);
Assert::same(3, $sitemap->count());

echo "NSeo: ALL TESTS PASSED\n";
