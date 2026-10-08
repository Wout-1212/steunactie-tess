<section class="py-[42px] md:py-[72px]">
    <div
        class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)] grid grid-cols-1 items-center gap-[26px] md:grid-cols-2 md:gap-8 lg:gap-16">
        <div>
            <h2><?= $page->treatment_title()->esc() ?></h2>
            <ol class="steps"><?php foreach ($page->treatment_steps()->toStructure() as $step): ?>
                    <li><?= $step->heading()->esc() ?></li><?php endforeach ?>
            </ol><a class="mt-3 inline-block text-sm font-bold text-pink-dark hover:underline"
                href="<?= page('behandelingsplan')->url() ?>"><?= t('home.treatment.more') ?></a>
        </div>
        <?php snippet('picture', ['source' => $page->treatment_image(), 'label' => t('home.treatment.image')]) ?>
    </div>
</section>
