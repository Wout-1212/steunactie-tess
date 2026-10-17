<?php if ($site->bankaccount()->isNotEmpty()): ?>
    <div id="bank-transfer" data-bank-account class="mb-3 flex scroll-mt-[calc(var(--header-height,78px)+20px)] flex-wrap items-center justify-between gap-3 rounded-[6px] bg-white p-[14px] text-left text-ink md:gap-x-5 md:gap-y-3 md:px-[18px] md:py-4 motion-safe:transition-shadow motion-safe:duration-250 motion-safe:hover:shadow-card">
        <div><p class="mb-1 text-[13px] font-bold text-ink"><?= t('bank.label') ?></p><p class="m-0 text-[14px] font-semibold tracking-[.025em] wrap-anywhere"><?= $site->bankaccount()->esc() ?></p></div>
        <?php snippet('components/button', ['text' => t('bank.copy'), 'variant' => 'outline', 'size' => 'small', 'attrs' => ['data-copy-iban' => $site->bankaccount()->value(), 'data-copied' => t('bank.copied'), 'data-copy-failed' => t('bank.copy-failed')]]) ?>
        <p class="m-0 basis-full text-[12px] text-pink empty:hidden" data-bank-status role="status"></p>
    </div>
<?php endif ?>
