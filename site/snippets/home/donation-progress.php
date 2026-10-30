<?php // Fundraising only: euros raised from all sources. The medical scale above never uses these numbers. ?>
<section class="py-9 text-ink md:py-16" id="donatievoortgang">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)]">
        <?php snippet('components/heading', ['text' => $site->progress_title()->or(t('progress.title'))]) ?>
        <div class="mb-3 flex flex-wrap items-baseline justify-between gap-3 md:gap-5 max-xs:items-start">
            <?php if ($progress['hasGoal']): ?>
                <strong data-count-up class="text-[32px] leading-[1.2] tracking-[-.02em] text-pink tabular-nums md:text-[40px]"><?= $progress['raised'] ?></strong><span class="max-w-none text-right text-[11px] leading-normal text-ink md:text-[13px]"><?= t('progress.of-goal') ?> <?= $progress['goal'] ?>
                    <b data-count-up class="ml-[6px] inline-block text-right tabular-nums md:ml-3"><?= $progress['percent'] ?>%</b></span>
            <?php else: ?>
                <strong class="text-[32px] leading-[1.2] tracking-[-.02em] text-pink md:text-[40px]"><?= t('progress.sample-raised') ?></strong><span class="max-w-none text-right text-[11px] leading-normal text-ink md:text-[13px]"><?= t('progress.of-goal') ?> <?= t('progress.sample-goal') ?>
                    <b class="ml-[6px] inline-block md:ml-3">14%</b></span>
            <?php endif ?>
        </div>
        <?php if ($progress['hasGoal']): ?>
            <progress data-donation-bar class="donation-bar block h-[15px] w-full appearance-none overflow-hidden rounded-full bg-white" value="<?= $progress['percent'] ?>" max="100"
                aria-label="<?= t('progress.label') ?>"><?= $progress['percent'] ?>%</progress>
        <?php else: ?>
            <div data-donation-bar class="block h-[15px] w-full overflow-hidden rounded-full bg-white" aria-hidden="true"><span class="block h-full w-[14%] rounded-full bg-pink motion-safe:transition-[width] motion-safe:duration-1200 motion-safe:ease-[cubic-bezier(.22,1,.36,1)]"></span></div>
        <?php endif ?>
        <?php if ($progress['hasGoal'] && $progress['donations']): ?>
            <p class="mt-2 mb-0 flex items-center gap-1.5 text-sm"><?php snippet('components/icon', ['name' => 'heart', 'class' => 'size-4']) ?><span><?= tt('progress.donations', ['count' => '<span data-count-up class="inline-block tabular-nums">' . $progress['donations'] . '</span>']) ?></span></p>
        <?php endif ?>
        <div class="mt-4 grid grid-cols-4 gap-2 text-center text-[9px] leading-normal md:gap-4 md:text-[12px]">
            <?php foreach ($progress['milestones'] as $milestone): ?>
                <div class="min-w-0 wrap-anywhere"><strong class="inline-flex items-center gap-1 text-[10px] tracking-[.04em] text-pink max-md:text-ink xs:text-[12px] md:text-[15px]"><?php if ($milestone['reached']): ?><?php snippet('components/icon', ['name' => 'check', 'class' => 'size-4']) ?><?php endif ?><?= $milestone['amount'] ?></strong><span class="block"><?= esc($milestone['label']) ?></span></div>
            <?php endforeach ?>
        </div>
    </div>
</section>
