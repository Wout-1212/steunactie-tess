</main>
<?php
$support = array_filter([
    t('footer.fundraiser') => $site->fundraiser()->value(),
    'GoFundMe' => $site->gofundme()->value(),
]);
// Display only the enterprise number from the existing combined details field.
preg_match('/\b(?:BE\s*)?\d{4}[.\s]?\d{3}[.\s]?\d{3}\b/iu', $site->organisationdetails()->value(), $organisationNumber);
$footerEmail = $site->contactemail()->or('contact@samenvoortess.be')->value();
$isQuietPage = !$page->isHomePage() && $page->intendedTemplate()->name() !== 'disclaimer';
$link = 'text-ink underline-offset-3 hover:text-pink hover:underline';
$fade = 'motion-safe:transition-[color,background-color] motion-safe:duration-200';
?>
<footer class="pt-5 pb-[26px] text-left text-ink max-md:text-center <?= $isQuietPage ? 'bg-background-middle' : 'bg-background-bottom' ?>">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] md:w-[calc(100%-64px)]">
        <div class="grid grid-cols-1 items-start gap-[26px] border-t border-divider pt-8 md:grid-cols-2 md:gap-x-10 md:gap-y-7 md:pt-10 2xl:grid-cols-[1.4fr_1.45fr_.85fr_1.2fr] 2xl:gap-10 [&>*]:min-w-0">
            <div>
                <a class="mx-auto mb-7 block w-max text-[24px] font-bold text-pink underline-offset-3 hover:underline motion-safe:transition-[color,background-color] motion-safe:duration-200 md:mb-6 2xl:mx-0" href="<?= $site->url() ?>"><?php if ($logo = $site->logo()->toFile()): ?><img class="block h-auto w-[160px] [filter:drop-shadow(0_0_.7px_#27222A)] md:w-[180px]" src="<?= $logo->url() ?>" alt="<?= $site->title()->esc() ?>" loading="lazy"><?php else: ?><img class="block h-auto w-[160px] [filter:drop-shadow(0_0_.7px_#27222A)] md:w-[180px]" src="<?= url('assets/images/logo.png') ?>" alt="<?= $site->title()->esc() ?>" loading="lazy"><?php endif ?></a>
                <div class="grid gap-3 max-md:justify-center">
                <?php foreach (['instagram' => 'footer.instagram', 'instagramtess' => 'footer.instagram-tess'] as $field => $label): ?>
                    <?php if ($site->$field()->isNotEmpty()): ?><a class="flex items-center gap-2 text-[13px] wrap-anywhere max-md:justify-center <?= $link ?> <?= $fade ?>" href="<?= $site->$field()->esc() ?>"><?php snippet('components/icon', ['name' => 'instagram', 'class' => 'size-[14px] shrink-0 text-pink']) ?><span class="sr-only"><?= t($label) ?>: </span>@<?= esc(basename(rtrim(parse_url($site->$field()->value(), PHP_URL_PATH) ?? '', '/'))) ?></a><?php endif ?>
                <?php endforeach ?>
                </div>
            </div>
            <nav aria-label="<?= t('footer.menu') ?>">
                <h2 class="mb-3 text-[15px]"><?= t('footer.menu') ?></h2>
                <ul class="mx-auto grid w-fit max-w-full grid-cols-2 items-center gap-x-6 gap-y-2 text-center md:mx-0 md:w-auto md:max-w-none md:items-stretch md:gap-x-8 md:text-left">
                    <?php foreach (['home', 'haar-verhaal', 'behandelingsplan'] as $slug): ?>
                        <?php if ($item = page($slug)): ?><li class="text-[13px]"><a class="<?= $link ?> motion-safe:link-underline" href="<?= $item->url() ?>"><?= $item->footerlabel()->or($item->navtitle())->or($item->title())->esc() ?></a></li><?php endif ?>
                    <?php endforeach ?>
                    <li class="text-[13px]"><a class="<?= $link ?> motion-safe:link-underline" href="<?= page('home')->url() ?>#help-tess"><?= t('cta.help') ?></a></li>
                    <?php if ($item = page('faq')): ?><li class="text-[13px]"><a class="<?= $link ?> motion-safe:link-underline" href="<?= $item->url() ?>"><?= $item->footerlabel()->or($item->navtitle())->or($item->title())->esc() ?></a></li><?php endif ?>
                    <li class="text-[13px]"><a class="<?= $link ?> motion-safe:link-underline" href="<?= page('home')->url() ?>#contact"><?= t('nav.contact') ?></a></li>
                </ul>
            </nav>
            <div>
                <h2 class="mb-3 text-[15px]"><?= t('footer.support') ?></h2>
                <ul class="grid gap-2">
                    <?php foreach ($support as $label => $url): ?><li class="text-[13px]"><a class="underline <?= $link ?> <?= $fade ?>" href="<?= esc($url, 'attr') ?>"><?= esc($label) ?></a></li><?php endforeach ?>
                    <?php if ($site->bankaccount()->isNotEmpty()): ?><li class="text-[13px]"><a class="underline <?= $link ?> <?= $fade ?>" href="<?= page('home')->url() ?>#bank-transfer"><?= t('footer.transfer') ?></a></li><?php endif ?>
                </ul>
            </div>
            <div>
                <h2 class="mb-3 text-[15px]"><?= t('footer.about') ?></h2>
                <address class="text-[13px] leading-[1.9] not-italic wrap-anywhere">
                    <?= $site->organisation()->or($site->title())->esc() ?><br>
                    <?= isset($organisationNumber[0]) ? esc($organisationNumber[0]) : t('footer.number-placeholder') ?>
                    <?php if ($site->bankaccount()->isNotEmpty()): ?><br>IBAN <?= $site->bankaccount()->esc() ?><?php endif ?>
                    <br><a class="<?= $link ?> <?= $fade ?>" href="mailto:<?= esc($footerEmail, 'attr') ?>"><?= esc($footerEmail) ?></a>
                </address>
            </div>
        </div>
        <div class="mt-[30px] flex flex-col items-center justify-between gap-3 border-t border-divider pt-[22px] text-center md:mt-10 md:flex-row md:gap-x-7 md:gap-y-4 md:text-left">
            <p class="m-0 text-[13px]">© <?= date('Y') ?> <?= $site->title()->esc() ?> · <?= t('footer.credit') ?></p>
            <?php if ($disclaimer = page('disclaimer')): ?><a class="shrink-0 text-[13px] underline <?= $link ?> <?= $fade ?>" href="<?= $disclaimer->url() ?>"><?= t('footer.disclaimer') ?></a><?php endif ?>
        </div>
    </div>
</footer>
</body>
</html>
