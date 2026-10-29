<?php
// French puts a (non-breaking) space before a colon.
$colon = $language === 'fr' ? "\u{00A0}:" : ':';
?>
<?= $firstName !== ''
    ? tt('donation.mail-greeting', null, ['name' => $firstName], $language)
    : t('donation.mail-greeting-anonymous', null, $language) ?>


<?= tt('donation.mail-thanks', null, ['amount' => $amount], $language) ?>


<?= t('donation.mail-summary', null, $language) ?>

<?php foreach (['amount' => $amount, 'date' => $date, 'method' => $method, 'reference' => $reference] as $key => $text): ?>
<?= t('donation.mail-' . $key, null, $language) ?><?= $colon ?> <?= $text ?>

<?php endforeach ?>

<?php if ($message !== ''): ?>
<?= t($held ? 'donation.mail-message-held' : 'donation.mail-message', null, $language) ?>

<?= $message ?>


<?php endif ?>
<?= t('donation.mail-impact-title', null, $language) ?>

<?php if ($milestone): ?>
<?= tt('donation.mail-impact', null, ['amount' => $milestone['amount'], 'goal' => $milestone['label']], $language) ?>

<?php endif ?>
<?= t('donation.mail-progress', null, $language) ?><?= $colon ?> <?= $progressUrl ?>


<?= t('donation.mail-share-title', null, $language) ?>

<?= t('donation.mail-share-text', null, $language) ?>

<?= t('donation.mail-share-button', null, $language) ?><?= $colon ?> <?= $url ?>


<?= tt('donation.mail-instagram', null, ['handle' => $instagramHandle], $language) ?> (<?= $instagram ?>)

<?= t('donation.mail-closing', null, $language) ?>

<?= t('donation.mail-signature', null, $language) ?>


--
<?= t('donation.mail-questions', null, $language) ?> <?= $contact ?>

<?php if ($privacyUrl): ?>
<?= t('donation.mail-privacy', null, $language) ?><?= $colon ?> <?= $privacyUrl ?>

<?php endif ?>
<?php if ($donationsUrl): ?>
<?= t('donation.mail-donation-statement', null, $language) ?><?= $colon ?> <?= $donationsUrl ?>

<?php endif ?>
<?= tt('donation.mail-reason', null, ['domain' => $domain], $language) ?>
