<?php
$label = 'padding:8px 16px 8px 0;border-bottom:1px solid #F5D7E6;color:#C43B75;white-space:nowrap;vertical-align:top;';
$value = 'padding:8px 0;border-bottom:1px solid #F5D7E6;word-break:break-word;overflow-wrap:break-word;';
?>
<?php snippet('emails/layout', [
    'footer' => t('contact.mail-footer', null, 'nl') . ' ' . $url,
    'preview' => Str::short(preg_replace('/\s+/u', ' ', $message), 140),
], slots: true) ?>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
    <tr>
        <td width="1" style="<?= $label ?>"><?= t('contact.name', null, 'nl') ?></td>
        <td style="<?= $value ?>font-weight:bold;"><?= esc($name) ?></td>
    </tr>
    <tr>
        <td width="1" style="<?= $label ?>"><?= t('contact.email', null, 'nl') ?></td>
        <td style="<?= $value ?>"><a href="mailto:<?= esc($email, 'attr') ?>" style="color:#C43B75;text-decoration:underline;"><?= esc($email) ?></a></td>
    </tr>
    <tr>
        <td width="1" style="<?= $label ?>"><?= t('contact.mail-language', null, 'nl') ?></td>
        <td style="<?= $value ?>"><?= esc($language) ?></td>
    </tr>
</table>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td bgcolor="#F5D7E6" style="padding:16px;background-color:#F5D7E6;border-left:4px solid #C43B75;word-break:break-word;overflow-wrap:break-word;">
            <?= nl2br(esc($message)) ?></td>
    </tr>
</table>
<?php endsnippet() ?>
