</main>
<?php
$support = array_filter([
    t('footer.fundraiser') => $site->fundraiser()->value(),
    'GoFundMe' => $site->gofundme()->value(),
]);
?>
<footer class="bg-blush pt-10 pb-6 text-center text-ink md:pt-14 md:text-left [&_a:hover]:underline">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)]">
        <div
            class="grid gap-y-5 md:grid-cols-2 md:gap-x-10 md:gap-y-10 min-[72rem]:grid-cols-[repeat(4,auto)] min-[72rem]:justify-between [&_h2]:mb-2 [&_h2]:text-[1.2rem] [&_h2]:text-pink md:[&_h2]:mb-3">
            <div class="border-b border-pink pb-4 md:border-0 md:pb-0">
                <a class="text-2xl leading-tight hover:no-underline!" href="<?= $site->url() ?>"><?= $site->title()->esc() ?></a>
                <p class="mt-1 mb-2 text-ink/75"><?= $site->footer_tagline()->esc() ?></p>
                <?php foreach (['instagram' => 'footer.instagram', 'instagramtess' => 'footer.instagram-tess'] as $field => $label): ?>
                    <?php if ($site->$field()->isNotEmpty()): ?><a
                            class="flex items-center justify-center gap-1.5 py-0.5 text-lg md:justify-start"
                            href="<?= $site->$field()->esc() ?>"><?php snippet('icon', ['name' => 'instagram', 'class' => 'size-5 shrink-0']) ?><span
                                class="sr-only"><?= t($label) ?>: </span>@<?= esc(basename(rtrim(parse_url($site->$field()->value(), PHP_URL_PATH) ?? '', '/'))) ?></a><?php endif ?>
                <?php endforeach ?>
            </div>
            <nav aria-label="<?= t('footer.menu') ?>">
                <h2><?= t('footer.menu') ?></h2>
                <ul class="grid grid-cols-2 gap-x-6 gap-y-0.5 md:w-max md:grid-cols-[auto_auto] md:gap-x-10 md:whitespace-nowrap">
                    <?php foreach (['home', 'haar-verhaal', 'behandelingsplan'] as $slug): ?>
                        <?php if ($item = page($slug)): ?><li><a href="<?= $item->url() ?>"><?= $item->footerlabel()->or($item->navtitle())->or($item->title())->esc() ?></a></li><?php endif ?>
                    <?php endforeach ?>
                    <li><a href="<?= page('home')->url() ?>#help-tess"><?= t('cta.help') ?></a></li>
                    <?php if ($item = page('faq')): ?><li><a href="<?= $item->url() ?>"><?= $item->footerlabel()->or($item->navtitle())->or($item->title())->esc() ?></a></li><?php endif ?>
                    <li><a href="<?= page('home')->url() ?>#contact"><?= t('nav.contact') ?></a></li>
                </ul>
            </nav>
            <?php if ($support): ?>
                <div>
                    <h2><?= t('footer.support') ?></h2>
                    <p class="flex flex-wrap justify-center gap-x-2 md:flex-col md:items-start md:gap-y-0.5">
                        <?php foreach ($support as $label => $url): ?>
                            <?php if ($label !== array_key_first($support)): ?><span class="md:hidden" aria-hidden="true">-</span><?php endif ?><a
                                href="<?= esc($url, 'attr') ?>"><?= esc($label) ?></a>
                        <?php endforeach ?>
                    </p>
                </div>
            <?php endif ?>
            <div>
                <h2><?= t('footer.about') ?></h2>
                <address class="not-italic">
                    <?= $site->organisation()->or($site->title())->esc() ?>
                    <?php if ($site->organisationdetails()->isNotEmpty()): ?><br><?= $site->organisationdetails()->esc()->nl2br() ?><?php endif ?>
                    <?php if ($site->bankaccount()->isNotEmpty()): ?><br>IBAN <?= $site->bankaccount()->esc() ?><?php endif ?>
                    <?php if ($site->contactemail()->isNotEmpty()): ?><br><a
                            href="mailto:<?= $site->contactemail()->esc() ?>"><?= $site->contactemail()->esc() ?></a><?php endif ?>
                </address>
            </div>
        </div>
        <div
            class="mt-5 flex flex-col gap-0.5 border-t border-pink pt-3 text-sm md:mt-12 md:flex-row md:justify-between md:gap-5 md:pt-5">
            <p class="mb-0">
                <?php foreach (['privacy', 'algemene-voorwaarde'] as $i => $slug): ?>
                    <?php if ($item = page($slug)): ?><?= $i > 0 ? ' - ' : '' ?><a class="underline"
                            href="<?= $item->url() ?>"><?= $item->footerlabel()->or($item->title())->esc() ?></a><?php endif ?>
                <?php endforeach ?>
            </p>
            <p class="mb-0">© <?= date('Y') ?> <?= $site->title()->esc() ?></p>
            <p class="mb-0"><?= t('footer.credit') ?></p>
        </div>
    </div>
</footer>
</body>

</html>
