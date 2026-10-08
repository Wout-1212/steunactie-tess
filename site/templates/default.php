<?php snippet('header') ?>
<section class="py-[42px] md:py-[72px] min-h-[45vh]">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)]">
        <?php if ($page->questions()->isEmpty()): ?>
            <?php if ($page->cover()->isNotEmpty()): ?>
                <div class="grid grid-cols-1 items-center gap-[26px] md:grid-cols-2 md:gap-8 lg:gap-16 mb-10">
                    <?php snippet('picture', ['source' => $page->cover(), 'label' => $page->title()->value()]) ?>
                    <div>
                        <h1><?= $page->heading()->or($page->title())->esc() ?></h1>
                        <div class="prose"><?= $page->text()->kirbytext() ?></div>
                    </div>
                </div>
            <?php else: ?>
                <div class="mb-6 max-w-[780px] md:mb-12">
                    <h1><?= $page->heading()->or($page->title())->esc() ?></h1>
                    <div class="prose"><?= $page->text()->kirbytext() ?></div>
                </div><?php endif ?>
        <?php endif ?>
        <?php foreach ($page->sections()->toStructure() as $section): ?>
            <div
                class="grid grid-cols-1 gap-[26px] py-[18px] md:grid-cols-[1fr_1.3fr] md:gap-16 md:py-[26px] [&_h2]:text-[1.3rem]">
                <h2><?= $section->heading()->esc() ?></h2>
                <div class="prose"><?= $section->text()->kirbytext() ?></div>
            </div><?php endforeach ?>
        <?php if ($page->steps()->isNotEmpty()): ?>
            <div
                class="grid grid-cols-1 gap-[26px] py-[18px] md:grid-cols-[1fr_1.3fr] md:gap-16 md:py-[26px] [&_h2]:text-[1.3rem]">
                <h2><?= $page->steps_title()->esc() ?></h2>
                <ol class="steps"><?php foreach ($page->steps()->toStructure() as $step): ?>
                        <li><strong><?= $step->heading()->esc() ?></strong><?= $step->text()->kirbytext() ?></li>
                    <?php endforeach ?>
                </ol>
            </div><?php endif ?>
        <?php if ($page->questions()->isNotEmpty()): ?>
            <div class="mt-6 grid grid-cols-1 gap-[26px] md:mt-8 md:grid-cols-[1fr_1.3fr] md:gap-16">
                <div>
                    <h1><?= $page->heading()->or($page->title())->esc() ?></h1>
                    <div class="prose"><?= $page->text()->kirbytext() ?></div>
                    <?php if ($page->accordionintro()->isNotEmpty()): ?>
                        <p><?= $page->accordionintro()->esc() ?></p><?php endif ?>
                </div>
                <div
                    class="space-y-3 [&_details]:rounded-[5px] [&_details]:border [&_details]:border-line [&_details]:bg-white [&_.prose]:px-[18px] [&_.prose]:pb-[18px] [&_.prose]:text-[.9rem]">
                    <?php foreach ($page->questions()->toStructure() as $i => $question): ?>
                        <details <?= $i === 0 ? 'open' : '' ?>>
                            <summary><?= $question->question()->esc() ?></summary>
                            <div class="prose"><?= $question->answer()->kirbytext() ?></div>
                        </details><?php endforeach ?>
                </div>
            </div><?php endif ?>
    </div>
</section><?php snippet('cta') ?><?php snippet('footer') ?>