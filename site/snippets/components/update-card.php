<?php
// On the homepage the cards are flatter: no border, wider photo, smaller text.
$home = !empty($homeLayout);
?>
<a href="<?= $update->url() ?>" data-update-card
    class="group flex flex-col overflow-hidden bg-white text-ink hover:-translate-y-[3px] motion-safe:transition-[transform,translate,box-shadow,border-color] motion-safe:duration-250 motion-safe:hover:shadow-card-raised <?= $home ? 'rounded-[6px] border-0' : 'rounded-lg border border-line' ?>">
    <?php if ($photo = $update->thumbnail()): ?>
        <img class="w-full object-cover motion-safe:transition-[transform] motion-safe:duration-500 motion-safe:group-hover:[transform:scale(1.04)] <?= $home ? 'aspect-[2/1] md:aspect-[2.6/1]' : 'aspect-[4/3]' ?>" src="<?= $photo->crop(640, 480)->url() ?>"
            srcset="<?= $photo->crop(640, 480)->url() ?> 640w, <?= $photo->crop(960, 720)->url() ?> 960w"
            sizes="(min-width: 56.3125rem) 360px, (min-width: 42.5625rem) 50vw, 100vw" width="640" height="480"
            alt="" loading="lazy">
    <?php else: ?>
        <div class="flex items-center justify-center bg-blush text-pink-dark <?= $home ? 'aspect-[2/1] md:aspect-[2.6/1]' : 'aspect-[4/3]' ?>" aria-hidden="true">
            <?php snippet('components/icon', ['name' => 'heart', 'class' => 'size-10']) ?></div>
    <?php endif ?>
    <div class="flex flex-1 flex-col <?= $home ? 'p-4' : 'p-5 md:p-6' ?>">
        <time class="<?= $home ? 'text-[12px]' : '' ?>" datetime="<?= $update->date()->toDate('Y-m-d') ?>"><?= $update->date()->toLocalDate() ?></time>
        <?php /* h3 under a section heading on the homepage, h2 straight under the page title on the updates page. */ ?>
        <<?= $home ? 'h3' : 'h2' ?> class="<?= $home ? 'mb-[9px] text-[15px] leading-[1.35] text-ink' : 'text-[1.1rem]' ?>"><?= $update->title()->esc() ?></<?= $home ? 'h3' : 'h2' ?>>
        <p class="text-muted <?= $home ? 'mb-[10px] text-[13px] leading-[1.5]' : 'text-base' ?>"><?= $update->summary(140) ?></p>
        <span class="mt-auto pt-3 text-sm font-bold text-pink-dark group-hover:underline"><?= t('updates.read') ?></span>
    </div>
</a>
