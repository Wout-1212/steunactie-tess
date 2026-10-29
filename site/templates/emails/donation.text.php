<?= tt('donation.mail-greeting', null, ['name' => $name], $language) ?>


<?= tt('donation.mail-thanks', null, ['amount' => $amount], $language) ?>

<?php if ($message !== ''): ?>

<?= t($held ? 'donation.mail-message-held' : 'donation.mail-message', null, $language) ?>

<?= $message ?>

<?php endif ?>

<?= t('donation.mail-closing', null, $language) ?>

<?= t('donation.mail-signature', null, $language) ?>


--
<?= t('donation.mail-footer', null, $language) ?> <?= $url ?>
