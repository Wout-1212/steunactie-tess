<?= tt('donation.mail-greeting', null, ['name' => $name], $language) ?>


<?= t('donation.mail-thanks', null, $language) ?>


<?= t('donation.amount', null, $language) ?>: <?= $amount ?>

<?= t('donation.mail-date', null, $language) ?>: <?= $date ?>

<?= t('donation.mail-reference', null, $language) ?>: <?= $reference ?>

<?php if ($message !== ''): ?>

<?= t($held ? 'donation.mail-message-held' : 'donation.mail-message', null, $language) ?>

<?= $message ?>

<?php endif ?>

<?= t('donation.mail-closing', null, $language) ?>


--
<?= t('donation.mail-footer', null, $language) ?> <?= $url ?>
