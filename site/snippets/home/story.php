<section class="home-story py-[42px] md:py-[72px]">
    <div
        class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)] grid grid-cols-1 items-center gap-[26px] md:grid-cols-2 md:gap-8 lg:gap-16">
        <?php snippet('picture', ['source' => $page->story_image(), 'label' => t('home.story.image')]) ?>
        <div>
            <h2><?= $page->story_title()->esc() ?></h2>
            <div class="prose"><?= $page->story_text()->kirbytext() ?></div><a
                class="mt-3 inline-block text-sm font-bold text-pink-dark hover:underline"
                href="<?= page('haar-verhaal')->url() ?>"><?= t('home.story.more') ?></a>
        </div>
    </div>
</section>
