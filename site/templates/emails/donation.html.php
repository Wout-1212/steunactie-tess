<?php snippet('emails/layout', [
    'footer' => t('donation.mail-footer', null, $language) . ' ' . $url,
    'preview' => tt('donation.mail-thanks', null, ['amount' => $amount], $language),
    'lang' => $language,
], slots: true) ?>
<p style="margin:0 0 16px;"><?= tt('donation.mail-greeting', null, ['name' => esc($name)], $language) ?></p>
<p style="margin:0 0 20px;"><?= tt('donation.mail-thanks', null, ['amount' => esc($amount)], $language) ?></p>
<?php if ($message !== ''): ?>
    <p style="margin:0 0 8px;"><?= t($held ? 'donation.mail-message-held' : 'donation.mail-message', null, $language) ?></p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td bgcolor="#F5D7E6" style="padding:16px;background-color:#F5D7E6;border-left:4px solid #C43B75;word-break:break-word;overflow-wrap:break-word;">
                <?= nl2br(esc($message)) ?></td>
        </tr>
    </table>
<?php endif ?>
<p style="margin:0;"><?= t('donation.mail-closing', null, $language) ?><br>
    <?= t('donation.mail-signature', null, $language) ?></p>
<?php endsnippet() ?>
