<section>
    <div
        class="mx-auto flex w-[calc(100%-40px)] max-w-[1120px] flex-col items-start justify-center gap-6 border-t border-divider px-4 py-8 md:min-h-[180px] md:flex-row md:items-center md:justify-between md:gap-[30px] md:px-6 md:py-9 lg:w-[calc(100%-64px)]">
        <div class="min-w-0">
            <h2><?= $site->cta_title()->esc() ?></h2>
            <p class="text-base"><?= $site->cta_text()->esc() ?></p>
        </div><?php snippet('components/button', ['donation' => true, 'href' => page('home')->url() . '#help-tess', 'text' => t('cta.help'), 'size' => 'large']) ?>
    </div>
</section>
