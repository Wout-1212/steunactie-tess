<?php
$sections = $page->sections()->toStructure();
$steps = $page->steps()->toStructure();
$questions = $page->questions()->toStructure();
$imageCount = 0;
?>
<?php snippet('header') ?>
<section class="content-page py-[42px] md:py-[72px] min-h-[45vh]">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)]">
        <header class="content-intro">
            <h1<?= in_array($page->id(), ['haar-verhaal', 'behandelingsplan', 'faq'], true) ? ' class="content-page-title"' : '' ?>><?= $page->heading()->or($page->title())->esc() ?></h1>
            <?php if ($page->text()->isNotEmpty()): ?>
                <div class="prose"><?= $page->text()->kirbytext() ?></div>
            <?php endif ?>
        </header>
        <?php if ($page->cover()->isNotEmpty()): ?>
            <div class="content-cover">
                <?php snippet('picture', ['source' => $page->cover(), 'label' => $page->title()->value()]) ?>
            </div>
        <?php endif ?>

        <?php if ($sections->isNotEmpty()): ?>
            <div class="content-sections">
                <?php foreach ($sections as $section): ?>
                    <?php
                    $image = $section->image()->toFile();
                    $position = $section->imageposition()->or('auto')->value();
                    if ($image) {
                        $imageCount++;
                        $reversed = $position === 'left' || ($position === 'auto' && $imageCount % 2 === 0);
                    }
                    ?>
                    <article class="content-section<?= $image ? ' has-image' : '' ?><?= $image && $reversed ? ' is-reversed' : '' ?>">
                        <div class="content-section-text">
                            <?php if ($section->heading()->isNotEmpty()): ?>
                                <h2><?= $section->heading()->esc() ?></h2>
                            <?php endif ?>
                            <div class="prose"><?= $section->text()->kirbytext() ?></div>
                        </div>
                        <?php if ($image): ?>
                            <figure class="content-section-media">
                                <img src="<?= $image->url() ?>" alt="<?= $image->alt()->or($section->heading())->esc('attr') ?>"
                                    width="<?= $image->width() ?>" height="<?= $image->height() ?>" loading="lazy">
                                <?php if ($section->caption()->isNotEmpty()): ?>
                                    <figcaption><?= $section->caption()->esc() ?></figcaption>
                                <?php endif ?>
                            </figure>
                        <?php endif ?>
                    </article>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <?php if ($steps->isNotEmpty()): ?>
            <div class="content-steps">
                <?php if ($page->steps_title()->isNotEmpty()): ?>
                    <h2><?= $page->steps_title()->esc() ?></h2>
                <?php endif ?>
                <?php if ($page->steps_intro()->isNotEmpty()): ?>
                    <div class="prose"><?= $page->steps_intro()->kirbytext() ?></div>
                <?php endif ?>
                <ol class="timeline">
                    <?php foreach ($steps as $step): ?>
                        <li>
                            <div class="timeline-card">
                                <h3><?= $step->heading()->esc() ?></h3>
                                <?php if ($step->text()->isNotEmpty()): ?>
                                    <div class="prose"><?= $step->text()->kirbytext() ?></div>
                                <?php endif ?>
                            </div>
                        </li>
                    <?php endforeach ?>
                </ol>
            </div>
        <?php endif ?>

        <?php if ($questions->isNotEmpty()): ?>
            <div class="content-faq">
                <?php if ($page->accordiontitle()->isNotEmpty()): ?>
                    <h2><?= $page->accordiontitle()->esc() ?></h2>
                <?php endif ?>
                <?php if ($page->accordionintro()->isNotEmpty()): ?>
                    <p class="content-faq-intro"><?= $page->accordionintro()->esc() ?></p>
                <?php endif ?>
                <div class="faq-items">
                    <?php foreach ($questions as $i => $question): ?>
                        <details<?= $i === 0 ? ' open' : '' ?>>
                            <summary><?= $question->question()->esc() ?></summary>
                            <div class="prose"><?= $question->answer()->kirbytext() ?></div>
                        </details>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif ?>
    </div>
</section><?php snippet('cta') ?><?php snippet('footer') ?>
