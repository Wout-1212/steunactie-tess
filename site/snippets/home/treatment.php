<?php
// Medical progress is deliberately independent of the fundraising controller.
// Only explicitly classified treatments contribute; financial values never tilt the scale.
$medicalSteps = $page->medical_steps()->toStructure();
$done = $medicalSteps->filter(fn ($step) => $step->status()->value() === 'done');
$planned = $medicalSteps->filter(fn ($step) => $step->status()->value() === 'planned');
$weight = fn ($items) => array_sum($items->values(fn ($step) => max(1, $step->weight()->or(1)->toFloat())));
$doneWeight = $weight($done);
$plannedWeight = $weight($planned);
$totalWeight = $doneWeight + $plannedWeight;
$hasMedicalData = $totalWeight > 0;
$ratio = $hasMedicalData ? $doneWeight / $totalWeight : 0.5;
$angle = round((0.5 - $ratio) * 24, 2);
$radians = deg2rad($angle);
$leftX = 400 - 250 * cos($radians);
$rightX = 400 + 250 * cos($radians);
$leftY = 110 - 250 * sin($radians);
$rightY = 110 + 250 * sin($radians);
$locale = strtok($kirby->language()?->locale(LC_ALL) ?? 'nl_BE', '.');
$currency = new NumberFormatter($locale, NumberFormatter::CURRENCY);
$currency->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);
?>
<section class="home-treatment" aria-labelledby="medical-title">
    <div class="page-shell">
        <h2 id="medical-title"><?= $page->medical_title()->or(t('treatment.title'))->esc() ?></h2>
        <p class="medical-intro"><?= $page->medical_intro()->or(t('treatment.intro'))->esc() ?></p>
        <svg class="medical-scale" viewBox="0 0 800 400" role="img" aria-labelledby="medical-scale-title medical-scale-description">
            <title id="medical-scale-title"><?= t('treatment.scale-label') ?></title>
            <desc id="medical-scale-description"><?= $hasMedicalData ? tt('treatment.scale-description', ['done' => $done->count(), 'planned' => $planned->count()]) : t('treatment.unconfirmed') ?></desc>
            <path d="M400 110 V365" class="scale-support" />
            <path d="M308 380 Q400 349 492 380 Z" class="scale-base" />
            <path d="M<?= $leftX ?> <?= $leftY ?> L<?= $rightX ?> <?= $rightY ?>" class="scale-beam" />
            <?php foreach ([[$leftX, $leftY, true], [$rightX, $rightY, false]] as [$x, $y, $filled]): ?>
                <path d="M<?= $x ?> <?= $y ?> L<?= $x - 100 ?> <?= $y + 130 ?> M<?= $x ?> <?= $y ?> L<?= $x + 100 ?> <?= $y + 130 ?>" class="scale-chain" />
                <path d="M<?= $x - 104 ?> <?= $y + 130 ?> Q<?= $x ?> <?= $y + 184 ?> <?= $x + 104 ?> <?= $y + 130 ?> Z" class="scale-pan <?= $filled && $doneWeight > 0 ? 'scale-pan-done' : '' ?>" />
                <circle cx="<?= $x ?>" cy="<?= $y ?>" r="6" class="scale-joint" />
            <?php endforeach ?>
            <circle cx="400" cy="110" r="12" class="scale-pivot" />
        </svg>
        <div class="medical-cards">
            <?php foreach ([['done', $done, 'medical_done_amount'], ['planned', $planned, 'medical_planned_amount']] as [$status, $items, $amountField]): ?>
                <div class="medical-card medical-card-<?= $status ?>">
                    <div class="medical-card-heading">
                        <h3><?php snippet('icon', ['name' => 'heart', 'class' => 'size-4']) ?><?= t('treatment.' . $status) ?></h3>
                        <?php if ($page->$amountField()->isNotEmpty()): ?><strong><?= esc($currency->formatCurrency(max(0, $page->$amountField()->toFloat()), 'EUR')) ?></strong><?php endif ?>
                    </div>
                    <?php if ($items->isNotEmpty()): ?>
                        <ul><?php foreach ($items as $item): ?><li><span class="medical-status" aria-hidden="true"><?= $status === 'done' ? 'âœ“' : '' ?></span><span><?= $item->heading()->esc() ?></span></li><?php endforeach ?></ul>
                    <?php else: ?><p class="medical-empty"><?= t('treatment.unconfirmed') ?></p><?php endif ?>
                </div>
            <?php endforeach ?>
        </div>
        <?php if ($page->treatment_steps()->isNotEmpty() || $page->treatment_image()->isNotEmpty()): ?>
            <details class="medical-existing">
                <summary><?= $page->treatment_title()->esc() ?></summary>
                <?php if ($page->treatment_steps()->isNotEmpty()): ?><ol class="steps"><?php foreach ($page->treatment_steps()->toStructure() as $step): ?><li><?= $step->heading()->esc() ?></li><?php endforeach ?></ol><?php endif ?>
                <?php if ($page->treatment_image()->isNotEmpty()): ?><?php snippet('picture', ['source' => $page->treatment_image(), 'label' => t('home.treatment.image')]) ?><?php endif ?>
            </details>
        <?php endif ?>
        <a class="section-link" href="<?= page('behandelingsplan')->url() ?>"><?= t('home.treatment.more') ?></a>
    </div>
</section>
