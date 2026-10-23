<?php
// The steps are managed once in the Panel (Traject tab), so the homepage and the treatment plan page always match.
$plan = page('behandelingsplan');
$steps = $site->journey_steps()->toStructure();
?>
<section class="py-9 md:py-16" aria-labelledby="journey-title">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] md:w-[calc(100%-64px)]">
        <?php snippet('components/heading', ['text' => $page->treatment_title(), 'id' => 'journey-title']) ?>
        <?php if ($page->treatment_intro()->isNotEmpty()): ?>
            <p class="max-w-[68ch]"><?= $page->treatment_intro()->esc() ?></p>
        <?php endif ?>
        <?php if ($page->treatment_image()->isNotEmpty()): ?>
            <div class="mt-6 mb-2 max-w-[760px]"><?php snippet('components/picture', ['source' => $page->treatment_image(), 'label' => t('home.treatment.image'), 'sizes' => '(min-width: 800px) 760px, 100vw', 'ratio' => 'aspect-[16/9]']) ?></div>
        <?php endif ?>
        <?php if ($steps->isNotEmpty()): ?>
            <?php snippet('components/step-track', ['steps' => $steps, 'label' => $page->treatment_title(), 'class' => 'mt-[22px] md:mt-[30px]']) ?>
        <?php endif ?>
        <?php if ($plan): ?>
            <div class="mt-[22px]"><?php snippet('components/button', ['href' => $plan->url(), 'text' => t('home.treatment.more'), 'variant' => 'outline']) ?></div>
        <?php endif ?>
    </div>
</section>
