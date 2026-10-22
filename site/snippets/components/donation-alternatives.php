<?php
// Other ways to give: bank transfer and the external fundraisers. Below the donation block on the
// homepage, and on the donation page while online donations are not available.
?>
<p class="mt-7 mb-[18px] text-[13px]"><?= t('donation.alternatives') ?></p>
<?php snippet('components/bank-account') ?>
<?php foreach (['fundraiser' => 'Steunactie', 'gofundme' => 'GoFundMe'] as $platform => $platformLabel): ?>
    <?php if ($site->$platform()->isNotEmpty()): ?>
        <div class="mb-3 flex flex-wrap items-center justify-between gap-3 rounded-[6px] bg-white p-[14px] text-left text-ink md:gap-x-5 md:gap-y-3 md:px-[18px] md:py-4 motion-safe:transition-shadow motion-safe:duration-250 motion-safe:hover:shadow-card"><div><h3 class="mb-1 text-[13px] leading-[1.35] font-bold text-ink"><?= tt('donation.via', ['platform' => $platformLabel]) ?></h3><p class="m-0 text-[12px]"><?= t('donation.' . $platform . '-description') ?></p></div><?php snippet('components/button', ['donation' => true, 'href' => $site->$platform()->value(), 'text' => tt('donation.to-platform', ['platform' => $platformLabel]), 'variant' => 'outline', 'size' => 'small']) ?></div>
    <?php endif ?>
<?php endforeach ?>
