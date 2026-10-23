<?php
// Photo from a files field, or a placeholder box while no photo is uploaded.
// $ratio:       aspect-ratio classes (default 4:3).
// $rounded:     corner radius of the photo (default 8px).
// $placeholder: 'soft' (pink box) or 'dashed' (transparent box with a dashed border).
// $portrait:    the hero photo; loads eagerly and first, and uses the large uppercase placeholder label.
// $sizes:       how wide the photo is shown, so the browser picks the smallest file that is sharp enough
//               (default: half the page from 681px, the full width below).
$ratio ??= 'aspect-[4/3]';
$rounded ??= 'rounded-lg';
$portrait ??= false;
$sizes ??= '(min-width: 1120px) 560px, (min-width: 681px) 50vw, 100vw';
$placeholder ??= $portrait ? 'dashed' : 'soft';
$placeholderClasses = [
    'soft' => "$rounded border border-line bg-blush text-sm tracking-[.04em] text-pink-dark",
    // The hero label styles every span inside it, including the automatic Tess name.
    'dashed' => 'rounded-[6px] border border-dashed border-[#C43B7580] bg-[#FFFFFF55] text-center text-pink' . ($portrait ? ' [&_span]:p-6 [&_span]:text-[12px] [&_span]:tracking-[.08em] [&_span]:uppercase' : ' text-sm tracking-[.04em]'),
];
?>
<?php if ($image = $source->toFile()): ?>
    <img class="h-auto w-full object-cover <?= $ratio ?> <?= $rounded ?>" src="<?= $image->resize(1200)->url() ?>"
        srcset="<?= $image->srcset([480, 800, 1200, 1600]) ?>" sizes="<?= $sizes ?>" alt="<?= $image->alt()->or($label)->esc() ?>"
        width="<?= $image->width() ?>" height="<?= $image->height() ?>" <?= $portrait ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"' ?>>
<?php else: ?>
    <div class="flex h-auto w-full items-center justify-center <?= $ratio ?> <?= $placeholderClasses[$placeholder] ?>" <?= $portrait ? 'data-photo-placeholder role="img" aria-label="' . esc($label, 'attr') . '"' : '' ?>>
        <span><?= esc($label) ?></span></div>
<?php endif ?>
