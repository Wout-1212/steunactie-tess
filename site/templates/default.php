<?php
$sections = $page->sections()->toStructure();
$steps = $page->steps()->toStructure();
$questions = $page->questions()->toStructure();
$imageCount = 0;
$sectionHeading = 'mb-4 text-[22px] md:text-[clamp(24px,2.6vw,30px)]';
$readingText = 'prose max-w-[68ch] text-[15px] leading-[1.7] md:text-[16px] md:leading-[1.75]';
?>
<?php snippet('layout/header') ?>
<section class="min-h-[45vh] py-[42px] wrap-break-word md:py-[72px]">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)]">
        <header data-no-reveal class="mx-auto mb-[30px] max-w-[760px] md:mb-11 motion-safe:animate-fade-up">
            <h1<?= in_array($page->id(), ['haar-verhaal', 'behandelingsplan', 'faq'], true) ? ' class="text-[clamp(30px,4vw,48px)]"' : '' ?>><?= $page->heading()->or($page->title())->esc() ?></h1>
            <?php if ($page->text()->isNotEmpty()): ?>
                <div class="prose max-w-none text-[16px] leading-[1.7] md:text-[17px]"><?= $page->text()->kirbytext() ?></div>
            <?php endif ?>
        </header>
        <?php if ($page->cover()->isNotEmpty()): ?>
            <div data-no-reveal class="mx-auto mb-9 max-w-[1040px] md:mb-14 motion-safe:animate-fade-up-late">
                <?php snippet('components/picture', ['source' => $page->cover(), 'label' => $page->title()->value(), 'ratio' => 'aspect-[4/3] md:aspect-[16/9]']) ?>
            </div>
        <?php endif ?>

        <?php if ($sections->isNotEmpty()): ?>
            <div data-reveal-group>
                <?php foreach ($sections as $section): ?>
                    <?php
                    $image = $section->image()->toFile();
                    $position = $section->imageposition()->or('auto')->value();
                    if ($image) {
                        $imageCount++;
                        $reversed = $position === 'left' || ($position === 'auto' && $imageCount % 2 === 0);
                    }
                    ?>
                    <?php /* With a photo: text and photo side by side on desktop, photo above the text on smaller screens. */ ?>
                    <article class="mx-auto w-full max-w-[760px] border-t border-divider py-7 first:border-t-0 first:pt-2 md:py-10<?= $image ? ' grid grid-cols-[minmax(0,1fr)] items-start gap-[18px] md:gap-6 lg:max-w-[1040px] lg:gap-14 ' . ($reversed ? 'lg:grid-cols-[minmax(0,1fr)_minmax(0,1.25fr)]' : 'lg:grid-cols-[minmax(0,1.25fr)_minmax(0,1fr)]') : '' ?>">
                        <div>
                            <?php if ($section->heading()->isNotEmpty()): ?>
                                <h2 class="<?= $sectionHeading ?>"><?= $section->heading()->esc() ?></h2>
                            <?php endif ?>
                            <div class="<?= $readingText ?>"><?= $section->text()->kirbytext() ?></div>
                        </div>
                        <?php if ($image): ?>
                            <?php /* The photo stays in view while visitors read a long section beside it. */ ?>
                            <figure class="m-0 -order-1 lg:sticky lg:top-[calc(var(--header-height,78px)+24px)]<?= $reversed ? '' : ' lg:order-0' ?>">
                                <img class="block h-auto max-h-[420px] w-full rounded-lg object-cover md:max-h-[520px] lg:max-h-[78vh]" src="<?= $image->url() ?>" alt="<?= $image->alt()->or($section->heading())->esc('attr') ?>"
                                    width="<?= $image->width() ?>" height="<?= $image->height() ?>" loading="lazy">
                                <?php if ($section->caption()->isNotEmpty()): ?>
                                    <figcaption class="mt-[10px] text-[13px] leading-[1.5] text-[#27222ACC]"><?= $section->caption()->esc() ?></figcaption>
                                <?php endif ?>
                            </figure>
                        <?php endif ?>
                    </article>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <?php if ($steps->isNotEmpty()): ?>
            <div data-reveal-group class="mx-auto max-w-[760px] <?= $sections->isNotEmpty() ? 'mt-4 border-t border-divider pt-7 md:pt-10' : 'pt-2' ?>">
                <?php if ($page->steps_title()->isNotEmpty()): ?>
                    <h2 class="<?= $sectionHeading ?>"><?= $page->steps_title()->esc() ?></h2>
                <?php endif ?>
                <?php if ($page->steps_intro()->isNotEmpty()): ?>
                    <div class="mb-2 <?= $readingText ?>"><?= $page->steps_intro()->kirbytext() ?></div>
                <?php endif ?>
                <ol data-reveal-group class="timeline mt-5 grid gap-3 md:mt-7 md:gap-4">
                    <?php foreach ($steps as $step): ?>
                        <li class="relative pl-11 md:pl-14">
                            <div class="rounded-lg bg-white p-4 md:px-[22px] md:py-[18px] motion-safe:transition-shadow motion-safe:duration-250 motion-safe:hover:shadow-card">
                                <h3 class="mb-[6px] text-[16px] leading-[1.4] text-ink"><?= $step->heading()->esc() ?></h3>
                                <?php if ($step->text()->isNotEmpty()): ?>
                                    <div class="prose max-w-none text-[15px] leading-[1.7]"><?= $step->text()->kirbytext() ?></div>
                                <?php endif ?>
                            </div>
                        </li>
                    <?php endforeach ?>
                </ol>
            </div>
        <?php endif ?>

        <?php if ($questions->isNotEmpty()): ?>
            <div data-reveal-group class="mx-auto max-w-[760px]<?= $sections->isNotEmpty() || $steps->isNotEmpty() ? ' mt-10' : '' ?>">
                <?php if ($page->accordiontitle()->isNotEmpty()): ?>
                    <h2 class="text-[clamp(22px,2.4vw,28px)]"><?= $page->accordiontitle()->esc() ?></h2>
                <?php endif ?>
                <?php if ($page->accordionintro()->isNotEmpty()): ?>
                    <p class="mb-5"><?= $page->accordionintro()->esc() ?></p>
                <?php endif ?>
                <div data-reveal-group class="grid gap-3">
                    <?php foreach ($questions as $i => $question): ?>
                        <details class="group rounded-lg border border-blush bg-white transition-[border-color,box-shadow] duration-160 open:border-[#C43B7566] open:shadow-[0_6px_18px_#27222A0D] motion-reduce:transition-none"<?= $i === 0 ? ' open' : '' ?>>
                            <summary class="fold-sign fold-sign-round relative list-none py-4 pr-14 pl-4 text-[15px] leading-[1.45] font-bold text-ink hover:text-pink md:py-[18px] md:pr-16 md:pl-[22px] md:text-[16px] motion-safe:transition-[color] motion-safe:duration-200"><?= $question->question()->esc() ?></summary>
                            <div class="prose max-w-none px-4 pb-4 text-[15px] leading-[1.7] md:px-[22px] md:pb-5 motion-safe:group-open:animate-fade-down"><?= $question->answer()->kirbytext() ?></div>
                        </details>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif ?>
    </div>
</section><?php snippet('layout/cta') ?><?php snippet('layout/footer') ?>
