<?php
// The steps are managed once on the treatment plan page, so the homepage and that page always match.
$plan = page('behandelingsplan');
$steps = $plan?->steps()->toStructure();
?>
<section class="py-9 md:py-16" aria-labelledby="journey-title">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] md:w-[calc(100%-64px)]">
        <?php snippet('components/heading', ['text' => $page->treatment_title(), 'id' => 'journey-title']) ?>
        <?php if ($page->treatment_intro()->isNotEmpty()): ?>
            <p class="max-w-[68ch]"><?= $page->treatment_intro()->esc() ?></p>
        <?php endif ?>
        <?php if ($page->treatment_image()->isNotEmpty()): ?>
            <div class="mt-6 mb-2 max-w-[760px]"><?php snippet('components/picture', ['source' => $page->treatment_image(), 'label' => t('home.treatment.image'), 'ratio' => 'aspect-[16/9]']) ?></div>
        <?php endif ?>
        <?php if ($steps?->isNotEmpty()): ?>
            <?php snippet('components/step-track', ['steps' => $steps, 'label' => $page->treatment_title(), 'class' => 'mt-[22px] md:mt-[30px]']) ?>
        <?php endif ?>
        <?php if ($plan): ?>
            <a class="mt-[22px] inline-block text-[13px] font-bold text-pink hover:underline motion-safe:transition-[color,background-color] motion-safe:duration-200" href="<?= $plan->url() ?>"><?= t('home.treatment.more') ?></a>
        <?php endif ?>
    </div>
</section>
