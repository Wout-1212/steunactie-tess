<?php
// Photo from a files field, or a placeholder box while no photo is uploaded.
// $ratio:       aspect-ratio classes (default 4:3).
// $rounded:     corner radius of the photo (default 8px).
// $placeholder: 'soft' (pink box) or 'dashed' (transparent box with a dashed border).
// $portrait:    the hero photo; loads eagerly and uses the large uppercase placeholder label.
$ratio ??= 'aspect-[4/3]';
$rounded ??= 'rounded-lg';
$portrait ??= false;
$placeholder ??= $portrait ? 'dashed' : 'soft';
$placeholderClasses = [
    'soft' => "$rounded border border-line bg-blush text-sm tracking-[.04em] text-pink-dark",
    // The hero label styles every span inside it, including the automatic Tess name.
    'dashed' => 'rounded-[6px] border border-dashed border-[#C43B7580] bg-[#FFFFFF55] text-center text-pink' . ($portrait ? ' [&_span]:p-6 [&_span]:text-[12px] [&_span]:tracking-[.08em] [&_span]:uppercase' : ' text-sm tracking-[.04em]'),
];
?>
<?php if ($image = $source->toFile()): ?>
    <img class="h-auto w-full object-cover <?= $ratio ?> <?= $rounded ?>" src="<?= $image->url() ?>" alt="<?= $image->alt()->or($label)->esc() ?>"
        width="<?= $image->width() ?>" height="<?= $image->height() ?>" loading="<?= $portrait ? 'eager' : 'lazy' ?>">
<?php else: ?>
    <div class="flex h-auto w-full items-center justify-center <?= $ratio ?> <?= $placeholderClasses[$placeholder] ?>" <?= $portrait ? 'data-photo-placeholder role="img" aria-label="' . esc($label, 'attr') . '"' : '' ?>>
        <span><?= esc($label) ?></span></div>
<?php endif ?>
