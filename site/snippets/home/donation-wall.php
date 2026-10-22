<?php
// Donation wall next to the donation block: the latest 10 paid donations, and a popup with all of them
// (loaded in batches from the donaties/lijst route by site.js).
$donations = $site->recentDonations(10);
$total = $site->mollieTotals()['donations'];
?>
<aside class="rounded-lg bg-white p-5 text-left md:p-6" aria-labelledby="donation-wall-title">
    <h3 class="mb-1 text-[18px] leading-[1.3] text-ink md:text-[20px]" id="donation-wall-title"><?= $page->messages_title()->or(t('wall.title'))->esc() ?></h3>
    <?php if ($page->messages_intro()->isNotEmpty()): ?>
        <p class="mb-4 text-[13px]"><?= $page->messages_intro()->esc() ?></p>
    <?php endif ?>
    <?php if ($donations === []): ?>
        <p class="mt-4 mb-0 text-[14px]"><?= t('wall.empty') ?></p>
    <?php else: ?>
        <ul class="mt-4 mb-0 list-none p-0">
            <?php foreach ($donations as $donation): ?><?php snippet('components/donation-item', ['donation' => $donation]) ?><?php endforeach ?>
        </ul>
        <?php if ($total > count($donations)): ?>
            <button type="button" class="mt-4 w-full rounded-[5px] border border-pink px-4 py-2.5 text-[13px] font-semibold text-pink hover:bg-blush motion-safe:transition-[background-color] motion-safe:duration-200"
                data-donations-open hidden><?= tt('wall.all', ['count' => $total]) ?></button>
        <?php endif ?>
    <?php endif ?>
</aside>
<?php if ($total > count($donations)): ?>
    <dialog data-donations-dialog data-url="<?= rtrim($kirby->language()->url(), '/') ?>/donaties/lijst" aria-labelledby="donations-dialog-title"
        class="m-auto max-h-[min(100%-32px,720px)] w-[min(100%-32px,560px)] rounded-lg border border-line bg-white p-0 text-left text-ink shadow-xl backdrop:bg-ink/60 motion-safe:open:animate-pop motion-safe:open:backdrop:animate-fade">
        <div class="flex items-start justify-between gap-4 border-b border-divider px-6 pt-6 pb-4">
            <?php snippet('components/heading', ['text' => tt('wall.all', ['count' => $total]), 'id' => 'donations-dialog-title', 'margin' => 'mb-0']) ?>
            <form method="dialog"><button class="flex size-9 items-center justify-center rounded-[5px] text-pink-dark hover:bg-blush"
                    aria-label="<?= t('share.close') ?>"><?php snippet('components/icon', ['name' => 'close']) ?></button></form>
        </div>
        <div class="px-6 py-4">
            <ul class="m-0 list-none p-0" data-donations-list aria-live="polite"></ul>
            <button type="button" class="mt-4 w-full rounded-[5px] border border-pink px-4 py-2.5 text-[13px] font-semibold text-pink hover:bg-blush disabled:opacity-50"
                data-donations-more hidden><?= t('wall.more') ?></button>
            <p class="m-0 text-[13px]" data-donations-status data-error="<?= t('wall.error') ?>" role="status"></p>
        </div>
    </dialog>
<?php endif ?>
