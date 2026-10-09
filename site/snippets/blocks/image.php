<?php
/** @var \Kirby\Cms\Block $block */
$image = $block->location() == 'web' ? null : $block->image()->toFile();
$src = $image ? $image->resize(1200)->url() : $block->src()->value();
$alt = $block->alt()->or($image?->alt())->esc();
// Uploaded photos open in the lightbox, unless the editor set a link.
$href = $block->link()->isNotEmpty() ? $block->link()->toUrl() : $image?->resize(1600)->url();
?>
<?php if ($src): ?>
    <figure class="my-8">
        <?php if ($href): ?><a class="block overflow-hidden rounded-lg" href="<?= esc($href, 'attr') ?>" <?= $block->link()->isEmpty() ? 'data-lightbox-item' : '' ?>><?php endif ?>
            <img class="w-full rounded-lg" src="<?= esc($src, 'attr') ?>" alt="<?= $alt ?>" <?php if ($image): ?>width="<?= $image->width() ?>" height="<?= $image->height() ?>" srcset="<?= $image->resize(800)->url() ?> 800w, <?= $image->resize(1200)->url() ?> 1200w" sizes="(min-width: 50rem) 736px, 100vw"<?php endif ?> loading="lazy">
        <?php if ($href): ?></a><?php endif ?>
        <?php if ($block->caption()->isNotEmpty()): ?>
            <figcaption class="mt-2 text-sm text-muted"><?= $block->caption() ?></figcaption>
        <?php endif ?>
    </figure>
<?php endif ?>
