<?php
$isHero = ($class ?? '') === 'hero-media';
$mediaClasses = $isHero
    ? 'media absolute inset-0 size-full object-cover'
    : 'media aspect-[4/3] h-auto w-full rounded-lg object-cover';
?>
<?php if ($image = $source->toFile()): ?>
    <img class="<?= $mediaClasses ?>" src="<?= $image->url() ?>" alt="<?= $image->alt()->or($label)->esc() ?>"
        width="<?= $image->width() ?>" height="<?= $image->height() ?>" loading="<?= $isHero ? 'eager' : 'lazy' ?>">
<?php else: ?>
    <?php if ($isHero): ?>
        <div class="<?= $mediaClasses ?> bg-hero-placeholder"></div>
    <?php else: ?>
        <div
            class="<?= $mediaClasses ?> flex items-center justify-center border border-line bg-blush text-sm tracking-[.04em] text-pink-dark">
            <span><?= esc($label) ?></span></div>
    <?php endif ?>
<?php endif ?>