<?php if ($site->bankaccount()->isNotEmpty()): ?>
    <div
        class="<?= !empty($compact) ? 'mb-4 [&_.bank-label]:text-xs [&_.iban]:text-base' : 'mt-[26px] rounded-lg border border-white/50 bg-white/15 p-4 md:px-5 md:py-[18px]' ?>">
        <p class="bank-label mb-[6px] text-sm"><?= $site->banklabel()->esc() ?></p>
        <p class="iban text-[clamp(1rem,2vw,1.3rem)] font-bold tracking-[.015em] tabular-nums [overflow-wrap:anywhere]">
            <?= $site->bankaccount()->esc() ?></p>
    </div>
<?php endif ?>