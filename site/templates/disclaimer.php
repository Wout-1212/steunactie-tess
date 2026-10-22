<?php
$sectionClass = 'w-full max-w-[800px] scroll-mt-[calc(var(--header-height,78px)+24px)] py-6 md:py-9';
$legalText = 'prose max-w-none wrap-anywhere';
?>
<?php snippet('layout/header') ?>
<section class="py-[42px] md:py-[72px]">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[800px] md:w-[calc(100%-64px)] [&>section+section]:mt-10 [&>section+section]:border-t [&>section+section]:border-divider md:[&>section+section]:mt-12">
        <h1><?= $page->title()->esc() ?></h1>
        <?php // The original pages remain the single editable source; no legal text is copied or rewritten. ?>
        <?php foreach (['algemene-voorwaarde' => 'algemene-voorwaarden', 'privacy' => 'privacy'] as $sourceId => $anchor): ?>
            <?php if ($legal = page($sourceId)): ?>
                <section class="<?= $sectionClass ?>" id="<?= $anchor ?>" aria-labelledby="<?= $anchor ?>-title">
                    <h2 class="mb-4 text-[23px] md:text-[26px]" id="<?= $anchor ?>-title"><?= $legal->title()->esc() ?></h2>
                    <div class="<?= $legalText ?>"><?= $legal->text()->kirbytext() ?></div>
                    <?php foreach ($legal->sections()->toStructure() as $section): ?>
                        <h3 class="mt-7"><?= $section->heading()->esc() ?></h3><div class="<?= $legalText ?>"><?= $section->text()->kirbytext() ?></div>
                    <?php endforeach ?>
                    <?php
                    // On the combined page, omit accordion headings that repeat the section title.
                    $sectionLabel = preg_replace('/\s+/u', '', mb_strtolower(trim($legal->title()->value())));
                    $accordionLabel = preg_replace('/\s+/u', '', mb_strtolower(trim($legal->accordiontitle()->value())));
                    ?>
                    <?php if ($legal->accordiontitle()->isNotEmpty() && !in_array($accordionLabel, [$sectionLabel, $sectionLabel . 'beleid'], true)): ?><h3 class="mt-7"><?= $legal->accordiontitle()->esc() ?></h3><?php endif ?>
                    <?php if ($legal->accordionintro()->isNotEmpty()): ?><div class="<?= $legalText ?>"><?= $legal->accordionintro()->kirbytext() ?></div><?php endif ?>
                    <?php snippet('disclaimer/legal-questions', ['questions' => $legal->questions()->toStructure()]) ?>
                </section>
            <?php endif ?>
        <?php endforeach ?>
        <section class="<?= $sectionClass ?>" id="donaties" aria-labelledby="donaties-title">
            <h2 class="mb-4 text-[23px] md:text-[26px]" id="donaties-title"><?= $page->accordiontitle()->esc() ?></h2>
            <?php if ($page->accordionintro()->isNotEmpty()): ?><div class="<?= $legalText ?>"><?= $page->accordionintro()->kirbytext() ?></div><?php endif ?>
            <?php snippet('disclaimer/legal-questions', ['questions' => $page->questions()->toStructure()]) ?>
        </section>
    </div>
</section>
<?php snippet('layout/cta') ?>
<?php snippet('layout/footer') ?>
