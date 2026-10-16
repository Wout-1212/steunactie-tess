<section class="home-donate scroll-mt-28" id="help-tess">
    <div class="donation-shell">
        <h2><?= $page->donation_title()->esc() ?></h2>
        <p><?= $page->donation_text()->esc() ?></p>
        <div class="amount-picker" role="group" aria-label="<?= t('donation.amounts') ?>">
            <?php $featuredAmount = (int)$page->featured_amount()->or(50)->value(); ?>
            <?php foreach ([10, 25, 50, 100] as $amount): ?><button type="button" data-amount="<?= $amount ?>" aria-pressed="false"><span>€ <?= $amount ?></span><?php if ($amount === $featuredAmount): ?><span class="amount-badge"><?= t('donation.most-chosen') ?></span><?php endif ?></button><?php endforeach ?>
        </div>
        <label for="amount"><?= t('donation.other-amount') ?></label>
        <input id="amount" type="number" min="1" step="1" inputmode="numeric" placeholder="<?= t('donation.amount') ?>">
        <?php if ($site->fundraiser()->isNotEmpty()): ?>
            <a class="design-button donation-primary" href="<?= $site->fundraiser()->esc() ?>"><?= t('donation.fundraiser') ?></a>
            <p class="donation-note"><?= t('donation.fundraiser-note') ?></p>
        <?php endif ?>
        <p class="donation-alternatives-label"><?= t('donation.alternatives') ?></p>
        <?php snippet('bank-account') ?>
        <?php foreach (['fundraiser' => 'Steunactie', 'gofundme' => 'GoFundMe'] as $field => $label): ?>
            <?php if ($site->$field()->isNotEmpty()): ?>
                <div class="donation-option"><div><h3><?= tt('donation.via', ['platform' => $label]) ?></h3><p><?= t('donation.' . $field . '-description') ?></p></div><a class="design-button design-button-outline" href="<?= $site->$field()->esc() ?>"><?= tt('donation.to-platform', ['platform' => $label]) ?></a></div>
            <?php endif ?>
        <?php endforeach ?>
    </div>
</section>
