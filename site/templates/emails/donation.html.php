<?php
// Data from tessDonationMailData() in plugins/tess-donations/lib/mail.php. Pink text only on white:
// on the blush box it is too faint, so text there stays ink.
$title = 'margin:0 0 8px;font-size:17px;line-height:1.3;font-weight:bold;color:#C43B75;';
$link = 'color:#C43B75;font-weight:bold;text-decoration:underline;';
$footerLink = 'color:#27222A;text-decoration:underline;';
$label = 'padding:4px 12px 4px 0;vertical-align:top;';
$value = 'padding:4px 0;font-weight:bold;vertical-align:top;word-break:break-word;overflow-wrap:break-word;';
?>
<?php snippet('emails/layout', [
    'preview' => t('donation.mail-preheader', null, $language),
    'lang' => $language,
    'logo' => true,
], slots: true) ?>
<?php slot() ?>
<p style="margin:0 0 16px;"><?= $firstName !== ''
    ? tt('donation.mail-greeting', null, ['name' => esc($firstName)], $language)
    : t('donation.mail-greeting-anonymous', null, $language) ?></p>
<p style="margin:0 0 24px;"><?= tt('donation.mail-thanks', null, ['amount' => '<strong>' . esc($amount) . '</strong>'], $language) ?></p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
    <tr>
        <td bgcolor="#F5D7E6" style="padding:20px;background-color:#F5D7E6;border-radius:8px;color:#27222A;">
            <h2 style="margin:0 0 10px;font-size:13px;line-height:1.3;font-weight:bold;letter-spacing:.1em;text-transform:uppercase;color:#27222A;">
                <?= t('donation.mail-summary', null, $language) ?></h2>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <?php foreach (['amount' => $amount, 'date' => $date, 'method' => $method, 'reference' => $reference] as $key => $text): ?>
                    <tr>
                        <?php /* A share of the width rather than nowrap, so on phones a long label wraps instead of the reference. */ ?>
                        <td width="40%" style="<?= $label ?>"><?= t('donation.mail-' . $key, null, $language) ?></td>
                        <td style="<?= $value ?>"><?= esc($text) ?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        </td>
    </tr>
</table>

<?php if ($message !== ''): ?>
    <p style="margin:0 0 8px;"><?= t($held ? 'donation.mail-message-held' : 'donation.mail-message', null, $language) ?></p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
        <tr>
            <td style="padding:4px 0 4px 16px;border-left:4px solid #C43B75;font-style:italic;word-break:break-word;overflow-wrap:break-word;">
                <?= nl2br(esc($message)) ?></td>
        </tr>
    </table>
<?php endif ?>

<h2 style="<?= $title ?>"><?= t('donation.mail-impact-title', null, $language) ?></h2>
<?php if ($milestone): ?>
    <p style="margin:0 0 8px;"><?= tt('donation.mail-impact', null, [
        'amount' => '<strong>' . esc($milestone['amount']) . '</strong>',
        'goal' => '<strong>' . esc($milestone['label']) . '</strong>',
    ], $language) ?></p>
<?php endif ?>
<p style="margin:0 0 28px;"><a href="<?= esc($progressUrl, 'attr') ?>" style="<?= $link ?>"><?= t('donation.mail-progress', null, $language) ?></a></p>

<h2 style="<?= $title ?>"><?= t('donation.mail-share-title', null, $language) ?></h2>
<p style="margin:0 0 16px;"><?= t('donation.mail-share-text', null, $language) ?></p>
<?php /* Bulletproof button: the cell carries the colour, so it shows even where the link's padding is ignored
    (Outlook on Windows, which gets the padding through mso-padding-alt instead). */ ?>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 12px;">
    <tr>
        <td align="center" bgcolor="#C43B75" style="border-radius:6px;background-color:#C43B75;mso-padding-alt:14px 28px;">
            <a href="<?= esc($share['whatsapp'], 'attr') ?>" target="_blank"
                style="display:inline-block;padding:14px 28px;border-radius:6px;font-family:Montserrat,Arial,Helvetica,sans-serif;font-size:16px;line-height:1.2;font-weight:bold;color:#FFFFFF;text-decoration:none;"><?= t('donation.mail-share-button', null, $language) ?></a>
        </td>
    </tr>
</table>
<p style="margin:0 0 28px;font-size:13px;"><?= t('donation.mail-share-other', null, $language) ?>
    <a href="<?= esc($share['facebook'], 'attr') ?>" target="_blank" style="<?= $link ?>">Facebook</a> ·
    <a href="<?= esc($share['email'], 'attr') ?>" style="<?= $link ?>"><?= t('share.email', null, $language) ?></a></p>

<p style="margin:0 0 28px;"><?= tt('donation.mail-instagram', null, [
    'handle' => '<a href="' . esc($instagram, 'attr') . '" target="_blank" style="' . $link . '">' . esc($instagramHandle) . '</a>',
], $language) ?></p>

<p style="margin:0;"><?= t('donation.mail-closing', null, $language) ?><br>
    <?= t('donation.mail-signature', null, $language) ?></p>
<?php endslot() ?>

<?php slot('footer') ?>
<p style="margin:0 0 8px;"><?= t('donation.mail-questions', null, $language) ?>
    <a href="mailto:<?= esc($contact, 'attr') ?>" style="<?= $footerLink ?>"><?= esc($contact) ?></a>
    <?php if ($privacyUrl): ?> · <a href="<?= esc($privacyUrl, 'attr') ?>" style="<?= $footerLink ?>"><?= t('donation.mail-privacy', null, $language) ?></a><?php endif ?>
    <?php if ($donationsUrl): ?> · <a href="<?= esc($donationsUrl, 'attr') ?>" style="<?= $footerLink ?>"><?= t('donation.mail-donation-statement', null, $language) ?></a><?php endif ?></p>
<p style="margin:0;"><?= tt('donation.mail-reason', null, [
    'domain' => '<a href="' . esc($url, 'attr') . '" style="' . $footerLink . '">' . esc($domain) . '</a>',
], $language) ?></p>
<?php endslot() ?>
<?php endsnippet() ?>
