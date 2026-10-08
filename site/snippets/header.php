<!doctype html>
<html lang="nl">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $page->title()->esc() ?> | <?= $site->title()->esc() ?></title>
<meta name="description" content="<?= $page->description()->or($site->description())->esc() ?>">
<?= css('assets/css/site.css') ?><?= js('assets/js/site.js', ['defer' => true]) ?>
</head><body class="bg-paper font-sans text-base leading-[1.65] text-ink">
<a class="fixed -top-[100px] left-5 z-50 bg-white p-3 text-pink-dark focus:top-[10px]" href="#main"><?= $site->copyganaarinhoud()->esc() ?></a>
<header data-site-header class="sticky top-0 z-40 border-b border-line bg-white text-ink transition-colors duration-200 motion-reduce:transition-none data-[scrolled=true]:bg-white/80 data-[scrolled=true]:backdrop-blur-md"><div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)] flex min-h-[76px] flex-wrap items-center justify-between gap-3 py-[14px] md:min-h-[90px] md:flex-nowrap md:gap-[30px] md:py-0">
<a class="shrink-0 leading-none [&_img]:h-auto [&_img]:w-auto [&_img]:max-h-16 [&_img]:max-w-[min(164px,42vw)] md:[&_img]:max-w-[164px] [&_img]:[filter:drop-shadow(0_0_.7px_#302a2d)_drop-shadow(0_0_.7px_#302a2d)]" href="<?= $site->url() ?>" aria-label="<?= $site->copysamenvoortesshome()->esc() ?>">
<?php if ($logo = $site->logo()->toFile()): ?><img src="<?= $logo->url() ?>" alt="<?= $site->copysamenvoortess()->esc() ?>">
<?php else: ?><img src="<?= url('assets/images/logo.png') ?>" width="2008" height="783" alt="<?= $site->copysamenvoortess()->esc() ?>"><?php endif ?>
</a>
<button class="menu-toggle rounded-[5px] border border-line bg-white px-[13px] py-[9px] text-ink md:hidden" aria-expanded="false" aria-controls="navigation" hidden><?= $site->copymenu()->esc() ?><span aria-hidden="true">☰</span></button>
<nav class="flex w-full flex-wrap items-center justify-center gap-[13px] py-3 text-xs md:w-auto md:flex-nowrap md:py-0 md:flex lg:gap-[25px] lg:text-[.8rem] [&>a:not(:last-child)]:p-2 md:[&>a:not(:last-child)]:p-0 [&>a[aria-current]]:font-bold [&>a[aria-current]]:text-pink-dark" id="navigation" aria-label="<?= $site->copyhoofdnavigatie()->esc() ?>">
<?php foreach (['home', 'haar-verhaal', 'behandelingsplan', 'faq'] as $slug): ?>
<?php if ($item = page($slug)): ?><a href="<?= $item->url() ?>" <?= $page->is($item) ? 'aria-current="page"' : '' ?>><?= $item->navtitle()->or($item->title())->esc() ?></a><?php endif ?>
<?php endforeach ?><a href="<?= page('home')->url() ?>#contact"><?= $site->copycontact()->esc() ?></a>
<a class="inline-flex min-h-11 items-center justify-center rounded-[5px] border px-[21px] py-[11px] text-sm font-bold border-transparent bg-pink text-donation-ink hover:bg-pink-dark hover:text-white" href="<?= page('home')->url() ?>#help-tess"><?= $site->copyhelptess()->esc() ?></a></nav>
</div></header><main id="main" class="scroll-mt-28">
