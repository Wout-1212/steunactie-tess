<?php $label = 'padding:4px 16px 4px 0;color:#6E6672;white-space:nowrap;vertical-align:top;' ?>
<?php snippet('emails/layout', ['footer' => t('contact.mail-footer', null, 'nl') . ' ' . $url], slots: true) ?>
<table role="presentation" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
    <tr>
        <td style="<?= $label ?>"><?= t('contact.name', null, 'nl') ?></td>
        <td style="padding:4px 0;font-weight:bold;"><?= esc($name) ?></td>
    </tr>
    <tr>
        <td style="<?= $label ?>"><?= t('contact.email', null, 'nl') ?></td>
        <td style="padding:4px 0;"><a href="mailto:<?= esc($email, 'attr') ?>" style="color:#C43B75;"><?= esc($email) ?></a></td>
    </tr>
    <tr>
        <td style="<?= $label ?>"><?= t('contact.mail-language', null, 'nl') ?></td>
        <td style="padding:4px 0;"><?= esc($language) ?></td>
    </tr>
</table>
<div style="padding:16px;background-color:#FBEFF5;border-left:4px solid #C43B75;border-radius:4px;">
    <?= nl2br(esc($message)) ?></div>
<?php endsnippet() ?>
