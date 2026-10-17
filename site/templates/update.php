<?php snippet('layout/header') ?>
<?php $photos = $page->photos()->toFiles() ?>
<article class="py-[42px] md:py-[72px]">
    <div class="mx-auto w-[calc(100%-40px)] lg:w-[calc(100%-64px)] max-w-[800px]">
        <a class="inline-block text-sm font-bold text-pink-dark hover:underline"
            href="<?= $page->parent()->url() ?>"><?= t('updates.back') ?></a>
        <h1 class="mt-6"><?= $page->title()->esc() ?></h1>
        <time datetime="<?= $page->date()->toDate('Y-m-d') ?>"><?= $page->date()->toLocalDate() ?></time>
        <div class="prose mt-6"><?= $page->text()->toBlocks() ?></div>
        <?php if ($photos->isNotEmpty()): ?>
            <section class="mt-10 border-t border-line pt-8" aria-labelledby="photos-title">
                <h2 id="photos-title" class="text-[1.3rem]"><?= t('updates.photos') ?></h2>
                <div class="grid grid-cols-3 gap-3 md:grid-cols-4">
                    <?php foreach ($photos as $photo): ?>
                        <a href="<?= $photo->resize(1600)->url() ?>" data-lightbox-item
                            class="block overflow-hidden rounded-lg"><img
                                class="aspect-square w-full object-cover transition-transform duration-200 ease-in-out hover:scale-105 motion-reduce:transition-none"
                                src="<?= $photo->crop(320, 320)->url() ?>" width="320" height="320"
                                alt="<?= $photo->alt()->or($page->title())->esc() ?>" loading="lazy"></a>
                    <?php endforeach ?>
                </div>
            </section>
        <?php endif ?>
        <?php /* Rendered always: photos in the text blocks open here too. */ ?>
        <dialog data-lightbox aria-label="<?= t('lightbox.label') ?>"
                class="m-auto max-h-none max-w-none overflow-visible bg-transparent p-0 backdrop:bg-ink/85 motion-safe:open:animate-pop motion-safe:open:backdrop:animate-fade">
                <img data-lightbox-image class="block max-h-[85vh] max-w-[92vw] rounded-lg object-contain" alt="">
                <form method="dialog"><button
                        class="fixed top-4 right-4 flex size-11 items-center justify-center rounded-full bg-white text-ink hover:bg-blush"
                        aria-label="<?= t('lightbox.close') ?>"><?php snippet('components/icon', ['name' => 'close']) ?></button></form>
                <button type="button" data-lightbox-prev
                    class="fixed top-1/2 left-4 flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-white text-ink hover:bg-blush"
                    aria-label="<?= t('lightbox.prev') ?>"><?php snippet('components/icon', ['name' => 'chevron-left']) ?></button>
                <button type="button" data-lightbox-next
                    class="fixed top-1/2 right-4 flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-white text-ink hover:bg-blush"
                    aria-label="<?= t('lightbox.next') ?>"><?php snippet('components/icon', ['name' => 'chevron-right']) ?></button>
            </dialog>
    </div>
</article><?php snippet('layout/cta') ?><?php snippet('layout/footer') ?>
