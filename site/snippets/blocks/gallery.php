<?php /** @var \Kirby\Cms\Block $block */ ?>
<?php if (($images = $block->images()->toFiles())->isNotEmpty()): ?>
    <figure class="my-8">
        <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
            <?php foreach ($images as $image): ?>
                <a href="<?= $image->resize(1600)->url() ?>" data-lightbox-item class="block overflow-hidden rounded-lg"><img
                        class="aspect-square w-full object-cover transition-transform duration-200 hover:scale-105 motion-reduce:transition-none"
                        src="<?= $image->crop(480, 480)->url() ?>" width="480" height="480"
                        alt="<?= $image->alt()->or($block->parent()->title())->esc() ?>" loading="lazy"></a>
            <?php endforeach ?>
        </div>
        <?php if ($block->caption()->isNotEmpty()): ?>
            <figcaption class="mt-2 text-sm text-muted"><?= $block->caption() ?></figcaption>
        <?php endif ?>
    </figure>
<?php endif ?>
