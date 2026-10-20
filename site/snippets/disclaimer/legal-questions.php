<?php /* Fold-out questions on the disclaimer page; all closed by default. */ ?>
<div class="mt-5 grid gap-3">
    <?php foreach ($questions as $question): ?>
        <details class="group rounded-[5px] border border-blush bg-white">
            <summary class="fold-sign relative list-none py-4 pr-12 pl-[18px] text-[15px] font-bold text-ink motion-safe:transition-[color] motion-safe:duration-200"><?= $question->question()->esc() ?></summary>
            <div class="prose max-w-none wrap-anywhere px-[18px] pb-[18px] motion-safe:group-open:animate-fade-down"><?= $question->answer()->kirbytext() ?></div>
        </details>
    <?php endforeach ?>
</div>
