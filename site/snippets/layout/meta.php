<?php
use Kirby\Toolkit\Str;

// Everything in <head> for search engines and sharing: title, description, canonical, language
// versions, Open Graph (WhatsApp, Facebook, …), icons and structured data.
// $noindex: true on pages that must stay out of search results (donation, thank-you).
$isHome = $page->isHomePage();
$isUpdate = $page->intendedTemplate()->name() === 'update';
$indexable = !($noindex ?? false) && !$page->isErrorPage();
$siteTitle = $site->title()->value();
$title = $isHome ? $siteTitle : $page->title()->value() . ' | ' . $siteTitle;
$description = $page->description()->value()
    ?: ($isUpdate ? Str::excerpt((string) $page->text()->toBlocks(), 160) : '')
    ?: $site->description()->value();
$languages = $page->translatedLanguages();
$ogLocale = fn ($language) => strtok($language->locale(LC_ALL) ?? '', '.');

// Share image: the page's own, else a photo of the page, else the default from the settings.
$image = $page->content()->get('shareimage')->toFile()
    ?? $page->content()->get('photos')->toFiles()->first()
    ?? $page->content()->get('cover')->toFile()
    ?? $page->content()->get('hero_image')->toFile()
    ?? $site->content()->get('shareimage')->toFile();
$share = $image?->crop(1200, 630);

$logo = $site->logo()->toFile()?->url() ?? url('assets/images/logo.png');
$organisation = [
    '@type' => 'NGO',
    '@id' => $site->url() . '/#organisation',
    'name' => $site->organisation()->or($siteTitle)->value(),
    'url' => $site->url(),
    'logo' => $logo,
    'sameAs' => array_values(array_filter([$site->instagram()->value(), $site->instagramtess()->value()])),
    'address' => $site->organisationaddress()->value() ?: null,
    'taxID' => $site->organisationnumber()->value() ?: null,
    'email' => $site->contactemail()->value() ?: null,
];
$structured = match (true) {
    $isHome => [
        '@context' => 'https://schema.org',
        '@graph' => [
            array_filter($organisation),
            ['@type' => 'WebSite', 'url' => $site->url(), 'name' => $siteTitle, 'inLanguage' => $kirby->languageCode(), 'publisher' => ['@id' => $organisation['@id']]],
        ],
    ],
    $isUpdate => array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $page->title()->value(),
        'datePublished' => $page->date()->toDate('Y-m-d'),
        'image' => $share?->url(),
        'url' => $page->url(),
        'inLanguage' => $kirby->languageCode(),
        'author' => ['@id' => $organisation['@id'], '@type' => 'NGO', 'name' => $organisation['name']],
        'publisher' => ['@id' => $organisation['@id'], '@type' => 'NGO', 'name' => $organisation['name']],
    ]),
    default => null,
};
?>
    <title><?= esc($title) ?></title>
    <meta name="description" content="<?= esc($description, 'attr') ?>">
<?php if (!$indexable): ?>
    <meta name="robots" content="noindex">
<?php else: ?>
    <link rel="canonical" href="<?= $page->url() ?>">
<?php /* Only languages the page really exists in; untranslated addresses redirect to Dutch (plugins/tess-seo). */ ?>
<?php if ($languages->count() > 1): ?>
<?php foreach ($languages as $language): ?>
    <link rel="alternate" hreflang="<?= $language->code() ?>" href="<?= $page->url($language->code()) ?>">
<?php endforeach ?>
    <link rel="alternate" hreflang="x-default" href="<?= $page->url($kirby->defaultLanguage()->code()) ?>">
<?php endif ?>
<?php endif ?>
    <meta property="og:site_name" content="<?= esc($siteTitle, 'attr') ?>">
    <meta property="og:type" content="<?= $isUpdate ? 'article' : 'website' ?>">
    <meta property="og:title" content="<?= esc($isHome ? $siteTitle : $page->title()->value(), 'attr') ?>">
    <meta property="og:description" content="<?= esc($description, 'attr') ?>">
    <meta property="og:url" content="<?= $page->url() ?>">
    <meta property="og:locale" content="<?= $ogLocale($kirby->language()) ?>">
<?php foreach ($languages as $language): ?><?php if ($language->code() !== $kirby->languageCode()): ?>
    <meta property="og:locale:alternate" content="<?= $ogLocale($language) ?>">
<?php endif ?><?php endforeach ?>
<?php if ($share): ?>
    <meta property="og:image" content="<?= $share->url() ?>">
    <meta property="og:image:width" content="<?= $share->width() ?>">
    <meta property="og:image:height" content="<?= $share->height() ?>">
    <meta property="og:image:alt" content="<?= $image->alt()->or($page->title())->esc('attr') ?>">
<?php endif ?>
    <meta name="twitter:card" content="<?= $share ? 'summary_large_image' : 'summary' ?>">
    <meta name="theme-color" content="#F5D7E6">
    <link rel="icon" href="<?= url('favicon.ico') ?>" sizes="32x32">
    <link rel="icon" href="<?= url('assets/images/favicon.svg') ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="<?= url('assets/images/apple-touch-icon.png') ?>">
    <link rel="preload" href="<?= url('assets/fonts/montserrat-latin.woff2') ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= url('assets/fonts/caveat-latin.woff2') ?>" as="font" type="font/woff2" crossorigin>
<?php if ($structured): ?>
    <script type="application/ld+json"><?= json_encode($structured, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<?php endif ?>
