<?php
// Updates as cards, newest first. Pass 'limit' to show only the latest few.
$items = page('updates')?->children()->listed()->sortBy('date', 'desc');
if ($items && isset($limit)) {
    $items = $items->limit($limit);
}
$home = !empty($homeLayout);
?>
<?php if ($items && $items->isNotEmpty()): ?>
    <div data-reveal-group class="my-[26px] grid <?= $home ? 'grid-cols-[1fr] gap-[18px] md:grid-cols-2 md:gap-5 xl:grid-cols-3' : 'grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3' ?>">
        <?php foreach ($items as $item): ?>
            <?php snippet('components/update-card', ['update' => $item, 'homeLayout' => $home]) ?>
        <?php endforeach ?>
    </div>
<?php else: ?>
    <p class="my-[26px] rounded-lg border border-dashed border-line bg-white p-6 text-muted"><?= t('updates.empty') ?></p>
<?php endif ?>
