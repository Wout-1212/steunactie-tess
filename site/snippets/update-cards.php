<?php $items = page('updates')?->children()->listed()->sortBy('date', 'desc'); ?>
<?php if ($items && $items->isNotEmpty()): ?>
    <div class="my-[26px] grid grid-cols-1 gap-6 md:grid-cols-2">
        <?php foreach ($items->limit(2) as $item): ?>
            <article
                class="grid grid-cols-[85px_1fr] items-center gap-[15px] rounded-lg bg-white p-5 md:grid-cols-1 md:gap-[22px] md:p-6 lg:grid-cols-[110px_1fr] [&_p]:text-base [&_p]:text-muted [&_.media]:h-[100px] [&_.media]:aspect-auto md:[&_.media]:h-auto md:[&_.media]:aspect-[4/3] md:[&_.media]:max-h-40 lg:[&_.media]:max-h-none">
                <?php snippet('picture', ['source' => $item->cover(), 'label' => page('updates')->copyfotobijupdate()->value()]) ?>
                <div><?php if ($item->date()->isNotEmpty()): ?><time
                            datetime="<?= $item->date()->toDate('Y-m-d') ?>"><?= $item->date()->toDate('d-m-Y') ?></time><?php endif ?>
                    <h3><?= $item->title()->esc() ?></h3>
                    <p><?= $item->text()->excerpt(140)->esc() ?></p><a
                        class="mt-3 inline-block text-sm font-bold text-pink-dark hover:underline"
                        href="<?= $item->url() ?>"><?= page('updates')->copyleesupdate()->esc() ?></a>
                </div>
            </article><?php endforeach ?>
    </div>
<?php else: ?>
    <p class="mb-6 rounded-lg border border-dashed border-line bg-white p-6 text-muted">
        <?= page('updates')->copybinnenkortleesjehierdeupdatesovertess()->esc() ?></p><?php endif ?>