<!doctype html>
<html lang="<?= $kirby->languageCode() ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $page->title()->esc() ?> | <?= $site->title()->esc() ?></title>
    <meta name="description" content="<?= $page->description()->or($site->description())->esc() ?>">
    <?php /* Version the assets by modification time so browsers never keep a stale copy after a rebuild. */ ?>
    <?= css(url('assets/css/site.css') . '?v=' . filemtime($kirby->root('index') . '/assets/css/site.css')) ?><?= js(url('assets/js/site.js') . '?v=' . filemtime($kirby->root('index') . '/assets/js/site.js'), ['defer' => true]) ?>
</head>

<body class="site-design <?= $page->isHomePage() ? 'home-design ' : '' ?>bg-paper font-sans text-base leading-[1.65] text-ink">
    <a class="fixed -top-[100px] left-5 z-50 bg-white p-3 text-pink-dark focus:top-[10px]"
        href="#main"><?= t('skip-link') ?></a>
    <header data-site-header
        class="site-header sticky top-0 z-40 text-ink [--nav-accent:var(--color-pink)]">
        <div
            class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)] flex min-h-[76px] flex-wrap items-center justify-between gap-3 py-[14px] md:min-h-[90px] md:flex-nowrap md:gap-[30px] md:py-0">
            <a class="site-logo shrink-0 leading-none [&_img]:h-auto [&_img]:w-auto [&_img]:max-h-16 [&_img]:max-w-[min(164px,42vw)] md:[&_img]:max-w-[164px] [&_img]:[filter:drop-shadow(0_0_.7px_#27222A)_drop-shadow(0_0_.7px_#27222A)]"
                href="<?= $site->url() ?>" aria-label="<?= t('nav.home-label') ?>">
                <?php if ($logo = $site->logo()->toFile()): ?><img src="<?= $logo->url() ?>"
                        alt="<?= $site->title()->esc() ?>">
                <?php else: ?><img src="<?= url('assets/images/logo.png') ?>" width="2008" height="783"
                        alt="<?= $site->title()->esc() ?>"><?php endif ?>
            </a>
            <a class="mobile-help design-button" href="<?= page('home')->url() ?>#help-tess"><?= t('cta.help') ?></a>
            <button
                class="menu-toggle min-h-12 rounded-[5px] border border-line bg-white px-[13px] py-[9px] text-ink md:hidden"
                aria-expanded="false" aria-controls="navigation" hidden><?= t('nav.toggle') ?><span
                    data-menu-icon class="ml-2" aria-hidden="true">☰</span></button>
            <nav class="flex w-full flex-wrap items-center justify-center gap-[13px] py-3 text-xs max-md:data-[enhanced=true]:absolute max-md:data-[enhanced=true]:inset-x-0 max-md:data-[enhanced=true]:top-full max-md:data-[enhanced=true]:h-[calc(100dvh-var(--header-height))] max-md:data-[enhanced=true]:flex-col max-md:data-[enhanced=true]:flex-nowrap max-md:data-[enhanced=true]:items-stretch max-md:data-[enhanced=true]:justify-start max-md:data-[enhanced=true]:overflow-y-auto max-md:data-[enhanced=true]:overscroll-contain max-md:data-[enhanced=true]:bg-white max-md:data-[enhanced=true]:text-ink max-md:data-[enhanced=true]:[--nav-accent:var(--color-pink-dark)] max-md:data-[enhanced=true]:px-6 max-md:data-[enhanced=true]:py-6 max-md:data-[enhanced=true]:text-lg md:w-auto md:flex-nowrap md:py-0 md:flex md:text-sm lg:gap-[25px] [&>a:not(:last-child)]:p-2 max-md:data-[enhanced=true]:[&>a:not(:last-child)]:rounded-lg max-md:data-[enhanced=true]:[&>a:not(:last-child)]:bg-blush max-md:data-[enhanced=true]:[&>a:not(:last-child)]:p-4 max-md:data-[enhanced=true]:[&>a]:text-left max-md:data-[enhanced=true]:[&>a]:min-h-12 md:[&>a:not(:last-child)]:p-0 [&>a[aria-current]]:font-bold [&>a[aria-current]]:text-(--nav-accent)"
                id="navigation" aria-label="<?= t('nav.main') ?>">
                <?php foreach (['home', 'haar-verhaal', 'behandelingsplan', 'updates', 'faq'] as $slug): ?>
                    <?php if ($item = page($slug)): ?><a href="<?= $item->url() ?>" <?= $page->is($item) ? 'aria-current="page"' : ($page->isDescendantOf($item) ? 'aria-current="true"' : '') ?>><?= $item->navtitle()->or($item->title())->esc() ?></a><?php endif ?>
                <?php endforeach ?><a href="<?= page('home')->url() ?>#contact"><?= t('nav.contact') ?></a>
                <details class="language-picker" data-language-picker>
                    <summary aria-label="<?= t('nav.languages') ?>: <?= strtoupper($kirby->languageCode()) ?>">
                        <?php snippet('icon', ['name' => 'globe', 'class' => 'language-globe']) ?>
                        <span><?= strtoupper($kirby->languageCode()) ?></span>
                    </summary>
                    <div class="language-options" aria-label="<?= t('nav.languages') ?>">
                        <?php foreach (['nl', 'fr', 'en'] as $code): ?>
                            <?php if ($language = $kirby->languages()->find($code)): ?>
                                <a href="<?= $page->url($language->code()) ?>" hreflang="<?= $language->code() ?>" lang="<?= $language->code() ?>" <?= $language->code() === $kirby->languageCode() ? 'aria-current="true"' : '' ?>><?= strtoupper($language->code()) ?><?php if ($language->code() === $kirby->languageCode()): ?><span aria-hidden="true">✓</span><?php endif ?></a>
                            <?php endif ?>
                        <?php endforeach ?>
                    </div>
                </details>
                <a class="inline-flex min-h-11 items-center justify-center rounded-[5px] border px-[21px] py-[11px] text-sm font-bold border-transparent bg-pink text-donation-ink hover:bg-pink-dark hover:text-white"
                    href="<?= page('home')->url() ?>#help-tess"><?= t('cta.help') ?></a>
            </nav>
        </div>
    </header>
    <main id="main" class="scroll-mt-28">