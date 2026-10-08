</main>
<footer class="bg-blush pt-12 pb-[22px] text-[.8rem] text-ink">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)]">
        <div class="grid grid-cols-2 gap-x-5 gap-y-[30px] md:grid-cols-[1.2fr_1fr_1fr_1.3fr] md:gap-[25px] lg:gap-10 [&_h2]:mb-[14px] [&_h2]:text-[.9rem] [&_h2]:text-pink-dark [&_a]:mb-[5px] [&_a]:block [&_a:hover]:underline">
            <div><a class="shrink-0 leading-none [&_img]:h-auto [&_img]:w-auto [&_img]:max-h-16 [&_img]:max-w-[min(164px,42vw)] md:[&_img]:max-w-[164px] [&_img]:[filter:drop-shadow(0_0_.7px_#302a2d)_drop-shadow(0_0_.7px_#302a2d)] mb-[18px] [&_img]:max-h-[72px] [&_img]:max-w-[180px]" href="<?= $site->url() ?>"><?php if ($logo = $site->logo()->toFile()): ?><img src="<?= $logo->url() ?>" alt="<?= $site->copysamenvoortess()->esc() ?>"><?php else: ?><img src="<?= url('assets/images/logo.png') ?>" width="2008" height="783" alt="<?= $site->copysamenvoortess()->esc() ?>"><?php endif ?></a>
                <p><?= $site->footertagline()->esc() ?></p>
                <?php foreach (['instagram', 'instagramtess'] as $field): ?><?php if ($site->$field()->isNotEmpty()): ?><a href="<?= $site->$field()->esc() ?>"><?= $site->content()->get($field . 'label')->esc() ?></a><?php endif ?><?php endforeach ?>
            </div>
            <div>
                <h2><?= $site->copymenu()->esc() ?></h2><?php foreach (['home', 'haar-verhaal', 'behandelingsplan', 'updates', 'faq'] as $slug): ?><?php if ($item = page($slug)): ?><a href="<?= $item->url() ?>"><?= $item->footerlabel()->or($item->title())->esc() ?></a><?php endif ?><?php endforeach ?><a href="<?= page('home')->url() ?>#contact"><?= $site->copycontact()->esc() ?></a>
            </div>
            <div>
                <h2><?= $site->copysteuntess()->esc() ?></h2><?php snippet('bank-account', ['compact' => true]) ?><a href="<?= page('home')->url() ?>#help-tess"><?= $site->copyhelptess()->esc() ?></a><?php if ($site->fundraiser()->isNotEmpty()): ?><a href="<?= $site->fundraiser()->esc() ?>"><?= $site->copyexternesteunactie()->esc() ?></a><?php endif ?>
            </div>
            <div>
                <h2><?= $site->copyoverdevzw()->esc() ?></h2><strong><?= $site->organisation()->or($site->title())->esc() ?></strong><?php if ($site->organisationdetails()->isNotEmpty()): ?><?= $site->organisationdetails()->kirbytext() ?><?php endif ?><?php if ($site->contactemail()->isNotEmpty()): ?><a href="mailto:<?= $site->contactemail()->esc() ?>"><?= $site->contactemail()->esc() ?></a><?php endif ?>
            </div>
        </div>
        <div class="mt-[35px] flex flex-col justify-between gap-3 border-t border-pink pt-5 text-[.65rem] md:flex-row md:gap-5 [&_a]:mr-3">
            <div><?php foreach (['privacy', 'algemene-voorwaarde'] as $slug): ?><?php if ($item = page($slug)): ?><a href="<?= $item->url() ?>"><?= $item->footerlabel()->or($item->title())->esc() ?></a><?php endif ?><?php endforeach ?></div><span>© <?= date('Y') ?> <?= $site->title()->esc() ?></span><span><?= $site->websitecredit()->esc() ?></span>
        </div>
    </div>
</footer>
</body>

</html>