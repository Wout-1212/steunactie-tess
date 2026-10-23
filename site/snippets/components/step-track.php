<?php
// Numbered steps with a status: a vertical list on phones and tablets, a horizontal row on desktop.
// Steps that do not fit scroll sideways; site.js adds the arrows and edge fade when needed.
// $steps:  structure with heading and optionally status (done, current, upcoming)
// $label:  name of the steps for screen readers, usually the section title
// $class:  optional classes for the outer element, such as spacing
$class ??= '';
$statuses = ['done', 'current', 'upcoming'];
?>
<div data-step-track data-reveal-group<?= $class ? ' class="' . $class . '"' : '' ?>>
    <div data-step-track-nav class="mb-3 flex justify-end gap-2" hidden>
        <?php foreach (['prev' => 'chevron-left', 'next' => 'chevron-right'] as $direction => $icon): ?>
            <button type="button" data-step-track-<?= $direction ?> class="flex size-10 items-center justify-center rounded-full bg-white text-pink shadow-card hover:bg-blush disabled:cursor-default disabled:opacity-40 disabled:hover:bg-white motion-safe:transition-[background-color,opacity] motion-safe:duration-200"
                aria-label="<?= t('steps.' . $direction) ?>"><?php snippet('components/icon', ['name' => $icon]) ?></button>
        <?php endforeach ?>
    </div>
    <div data-step-track-scroller role="region" aria-label="<?= esc(tt('steps.region', ['title' => (string)$label]), 'attr') ?>" class="rounded-lg focus-visible:-outline-offset-3 lg:-my-3 lg:snap-x lg:snap-mandatory lg:overflow-x-auto lg:overflow-y-hidden lg:overscroll-x-contain lg:py-3 lg:[scrollbar-width:none] lg:[&::-webkit-scrollbar]:hidden">
        <ol class="step-track grid gap-3 md:gap-[14px] lg:auto-cols-[minmax(240px,1fr)] lg:grid-flow-col lg:gap-5">
            <?php foreach ($steps as $step): ?>
                <?php $status = in_array($step->status()->value(), $statuses, true) ? $step->status()->value() : null ?>
                <li class="relative pl-11 md:pl-14 lg:snap-start lg:pt-[54px] lg:pl-0<?= $status === 'done' ? ' is-done' : '' ?>"<?= $status === 'current' ? ' aria-current="step"' : '' ?>>
                    <span class="step-track-marker absolute top-[14px] left-0 z-1 grid size-[30px] place-items-center rounded-[50%] border-2 border-pink text-[13px] leading-none font-bold md:size-9 md:text-[14px] lg:top-0 <?= $status === 'upcoming' ? 'bg-white text-pink' : 'bg-pink text-white' ?><?= $status === 'current' ? ' shadow-[0_0_0_6px_#C43B7526]' : '' ?>" aria-hidden="true"></span>
                    <div class="h-full rounded-lg border bg-white p-4 md:px-5 md:py-[18px] motion-safe:transition-shadow motion-safe:duration-250 motion-safe:hover:shadow-card <?= $status === 'current' ? 'border-pink' : 'border-transparent' ?>">
                        <?php if ($status): ?><span class="mb-[6px] block text-[11px] font-bold tracking-[.08em] text-pink uppercase"><?= t('journey.' . $status) ?></span><?php endif ?>
                        <h3 class="m-0 text-[15px] leading-[1.4] text-ink"><?= $step->heading()->esc() ?></h3>
                    </div>
                </li>
            <?php endforeach ?>
        </ol>
    </div>
</div>
