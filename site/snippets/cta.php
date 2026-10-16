<section class="site-cta py-8 md:py-[42px]">
    <div
        class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)] cta-content flex flex-col items-start justify-between gap-[30px] md:flex-row md:items-center [&_p]:text-base">
        <div>
            <h2><?= $site->cta_title()->esc() ?></h2>
            <p><?= $site->cta_text()->esc() ?></p>
        </div><a
            class="design-button"
            href="<?= page('home')->url() ?>#help-tess"><?= t('cta.help') ?></a>
    </div>
</section>