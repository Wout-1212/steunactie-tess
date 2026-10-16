</main>
<?php
$support = array_filter([
    t('footer.fundraiser') => $site->fundraiser()->value(),
    'GoFundMe' => $site->gofundme()->value(),
]);
// Display only the enterprise number from the existing combined details field.
preg_match('/\b(?:BE\s*)?\d{4}[.\s]?\d{3}[.\s]?\d{3}\b/iu', $site->organisationdetails()->value(), $organisationNumber);
$footerEmail = $site->contactemail()->or('contact@samenvoortess.be')->value();
?>
<footer class="site-footer tess-footer [&_a:hover]:underline">
    <div class="page-shell">
        <div class="footer-columns">
            <div class="footer-identity">
                <a class="footer-brand" href="<?= $site->url() ?>"><?php if ($logo = $site->logo()->toFile()): ?><img src="<?= $logo->url() ?>" alt="<?= $site->title()->esc() ?>" loading="lazy"><?php else: ?><img src="<?= url('assets/images/logo.png') ?>" alt="<?= $site->title()->esc() ?>" loading="lazy"><?php endif ?></a>
                <div class="footer-social">
                <?php foreach (['instagram' => 'footer.instagram', 'instagramtess' => 'footer.instagram-tess'] as $field => $label): ?>
                    <?php if ($site->$field()->isNotEmpty()): ?><a href="<?= $site->$field()->esc() ?>"><?php snippet('icon', ['name' => 'instagram', 'class' => 'footer-instagram-icon']) ?><span class="sr-only"><?= t($label) ?>: </span>@<?= esc(basename(rtrim(parse_url($site->$field()->value(), PHP_URL_PATH) ?? '', '/'))) ?></a><?php endif ?>
                <?php endforeach ?>
                </div>
            </div>
            <nav aria-label="<?= t('footer.menu') ?>">
                <h2><?= t('footer.menu') ?></h2>
                <ul class="footer-menu">
                    <?php foreach (['home', 'haar-verhaal', 'behandelingsplan'] as $slug): ?>
                        <?php if ($item = page($slug)): ?><li><a href="<?= $item->url() ?>"><?= $item->footerlabel()->or($item->navtitle())->or($item->title())->esc() ?></a></li><?php endif ?>
                    <?php endforeach ?>
                    <li><a href="<?= page('home')->url() ?>#help-tess"><?= t('cta.help') ?></a></li>
                    <?php if ($item = page('faq')): ?><li><a href="<?= $item->url() ?>"><?= $item->footerlabel()->or($item->navtitle())->or($item->title())->esc() ?></a></li><?php endif ?>
                    <li><a href="<?= page('home')->url() ?>#contact"><?= t('nav.contact') ?></a></li>
                </ul>
            </nav>
            <div>
                <h2><?= t('footer.support') ?></h2>
                <ul class="footer-support">
                    <?php foreach ($support as $label => $url): ?><li><a href="<?= esc($url, 'attr') ?>"><?= esc($label) ?></a></li><?php endforeach ?>
                    <?php if ($site->bankaccount()->isNotEmpty()): ?><li><a href="<?= page('home')->url() ?>#bank-transfer"><?= t('footer.transfer') ?></a></li><?php endif ?>
                </ul>
            </div>
            <div>
                <h2><?= t('footer.about') ?></h2>
                <address>
                    <?= $site->organisation()->or($site->title())->esc() ?><br>
                    <?= isset($organisationNumber[0]) ? esc($organisationNumber[0]) : t('footer.number-placeholder') ?>
                    <?php if ($site->bankaccount()->isNotEmpty()): ?><br>IBAN <?= $site->bankaccount()->esc() ?><?php endif ?>
                    <br><a href="mailto:<?= esc($footerEmail, 'attr') ?>"><?= esc($footerEmail) ?></a>
                </address>
            </div>
        </div>
        <div class="footer-legal">
            <p>© <?= date('Y') ?> <?= $site->title()->esc() ?> · <?= t('footer.credit') ?></p>
            <?php if ($disclaimer = page('disclaimer')): ?><a href="<?= $disclaimer->url() ?>"><?= t('footer.disclaimer') ?></a><?php endif ?>
        </div>
    </div>
</footer>
</body>
</html>
