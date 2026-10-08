<section class="bg-hero text-white">
    <div
        class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)] grid grid-cols-1 md:min-h-[460px] md:grid-cols-2 lg:min-h-[560px]">
        <div class="self-center py-12 md:py-[65px] md:pr-[45px] lg:py-[90px]">
            <p class="mb-4 text-xs font-bold tracking-[.12em] uppercase"><?= $page->hero_kicker()->esc() ?></p>
            <h1><?= $page->hero_title()->esc() ?></h1>
            <p class="mb-7 max-w-[380px] text-hero-copy"><?= $page->hero_text()->esc() ?></p>
            <div class="flex flex-wrap gap-3"><a
                    class="inline-flex min-h-11 items-center justify-center rounded-[5px] border px-[21px] py-[11px] text-sm font-bold border-transparent bg-pink text-donation-ink hover:bg-pink-dark hover:text-white"
                    href="#help-tess"><?= t('cta.help') ?></a><a
                    class="inline-flex min-h-11 items-center justify-center rounded-[5px] border px-[21px] py-[11px] text-sm font-bold border-current bg-transparent text-white hover:bg-pink-dark"
                    href="<?= page('haar-verhaal')->url() ?>"><?= t('home.hero.story-link') ?></a></div>
        </div>
        <?php snippet('picture', ['source' => $page->hero_image(), 'label' => t('home.hero.image'), 'class' => 'hero-media']) ?>
    </div>
</section>
