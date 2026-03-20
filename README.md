# NSeo

SEO manager for Nette Framework — meta tags, Open Graph, canonical URLs, and JSON-LD structured data.

## Features

- 🏷️ **Meta Tags** — Title, description, robots
- 🔗 **Canonical URLs** — Prevent duplicate content
- 📱 **Open Graph** — Facebook/Twitter card support
- 📊 **JSON-LD** — Structured data (Product, Article, WebPage, CollectionPage)
- 🗺️ **Site Name** — Global site name prefix

## Installation

```bash
composer require jansuchanek/nseo
```

## Configuration

```neon
extensions:
    seo: NSeo\DI\NSeoExtension
```

## Usage

In your presenter:

```php
#[Inject]
public SeoManager $seoManager;

public function renderDetail(string $slug): void
{
    $this->seoManager->setTitle('Product Name');
    $this->seoManager->setDescription('Product description');
    $this->seoManager->setCanonical($this->link('//detail', ['slug' => $slug]));
    $this->seoManager->setOg('type', 'product');

    // JSON-LD
    $this->seoManager->addJsonLd([
        '@type' => 'Product',
        'name' => 'Product Name',
        'offers' => ['@type' => 'Offer', 'price' => '299', 'priceCurrency' => 'CZK'],
    ]);
}
```

In your Latte `<head>`:

```latte
{$seoManager->renderHead()|noescape}
```

## Requirements

- PHP >= 8.2
- Nette DI ^3.2
- Latte ^3.1

## License

MIT
