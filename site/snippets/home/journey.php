<?php
$steps = $page->treatment_steps()->toStructure();
$statuses = ['done', 'current', 'upcoming'];
// Five steps or fewer fit side by side on desktop; more stay a vertical list.
$horizontal = $steps->count() <= 5;
?>
<section class="py-9 md:py-16" aria-labelledby="journey-title">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] md:w-[calc(100%-64px)]">
        <?php snippet('components/heading', ['text' => $page->treatment_title(), 'id' => 'journey-title']) ?>
        <?php if ($page->treatment_intro()->isNotEmpty()): ?>
            <p class="max-w-[68ch]"><?= $page->treatment_intro()->esc() ?></p>
        <?php endif ?>
        <?php if ($page->treatment_image()->isNotEmpty()): ?>
            <div class="mt-6 mb-2 max-w-[760px]"><?php snippet('components/picture', ['source' => $page->treatment_image(), 'label' => t('home.treatment.image'), 'ratio' => 'aspect-[16/9]']) ?></div>
        <?php endif ?>
        <?php if ($steps->isNotEmpty()): ?>
            <ol data-reveal-group class="journey mt-[22px] grid gap-3 md:mt-[30px] md:gap-[14px]<?= $horizontal ? ' journey-horizontal lg:auto-cols-fr lg:grid-flow-col lg:gap-5' : '' ?>">
                <?php foreach ($steps as $step): ?>
                    <?php $status = in_array($step->status()->value(), $statuses, true) ? $step->status()->value() : null ?>
                    <li class="relative pl-11 md:pl-14<?= $horizontal ? ' lg:pt-[54px] lg:pl-0' : '' ?><?= $status === 'done' ? ' is-done' : '' ?>"<?= $status === 'current' ? ' aria-current="step"' : '' ?>>
                        <span class="journey-marker absolute top-[14px] left-0 z-1 grid size-[30px] place-items-center rounded-[50%] border-2 border-pink text-[13px] leading-none font-bold md:size-9 md:text-[14px]<?= $horizontal ? ' lg:top-0' : '' ?> <?= $status === 'upcoming' ? 'bg-white text-pink' : 'bg-pink text-white' ?><?= $status === 'current' ? ' shadow-[0_0_0_6px_#C43B7526]' : '' ?>" aria-hidden="true"></span>
                        <div class="h-full rounded-lg border bg-white p-4 md:px-5 md:py-[18px] motion-safe:transition-shadow motion-safe:duration-250 motion-safe:hover:shadow-card <?= $status === 'current' ? 'border-pink' : 'border-transparent' ?>">
                            <?php if ($status): ?><span class="mb-[6px] block text-[11px] font-bold tracking-[.08em] text-pink uppercase"><?= t('journey.' . $status) ?></span><?php endif ?>
                            <h3 class="m-0 text-[15px] leading-[1.4] text-ink"><?= $step->heading()->esc() ?></h3>
                            <?php if ($step->text()->isNotEmpty()): ?><p class="mt-[6px] mb-0 text-[14px] leading-[1.6]"><?= $step->text()->esc() ?></p><?php endif ?>
                        </div>
                    </li>
                <?php endforeach ?>
            </ol>
        <?php endif ?>
        <a class="mt-[22px] inline-block text-[13px] font-bold text-pink hover:underline motion-safe:transition-[color,background-color] motion-safe:duration-200" href="<?= page('behandelingsplan')->url() ?>"><?= t('home.treatment.more') ?></a>
    </div>
</section>
