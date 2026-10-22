<?php $label = 'padding:4px 16px 4px 0;color:#6E6672;white-space:nowrap;vertical-align:top;' ?>
<?php snippet('emails/layout', ['footer' => t('donation.mail-footer', null, $language) . ' ' . $url], slots: true) ?>
<p style="margin:0 0 16px;"><?= tt('donation.mail-greeting', null, ['name' => esc($name)], $language) ?></p>
<p style="margin:0 0 20px;"><?= t('donation.mail-thanks', null, $language) ?></p>
<table role="presentation" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
    <tr>
        <td style="<?= $label ?>"><?= t('donation.amount', null, $language) ?></td>
        <td style="padding:4px 0;font-weight:bold;color:#C43B75;"><?= esc($amount) ?></td>
    </tr>
    <tr>
        <td style="<?= $label ?>"><?= t('donation.mail-date', null, $language) ?></td>
        <td style="padding:4px 0;"><?= esc($date) ?></td>
    </tr>
    <tr>
        <td style="<?= $label ?>"><?= t('donation.mail-reference', null, $language) ?></td>
        <td style="padding:4px 0;"><?= esc($reference) ?></td>
    </tr>
</table>
<?php if ($message !== ''): ?>
    <p style="margin:0 0 8px;color:#6E6672;"><?= t($held ? 'donation.mail-message-held' : 'donation.mail-message', null, $language) ?></p>
    <div style="padding:16px;margin-bottom:20px;background-color:#FBEFF5;border-left:4px solid #C43B75;border-radius:4px;">
        <?= nl2br(esc($message)) ?></div>
<?php endif ?>
<p style="margin:0;"><?= t('donation.mail-closing', null, $language) ?></p>
<?php endsnippet() ?>
