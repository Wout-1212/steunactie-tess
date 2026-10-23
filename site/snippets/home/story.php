<section class="py-9 md:py-16">
    <div
        class="mx-auto grid w-[calc(100%-40px)] max-w-[1120px] grid-cols-[1fr] items-center gap-[26px] md:grid-cols-[1fr_1.1fr] md:gap-9 lg:w-[calc(100%-64px)] 2xl:gap-16">
        <?php snippet('components/picture', ['source' => $page->story_image(), 'label' => t('home.story.image'), 'ratio' => 'aspect-[3/2]', 'placeholder' => 'dashed']) ?>
        <div>
            <?php snippet('components/heading', ['text' => $page->story_title()]) ?>
            <div class="prose max-w-[56ch]"><?= $page->story_text()->kirbytext() ?></div>
            <div class="mt-4"><?php snippet('components/button', ['href' => page('haar-verhaal')->url(), 'text' => t('home.story.more'), 'variant' => 'outline']) ?></div>
        </div>
    </div>
</section>
