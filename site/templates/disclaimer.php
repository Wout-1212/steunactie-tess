<?php snippet('header') ?>
<section class="disclaimer-page py-[42px] md:py-[72px]">
    <div class="page-shell">
        <h1><?= $page->title()->esc() ?></h1>
        <?php // The original pages remain the single editable source; no legal text is copied or rewritten. ?>
        <?php foreach (['algemene-voorwaarde' => 'algemene-voorwaarden', 'privacy' => 'privacy'] as $sourceId => $anchor): ?>
            <?php if ($legal = page($sourceId)): ?>
                <section class="legal-section" id="<?= $anchor ?>" aria-labelledby="<?= $anchor ?>-title">
                    <h2 id="<?= $anchor ?>-title"><?= $legal->title()->esc() ?></h2>
                    <div class="prose"><?= $legal->text()->kirbytext() ?></div>
                    <?php if ($legal->heading()->isNotEmpty() && $legal->heading()->value() !== $legal->title()->value()): ?><h3><?= $legal->heading()->esc() ?></h3><?php endif ?>
                    <?php foreach ($legal->sections()->toStructure() as $section): ?>
                        <h3><?= $section->heading()->esc() ?></h3><div class="prose"><?= $section->text()->kirbytext() ?></div>
                    <?php endforeach ?>
                    <?php if ($legal->steps()->isNotEmpty()): ?>
                        <h3><?= $legal->steps_title()->esc() ?></h3><ol class="steps"><?php foreach ($legal->steps()->toStructure() as $step): ?><li><strong><?= $step->heading()->esc() ?></strong><div class="prose"><?= $step->text()->kirbytext() ?></div></li><?php endforeach ?></ol>
                    <?php endif ?>
                    <?php
                    // On the combined page, omit accordion headings that repeat the section title.
                    $sectionLabel = preg_replace('/\s+/u', '', mb_strtolower(trim($legal->title()->value())));
                    $accordionLabel = preg_replace('/\s+/u', '', mb_strtolower(trim($legal->accordiontitle()->value())));
                    ?>
                    <?php if ($legal->accordiontitle()->isNotEmpty() && !in_array($accordionLabel, [$sectionLabel, $sectionLabel . 'beleid'], true)): ?><h3 class="legal-accordion-title"><?= $legal->accordiontitle()->esc() ?></h3><?php endif ?>
                    <?php if ($legal->accordionintro()->isNotEmpty()): ?><div class="prose"><?= $legal->accordionintro()->kirbytext() ?></div><?php endif ?>
                    <div class="legal-questions">
                        <?php foreach ($legal->questions()->toStructure() as $question): ?>
                            <details open><summary><?= $question->question()->esc() ?></summary><div class="prose"><?= $question->answer()->kirbytext() ?></div></details>
                        <?php endforeach ?>
                    </div>
                </section>
            <?php endif ?>
        <?php endforeach ?>
        <section class="legal-section" id="donaties" aria-labelledby="donaties-title">
            <h2 id="donaties-title"><?= $page->accordiontitle()->esc() ?></h2>
            <?php if ($page->accordionintro()->isNotEmpty()): ?><div class="prose"><?= $page->accordionintro()->kirbytext() ?></div><?php endif ?>
            <div class="legal-questions">
                <?php foreach ($page->questions()->toStructure() as $question): ?>
                    <details open><summary><?= $question->question()->esc() ?></summary><div class="prose"><?= $question->answer()->kirbytext() ?></div></details>
                <?php endforeach ?>
            </div>
        </section>
    </div>
</section>
<?php snippet('cta') ?>
<?php snippet('footer') ?>
