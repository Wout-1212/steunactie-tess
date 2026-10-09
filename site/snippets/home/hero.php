<section data-hero
    class="relative -mt-[76px] flex min-h-[max(600px,90svh)] items-center overflow-hidden bg-hero text-white md:-mt-[90px] md:min-h-[max(680px,100svh)]">
    <?php snippet('picture', ['source' => $page->hero_image(), 'label' => t('home.hero.image'), 'class' => 'hero-media']) ?>
    <?php /* Overlays keep the white text and transparent navbar readable on any photo, even a very light one. */ ?>
    <div class="absolute inset-0 bg-hero/70 md:bg-transparent md:bg-linear-to-r md:from-hero/90 md:via-hero/70 md:to-hero/20"
        aria-hidden="true"></div>
    <div class="absolute inset-x-0 top-0 h-48 bg-linear-to-b from-hero/85 via-hero/50 to-transparent" aria-hidden="true"></div>
    <div class="relative mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)]">
        <div class="max-w-[560px] pt-[124px] pb-12 md:pt-[155px] md:pb-[65px] lg:pt-[180px] lg:pb-[90px]">
            <p class="mb-4 text-xs font-bold tracking-[.12em] uppercase"><?= $page->hero_kicker()->esc() ?></p>
            <h1><?= $page->hero_title()->esc() ?></h1>
            <p class="mb-7 max-w-[380px] text-hero-copy"><?= $page->hero_text()->esc() ?></p>
            <div class="flex flex-wrap gap-3"><a
                    class="inline-flex min-h-11 items-center justify-center rounded-[5px] border px-[21px] py-[11px] text-sm font-bold border-transparent bg-pink text-donation-ink hover:bg-pink-dark hover:text-white"
                    href="#help-tess"><?= t('cta.help') ?></a><a
                    class="inline-flex min-h-11 items-center justify-center rounded-[5px] border px-[21px] py-[11px] text-sm font-bold border-current bg-transparent text-white hover:bg-pink-dark"
                    href="<?= page('haar-verhaal')->url() ?>"><?= t('home.hero.story-link') ?></a></div>
        </div>
    </div>
</section>