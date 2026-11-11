<?php /* Data from site/controllers/thanks.php: $state ('paid', 'pending' or 'failed'), $donation, $heading, $text, $tabTitle. */ ?>
<?php snippet('layout/header', ['noindex' => true, 'title' => $tabTitle]) ?>
<section class="min-h-[45vh] py-[42px] md:py-[72px]">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[760px] text-center motion-safe:animate-fade-up"<?= $state === 'paid' ? ' data-confetti="' . esc($donation->reference, 'attr') . '"' : '' ?>>
        <?php if ($state === 'paid'): ?><?php snippet('components/icon', ['name' => 'heart', 'class' => 'mx-auto mb-4 size-10 text-pink']) ?><?php endif ?>
        <h1 class="text-[clamp(30px,4vw,48px)]"><?= $heading ?></h1>
        <p class="mx-auto max-w-[56ch] text-[16px] leading-[1.7] md:text-[17px]"><?= $text ?></p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <?php if ($state === 'failed'): ?>
                <?php snippet('components/button', ['donation' => true, 'href' => page('doneren')?->url() . ($donation ? '?amount=' . (int) ($donation->amount_cents / 100) : ''), 'text' => t('donation.try-again')]) ?>
            <?php endif ?>
            <?php snippet('components/button', ['href' => page('home')->url(), 'text' => t('donation.back-home'), 'variant' => $state === 'failed' ? 'outline' : 'solid']) ?>
        </div>
    </div>
</section>
<?php snippet('layout/footer') ?>
