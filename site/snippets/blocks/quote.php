<?php /** @var \Kirby\Cms\Block $block */ ?>
<blockquote class="my-8 border-l-4 border-pink pl-5">
    <div class="text-[1.15rem] leading-snug font-semibold"><?= $block->text() ?></div>
    <?php if ($block->citation()->isNotEmpty()): ?>
        <footer class="mt-2 text-sm text-muted">— <?= $block->citation() ?></footer>
    <?php endif ?>
</blockquote>
