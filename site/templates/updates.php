<?php snippet('header') ?>
<section class="py-[42px] md:py-[72px] min-h-[45vh]">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)]">
        <h1 class="content-page-title"><?= $page->title()->esc() ?></h1>
        <div class="prose"><?= $page->text()->kirbytext() ?></div>
        <?php snippet('update-cards') ?>
    </div>
</section><?php snippet('cta') ?><?php snippet('footer') ?>
