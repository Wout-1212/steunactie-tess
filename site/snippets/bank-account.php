<?php if ($site->bankaccount()->isNotEmpty()): ?>
    <div id="bank-transfer" class="bank-account <?= !empty($compact) ? 'bank-account-compact' : '' ?>">
        <div><p class="bank-label"><?= t('bank.label') ?></p><p class="iban"><?= $site->bankaccount()->esc() ?></p></div>
        <button type="button" class="design-button design-button-outline" data-copy-iban="<?= $site->bankaccount()->esc() ?>" data-copied="<?= t('bank.copied') ?>" data-copy-failed="<?= t('bank.copy-failed') ?>"><?= t('bank.copy') ?></button>
        <p class="bank-copy-status" data-bank-status role="status"></p>
    </div>
<?php endif ?>
