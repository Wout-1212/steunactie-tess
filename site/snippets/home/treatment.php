<section class="py-[42px] md:py-[72px]">
    <div
        class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)] grid grid-cols-1 items-center gap-[26px] md:grid-cols-2 md:gap-8 lg:gap-16">
        <div>
            <h2><?= $page->treatmenttitle()->esc() ?></h2>
            <ol class="steps"><?php foreach ($page->steps()->toStructure() as $step): ?>
                    <li><?= $step->heading()->esc() ?></li><?php endforeach ?>
            </ol><a class="mt-3 inline-block text-sm font-bold text-pink-dark hover:underline"
                href="<?= page('behandelingsplan')->url() ?>"><?= $page->copyleesmeeroverhetbehandelplan()->esc() ?></a>
        </div>
        <?php snippet('picture', ['source' => $page->treatmentimage(), 'label' => $page->copyfotobijhetbehandeltraject()->value()]) ?>
    </div>
</section>
