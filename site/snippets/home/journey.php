<?php
$steps = $page->treatment_steps()->toStructure();
$statuses = ['done', 'current', 'upcoming'];
?>
<section class="home-journey" aria-labelledby="journey-title">
    <div class="page-shell">
        <h2 id="journey-title"><?= $page->treatment_title()->esc() ?></h2>
        <?php if ($page->treatment_intro()->isNotEmpty()): ?>
            <p class="journey-intro"><?= $page->treatment_intro()->esc() ?></p>
        <?php endif ?>
        <?php if ($page->treatment_image()->isNotEmpty()): ?>
            <div class="journey-media"><?php snippet('picture', ['source' => $page->treatment_image(), 'label' => t('home.treatment.image')]) ?></div>
        <?php endif ?>
        <?php if ($steps->isNotEmpty()): ?>
            <ol class="journey<?= $steps->count() <= 5 ? ' journey-horizontal' : '' ?>">
                <?php foreach ($steps as $step): ?>
                    <?php $status = in_array($step->status()->value(), $statuses, true) ? $step->status()->value() : null ?>
                    <li class="journey-step<?= $status ? ' is-' . $status : '' ?>"<?= $status === 'current' ? ' aria-current="step"' : '' ?>>
                        <span class="journey-marker" aria-hidden="true"></span>
                        <div class="journey-card">
                            <?php if ($status): ?><span class="journey-status"><?= t('journey.' . $status) ?></span><?php endif ?>
                            <h3><?= $step->heading()->esc() ?></h3>
                            <?php if ($step->text()->isNotEmpty()): ?><p><?= $step->text()->esc() ?></p><?php endif ?>
                        </div>
                    </li>
                <?php endforeach ?>
            </ol>
        <?php endif ?>
        <a class="section-link" href="<?= page('behandelingsplan')->url() ?>"><?= t('home.treatment.more') ?></a>
    </div>
</section>
