<section class="py-9 text-center md:py-16" id="help-tess">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[660px]">
        <?php snippet('components/heading', ['text' => $page->donation_title(), 'margin' => 'mb-[14px]']) ?>
        <p class="text-[14px]"><?= $page->donation_text()->esc() ?></p>
        <div class="mt-[26px] mb-[18px] grid grid-cols-[repeat(4,1fr)] gap-2 md:gap-[10px]" role="group" aria-label="<?= t('donation.amounts') ?>">
            <?php $featuredAmount = (int)$page->featured_amount()->or(50)->value(); ?>
            <?php foreach ([10, 25, 50, 100] as $amount): ?><button class="relative flex min-h-[62px] flex-col items-center justify-center gap-[3px] rounded-[5px] border border-pink bg-white px-[2px] py-[6px] font-bold text-pink aria-pressed:bg-pink aria-pressed:text-white max-md:text-[13px] motion-safe:transition-[background-color,color,transform] motion-safe:duration-[200ms,200ms,150ms] motion-safe:not-aria-pressed:hover:bg-[#F5D7E680] motion-safe:active:[transform:scale(.97)]" type="button" data-amount="<?= $amount ?>" aria-pressed="false"><span>€ <?= $amount ?></span><?php if ($amount === $featuredAmount): ?><span class="absolute top-0 left-1/2 block w-max max-w-[calc(100%+6px)] rounded-[3px] bg-blush px-1 py-[2px] text-center text-[9px] leading-[1.2] font-medium text-ink [transform:translate(-50%,-50%)]"><?= t('donation.most-chosen') ?></span><?php endif ?></button><?php endforeach ?>
        </div>
        <label class="mb-[7px] block text-left text-[13px] text-pink" for="amount"><?= t('donation.other-amount') ?></label>
        <input class="mb-[18px] block min-h-[46px] w-full rounded-[5px] border border-blush bg-white px-[14px] py-[10px] text-ink" id="amount" type="number" min="1" step="1" inputmode="numeric" placeholder="<?= t('donation.amount') ?>">
        <?php if ($site->fundraiser()->isNotEmpty()): ?>
            <?php snippet('components/button', ['donation' => true, 'href' => $site->fundraiser()->value(), 'text' => t('donation.fundraiser'), 'size' => 'block']) ?>
            <p class="mt-[14px] mb-[26px] text-[11px]"><?= t('donation.fundraiser-note') ?></p>
        <?php endif ?>
        <p class="mt-7 mb-[18px] text-[13px]"><?= t('donation.alternatives') ?></p>
        <?php snippet('components/bank-account') ?>
        <?php foreach (['fundraiser' => 'Steunactie', 'gofundme' => 'GoFundMe'] as $field => $label): ?>
            <?php if ($site->$field()->isNotEmpty()): ?>
                <div class="mb-3 flex flex-wrap items-center justify-between gap-3 rounded-[6px] bg-white p-[14px] text-left text-ink md:gap-x-5 md:gap-y-3 md:px-[18px] md:py-4 motion-safe:transition-shadow motion-safe:duration-250 motion-safe:hover:shadow-card"><div><h3 class="mb-1 text-[13px] leading-[1.35] font-bold text-ink"><?= tt('donation.via', ['platform' => $label]) ?></h3><p class="m-0 text-[12px]"><?= t('donation.' . $field . '-description') ?></p></div><?php snippet('components/button', ['donation' => true, 'href' => $site->$field()->value(), 'text' => tt('donation.to-platform', ['platform' => $label]), 'variant' => 'outline', 'size' => 'small']) ?></div>
            <?php endif ?>
        <?php endforeach ?>
    </div>
</section>
