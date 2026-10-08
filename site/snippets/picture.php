<?php
$isHero = ($class ?? '') === 'hero-media';
$mediaClasses = $isHero
    ? 'media mb-6 h-full min-h-[230px] max-h-[330px] w-full rounded-lg object-cover md:mb-0 md:min-h-[400px] md:max-h-none md:rounded-none'
    : 'media aspect-[4/3] h-auto w-full rounded-lg object-cover';
?>
<?php if ($image = $source->toFile()): ?>
    <img class="<?= $mediaClasses ?>" src="<?= $image->url() ?>" alt="<?= $image->alt()->or($label)->esc() ?>" width="<?= $image->width() ?>" height="<?= $image->height() ?>" loading="<?= $isHero ? 'eager' : 'lazy' ?>">
<?php else: ?>
    <div class="<?= $mediaClasses ?> flex items-center justify-center text-sm tracking-[.04em] <?= $isHero ? 'bg-hero-placeholder text-blush' : 'border border-line bg-blush text-pink-dark' ?>"><span><?= esc($label) ?></span></div>
<?php endif ?>