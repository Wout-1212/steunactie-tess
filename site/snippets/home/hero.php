<section data-hero class="pt-5 pb-[38px] md:pt-[34px] md:pb-[58px]">
    <div class="mx-auto grid w-[calc(100%-40px)] max-w-[1120px] grid-cols-[1fr] items-center gap-[34px] md:w-[calc(100%-64px)] md:grid-cols-[1fr_1fr] md:gap-9 2xl:gap-16">
        <div class="max-w-[510px] [transform:translateY(-8px)] md:[transform:translateY(-12px)] 2xl:[transform:translateY(-20px)] motion-safe:animate-fade-up">
            <h1 class="mb-5 max-w-[330px] text-[36px] leading-[1.14] md:max-w-[500px] md:text-[clamp(38px,4.1vw,56px)]"><?= preg_replace('/\b(Tess)\s+/u', '$1<br>', $page->hero_title()->esc()->value()) ?></h1>
            <p class="mb-[22px] max-w-[435px] text-[15px] md:mb-7 md:text-[17px]"><?= $page->hero_text()->esc() ?></p>
            <div class="flex flex-wrap gap-[10px] md:gap-[14px]">
                <?php snippet('components/button', ['href' => '#help-tess', 'text' => t('donation.donate')]) ?>
                <?php snippet('components/button', ['href' => page('haar-verhaal')->url(), 'text' => t('home.hero.story-link'), 'variant' => 'outline']) ?>
            </div>
        </div>
        <div class="w-full min-w-0 motion-safe:animate-fade-up-late">
            <?php snippet('components/picture', ['source' => $page->hero_image(), 'label' => t('home.hero.image'), 'ratio' => 'aspect-[4/5]', 'rounded' => 'rounded-[6px]', 'portrait' => true]) ?>
        </div>
    </div>
</section>
