<?php
// The visitor picks an amount here and continues on the donation page (name, email, message).
// With a Mollie key (plugins/tess-donations) the donation wall stands next to it.
$mollie = $site->mollieEnabled();
$featuredAmount = (int)$page->featured_amount()->or(50)->value();
?>
<section class="py-9 text-center md:py-16" id="help-tess">
    <div class="mx-auto w-[calc(100%-40px)] <?= $mollie ? 'grid max-w-[1120px] items-start gap-8 md:w-[calc(100%-64px)] lg:grid-cols-[minmax(0,1.1fr)_minmax(0,1fr)] lg:gap-12' : 'max-w-[660px]' ?>">
        <div>
            <?php snippet('components/heading', ['text' => $page->donation_title(), 'margin' => 'mb-[14px]']) ?>
            <p class="text-[15px]"><?= $page->donation_text()->esc() ?></p>
            <form class="mt-[26px] rounded-lg bg-white p-5 md:p-6" method="get" action="<?= page('doneren')?->url() ?>">
                <div class="mt-2 mb-[18px] grid grid-cols-[repeat(4,1fr)] gap-2 md:gap-[10px]" role="group" aria-label="<?= t('donation.amounts') ?>">
                    <?php foreach ([10, 25, 50, 100] as $amount): ?><button class="relative flex min-h-[62px] flex-col items-center justify-center gap-[3px] rounded-[5px] border border-pink bg-white px-[2px] py-[6px] font-bold text-pink aria-pressed:bg-pink aria-pressed:text-white max-md:text-[13px] motion-safe:transition-[background-color,color,transform] motion-safe:duration-[200ms,200ms,150ms] motion-safe:not-aria-pressed:hover:bg-[#F5D7E680] motion-safe:active:[transform:scale(.97)]" type="button" data-amount="<?= $amount ?>" aria-pressed="false"><span>€ <?= $amount ?></span><?php if ($amount === $featuredAmount): ?><span class="absolute top-0 left-1/2 block w-max max-w-[calc(100%+6px)] rounded-[3px] bg-blush px-1 py-[2px] text-center text-[9px] leading-[1.2] font-medium text-ink [transform:translate(-50%,-50%)]"><?= t('donation.most-chosen') ?></span><?php endif ?></button><?php endforeach ?>
                </div>
                <label class="mb-[7px] block text-left text-[13px] text-pink" for="amount"><?= t('donation.other-amount') ?></label>
                <input class="mb-[18px] block min-h-[46px] w-full rounded-[5px] border border-pink/80 bg-white px-[14px] py-[10px] text-ink" id="amount" name="amount" type="number" required
                    min="<?= option('tess.donations.min') ?>" max="<?= option('tess.donations.max') ?>" step="1" inputmode="numeric" placeholder="<?= t('donation.amount') ?>">
                <?php snippet('components/button', ['donation' => true, 'text' => t('donation.donate'), 'size' => 'block', 'attrs' => ['type' => 'submit']]) ?>
                <p class="mt-[14px] mb-0 text-[11px]"><?= t('donation.next-step') ?></p>
            </form>
            <?php snippet('components/donation-alternatives') ?>
        </div>
        <?php if ($mollie): ?><?php snippet('home/donation-wall') ?><?php endif ?>
    </div>
</section>
