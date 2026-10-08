<section class="scroll-mt-28 py-6 md:py-7 bg-pink text-blush" id="donatievoortgang">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)]">
        <h2 class="mb-[15px] text-[clamp(.95rem,1.55vw,1.15rem)] leading-[1.3] tracking-[.055em] uppercase">
            <?= $page->progress_title()->esc() ?></h2>
        <div
            class="mb-3 flex flex-wrap items-baseline justify-between gap-3 [&_strong]:text-[clamp(1.45rem,2.5vw,1.8rem)] [&_strong]:leading-[1.2] [&_span]:max-w-[165px] [&_span]:text-right [&_span]:text-sm [&_span]:leading-normal [&_b]:ml-[6px] [&_b]:inline-block md:gap-5 md:[&_span]:max-w-none md:[&_span]:text-sm md:[&_b]:ml-3 max-xs:items-start max-xs:[&_span]:max-w-[140px]">
            <strong>€
                <?= $progress['hasTotals'] ? number_format($progress['raised'], 0, ',', '.') : t('progress.sample-raised') ?></strong><span><?= t('progress.of-goal') ?>
                <?= $progress['hasTotals'] ? number_format($progress['goal'], 0, ',', '.') : t('progress.sample-goal') ?>
                <b><?= $progress['percent'] ?>%</b></span></div>
        <?php if ($progress['hasTotals']): ?>
            <progress class="donation-bar" value="<?= $progress['percent'] ?>" max="100"
                aria-label="<?= t('progress.label') ?>"><?= $progress['percent'] ?>%</progress>
        <?php else: ?>
            <div class="donation-bar" aria-hidden="true"><span class="block h-full w-[14%] rounded-full bg-blush"></span></div>
        <?php endif ?>
        <div
            class="mt-[13px] grid grid-cols-2 gap-4 text-center text-sm leading-normal [&_strong]:text-base [&_strong]:tracking-[.04em] [&_span]:block md:grid-cols-4 md:gap-4">
            <?php foreach ($site->milestones()->toStructure() as $item): ?>
                <div><strong><?= $item->amount()->esc() ?></strong><span><?= $item->label()->esc() ?></span></div>
            <?php endforeach ?>
        </div>
    </div>
</section>
