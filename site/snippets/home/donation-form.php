<section class="scroll-mt-28 py-8 md:py-[42px] bg-pink text-donation-ink [&_h2]:text-donation-ink" id="help-tess">
    <div
        class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)] grid grid-cols-1 items-center gap-[26px] md:grid-cols-2 md:gap-8 lg:gap-16">
        <div>
            <h2><?= $page->donation_title()->esc() ?></h2>
            <p><?= $page->donation_text()->esc() ?></p><?php snippet('bank-account') ?>
        </div>
        <div
            class="[&_label]:mb-[6px] [&_label]:block [&_label]:text-sm [&_input]:mb-3 [&_input]:min-h-12 [&_input]:w-full [&_input]:rounded-[5px] [&_input]:border-0 [&_input]:bg-white [&_input]:px-[14px] [&_input]:py-[9px] [&_input]:text-ink">
            <div class="mb-[15px] grid grid-cols-2 gap-3 xs:grid-cols-4 [&_button]:min-h-12 [&_button]:rounded-[5px] [&_button]:border-2 [&_button]:border-white [&_button]:bg-white [&_button]:text-pink-dark [&_button[aria-pressed=true]]:bg-pink-dark [&_button[aria-pressed=true]]:text-white"
                role="group" aria-label="<?= t('donation.amounts') ?>">
                <?php foreach ([10, 25, 50, 100] as $amount): ?><button type="button" data-amount="<?= $amount ?>"
                        aria-pressed="false">€ <?= $amount ?></button><?php endforeach ?></div><label
                for="amount"><?= t('donation.other-amount') ?></label><input id="amount" type="number" min="1"
                step="1" inputmode="numeric" class="text-base" placeholder="<?= t('donation.amount') ?>">
            <?php if ($site->fundraiser()->isNotEmpty()): ?><a
                    class="inline-flex min-h-11 items-center justify-center rounded-[5px] border px-[21px] py-[11px] text-sm font-bold border-transparent bg-white text-pink-dark hover:bg-pink-dark hover:text-white w-full"
                    href="<?= $site->fundraiser()->esc() ?>"><?= t('donation.fundraiser') ?></a>
                <p class="mt-3 text-xs"><?= t('donation.fundraiser-note') ?>
                </p><?php endif ?>
        </div>
    </div>
</section>
