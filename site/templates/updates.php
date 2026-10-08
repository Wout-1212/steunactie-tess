<?php snippet('header') ?>
<section class="py-[42px] md:py-[72px] min-h-[45vh]">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)]">
        <h1><?= $page->title()->esc() ?></h1>
        <div class="prose"><?= $page->text()->kirbytext() ?></div>
        <div class="update-timeline mx-auto my-9 max-w-[850px] [&_h2]:text-[1.4rem]">
            <?php $items = $page->children()->listed()->sortBy('date', 'desc'); ?>
            <?php foreach ($items as $item): ?>
                <article><time datetime="<?= $item->date()->toDate('Y-m-d') ?>"><?= $item->date()->toDate('d-m-Y') ?></time>
                    <h2><a href="<?= $item->url() ?>"><?= $item->title()->esc() ?></a></h2>
                    <p><?= $item->text()->excerpt(250)->esc() ?></p><a
                        class="mt-3 inline-block text-sm font-bold text-pink-dark hover:underline"
                        href="<?= $item->url() ?>"><?= t('updates.read') ?></a>
                </article><?php endforeach ?>
            <?php if ($items->isEmpty()): ?>
                <p class="mb-6 rounded-lg border border-dashed border-line bg-white p-6 text-muted">
                    <?= t('updates.empty') ?>
                </p><?php endif ?>
        </div>
    </div>
</section><?php snippet('cta') ?><?php snippet('footer') ?>