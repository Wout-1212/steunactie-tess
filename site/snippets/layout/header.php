<?php
// Home and disclaimer use the full pink-yellow-aqua gradient; the other pages only pink and yellow.
$isQuietPage = !$page->isHomePage() && $page->intendedTemplate()->name() !== 'disclaimer';
$navLink = 'hover:text-pink md:motion-safe:link-underline';
$logoClass = 'h-auto w-auto max-h-[44px] max-w-[112px] [filter:drop-shadow(0_0_.7px_#27222A)_drop-shadow(0_0_.7px_#27222A)] md:max-h-[54px] md:max-w-[110px] 2xl:max-w-[140px]';
?>
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

<body class="min-h-screen font-sans text-[15px] leading-[1.65] text-ink <?= $isQuietPage ? 'bg-background-middle' : 'bg-background-bottom' ?>">
    <a class="fixed -top-[100px] left-5 z-50 bg-white p-3 text-pink-dark focus:top-[10px]"
        href="#main"><?= t('skip-link') ?></a>
    <header data-site-header
        class="sticky top-0 z-40 border-b border-transparent bg-blush text-ink transition-[background-color,border-color] duration-160 data-[scrolled=true]:border-b-[#C43B7514] motion-reduce:transition-none [--nav-accent:var(--color-pink)]">
        <div
            class="mx-auto flex min-h-[68px] w-[calc(100%-40px)] max-w-[1120px] flex-wrap items-center justify-between gap-2 py-[10px] md:min-h-[78px] md:flex-nowrap md:gap-4 md:py-0 lg:w-[calc(100%-64px)] 2xl:gap-[30px]">
            <a class="shrink-0 leading-none" href="<?= $site->url() ?>" aria-label="<?= t('nav.home-label') ?>">
                <?php if ($logo = $site->logo()->toFile()): ?><img class="<?= $logoClass ?>" src="<?= $logo->url() ?>"
                        alt="<?= $site->title()->esc() ?>">
                <?php else: ?><img class="<?= $logoClass ?>" src="<?= url('assets/images/logo.png') ?>" width="2008" height="783"
                        alt="<?= $site->title()->esc() ?>"><?php endif ?>
            </a>
            <?php snippet('components/button', ['donation' => true, 'href' => page('home')->url() . '#help-tess', 'text' => t('cta.help'), 'size' => 'header']) ?>
            <button data-menu-toggle
                class="min-h-[38px] rounded-[5px] border-0 bg-transparent p-[6px] text-[12px] text-pink md:hidden"
                aria-expanded="false" aria-controls="navigation" hidden><?= t('nav.toggle') ?><span
                    data-menu-icon class="ml-2" aria-hidden="true">☰</span></button>
            <nav class="flex w-full flex-wrap items-center justify-center gap-[13px] py-3 text-[12px] leading-[calc(1/0.75)] max-md:data-[enhanced=true]:absolute max-md:data-[enhanced=true]:inset-x-0 max-md:data-[enhanced=true]:top-full max-md:data-[enhanced=true]:h-[calc(100dvh-var(--header-height))] max-md:data-[enhanced=true]:flex-col max-md:data-[enhanced=true]:flex-nowrap max-md:data-[enhanced=true]:items-stretch max-md:data-[enhanced=true]:justify-start max-md:data-[enhanced=true]:overflow-y-auto max-md:data-[enhanced=true]:overscroll-contain max-md:data-[enhanced=true]:bg-blush max-md:data-[enhanced=true]:text-ink max-md:data-[enhanced=true]:[--nav-accent:var(--color-pink-dark)] max-md:data-[enhanced=true]:px-6 max-md:data-[enhanced=true]:py-6 max-md:data-[enhanced=true]:text-[18px] max-md:data-[enhanced=true]:leading-[calc(1.75/1.125)] max-md:motion-safe:data-[enhanced=true]:not-[.hidden]:animate-fade-down-fast md:w-auto md:flex-nowrap md:gap-3 md:py-0 md:flex md:text-[11px] md:leading-[calc(1.25/0.875)] 2xl:gap-[25px] 2xl:text-[12px] [&>a:not(:last-child)]:p-2 max-md:data-[enhanced=true]:[&>a:not(:last-child)]:rounded-lg max-md:data-[enhanced=true]:[&>a:not(:last-child)]:bg-blush max-md:data-[enhanced=true]:[&>a:not(:last-child)]:p-4 max-md:data-[enhanced=true]:[&>a]:text-left max-md:data-[enhanced=true]:[&>a]:min-h-12 md:[&>a:not(:last-child)]:p-0 [&>a[aria-current]]:font-bold [&>a[aria-current]]:text-(--nav-accent)"
                id="navigation" aria-label="<?= t('nav.main') ?>">
                <?php foreach (['home', 'haar-verhaal', 'behandelingsplan', 'updates', 'faq'] as $slug): ?>
                    <?php if ($item = page($slug)): ?><a class="<?= $navLink ?>" href="<?= $item->url() ?>" <?= $page->is($item) ? 'aria-current="page"' : ($page->isDescendantOf($item) ? 'aria-current="true"' : '') ?>><?= $item->navtitle()->or($item->title())->esc() ?></a><?php endif ?>
                <?php endforeach ?><a class="<?= $navLink ?>" href="<?= page('home')->url() ?>#contact"><?= t('nav.contact') ?></a>
                <details class="group relative shrink-0 max-md:self-start" data-language-picker>
                    <summary class="relative flex min-h-11 list-none items-center gap-1.5 p-0 focus-visible:rounded-[3px] focus-visible:outline-2 focus-visible:outline-offset-3 focus-visible:outline-pink motion-safe:transition-[color] motion-safe:duration-200" aria-label="<?= t('nav.languages') ?>: <?= strtoupper($kirby->languageCode()) ?>">
                        <?php snippet('components/icon', ['name' => 'globe', 'class' => 'size-[17px]']) ?>
                        <span><?= strtoupper($kirby->languageCode()) ?></span>
                    </summary>
                    <div class="absolute top-full right-0 z-50 min-w-[92px] rounded-[6px] bg-white p-[6px] text-ink shadow-[0_5px_18px_#27222A1A] max-md:relative max-md:right-auto max-md:mb-[6px] motion-safe:group-open:animate-fade-down" aria-label="<?= t('nav.languages') ?>">
                        <?php foreach (['nl', 'fr', 'en'] as $code): ?>
                            <?php if ($language = $kirby->languages()->find($code)): ?>
                                <?php $isCurrent = $language->code() === $kirby->languageCode() ?>
                                <a class="flex min-h-11 items-center justify-between gap-4 rounded-[3px] px-3 py-2 hover:bg-blush hover:text-pink focus-visible:bg-blush motion-safe:transition-[color,background-color] motion-safe:duration-200<?= $isCurrent ? ' font-bold text-pink' : '' ?>" href="<?= $page->url($language->code()) ?>" hreflang="<?= $language->code() ?>" lang="<?= $language->code() ?>" <?= $isCurrent ? 'aria-current="true"' : '' ?>><?= strtoupper($language->code()) ?><?php if ($isCurrent): ?><span aria-hidden="true">✓</span><?php endif ?></a>
                            <?php endif ?>
                        <?php endforeach ?>
                    </div>
                </details>
                <a class="donation-light inline-flex min-h-11 items-center justify-center rounded-[5px] border border-transparent bg-pink px-[21px] py-[11px] text-sm font-bold text-white hover:bg-pink-dark hover:text-white hover:opacity-90 md:max-2xl:px-3 motion-safe:lift"
                    href="<?= page('home')->url() ?>#help-tess"><?= t('cta.help') ?></a>
            </nav>
        </div>
    </header>
    <main id="main" class="min-h-[40vh] scroll-mt-28 bg-no-repeat <?= $isQuietPage ? 'bg-[linear-gradient(180deg,#F5D7E6_0%,#FFF6CE_100%)]' : 'bg-[linear-gradient(180deg,#F5D7E6_0%,#FFF6CE_50%,#C8EEF3_100%)]' ?> [&>section:not([data-hero])]:scroll-mt-[calc(var(--header-height,78px)+20px)]">