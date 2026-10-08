<section class="bg-hero text-white">
    <div
        class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)] grid grid-cols-1 md:min-h-[460px] md:grid-cols-2 lg:min-h-[560px]">
        <div class="self-center py-12 md:py-[65px] md:pr-[45px] lg:py-[90px]">
            <p class="mb-4 text-xs font-bold tracking-[.12em] uppercase"><?= $page->herokicker()->esc() ?></p>
            <h1><?= $page->herotitle()->esc() ?></h1>
            <p class="mb-7 max-w-[380px] text-hero-copy"><?= $page->herotext()->esc() ?></p>
            <div class="flex flex-wrap gap-3"><a
                    class="inline-flex min-h-11 items-center justify-center rounded-[5px] border px-[21px] py-[11px] text-sm font-bold border-transparent bg-pink text-donation-ink hover:bg-pink-dark hover:text-white"
                    href="#help-tess"><?= $page->copyhelptess()->esc() ?></a><a
                    class="inline-flex min-h-11 items-center justify-center rounded-[5px] border px-[21px] py-[11px] text-sm font-bold border-current bg-transparent text-white hover:bg-pink-dark"
                    href="<?= page('haar-verhaal')->url() ?>"><?= $page->copyleeshaarverhaal()->esc() ?></a></div>
        </div>
        <?php snippet('picture', ['source' => $page->heroimage(), 'label' => $page->copyfotovantessvolgt()->value(), 'class' => 'hero-media']) ?>
    </div>
</section>
