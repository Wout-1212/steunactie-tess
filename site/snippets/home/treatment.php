<?php
// Medical progress is deliberately independent of the fundraising controller.
// Only explicitly classified treatments contribute; financial values never tilt the scale.
$medicalSteps = $page->medical_steps()->toStructure();
$done = $medicalSteps->filter(fn($step) => $step->status()->value() === 'done');
$planned = $medicalSteps->filter(fn($step) => $step->status()->value() === 'planned');
$weight = fn($items) => array_sum($items->values(fn($step) => max(1, $step->weight()->or(1)->toFloat())));
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
<section class="py-9 md:py-16" aria-labelledby="medical-title">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] md:w-[calc(100%-64px)]">
        <?php snippet('components/heading', ['text' => $page->medical_title()->or(t('treatment.title')), 'id' => 'medical-title']) ?>
        <p class="max-w-[68ch] max-md:text-[14px]"><?= $page->medical_intro()->or(t('treatment.intro'))->esc() ?></p>
        <svg class="mx-auto my-[18px] block h-auto w-[min(100%,840px)] overflow-visible md:mt-[26px] md:mb-5" viewBox="0 0 800 400" role="img" aria-labelledby="medical-scale-title medical-scale-description">
            <title id="medical-scale-title"><?= t('treatment.scale-label') ?></title>
            <desc id="medical-scale-description"><?= $hasMedicalData ? tt('treatment.scale-description', ['done' => $done->count(), 'planned' => $planned->count()]) : t('treatment.unconfirmed') ?></desc>
            <path d="M400 110 V365" class="stroke-ink [stroke-width:12]" />
            <path d="M308 380 Q400 349 492 380 Z" class="fill-ink" />
            <path d="M<?= $leftX ?> <?= $leftY ?> L<?= $rightX ?> <?= $rightY ?>" class="stroke-pink [stroke-width:13] [stroke-linecap:round]" />
            <?php foreach ([[$leftX, $leftY, true], [$rightX, $rightY, false]] as [$x, $y, $filled]): ?>
                <path d="M<?= $x ?> <?= $y ?> L<?= $x - 100 ?> <?= $y + 130 ?> M<?= $x ?> <?= $y ?> L<?= $x + 100 ?> <?= $y + 130 ?>" class="fill-none stroke-ink [stroke-width:1.8]" />
                <path d="M<?= $x - 104 ?> <?= $y + 130 ?> Q<?= $x ?> <?= $y + 184 ?> <?= $x + 104 ?> <?= $y + 130 ?> Z" class="stroke-pink [stroke-width:2] <?= $filled && $doneWeight > 0 ? 'fill-pink' : 'fill-white' ?>" />
                <circle cx="<?= $x ?>" cy="<?= $y ?>" r="6" class="fill-ink" />
            <?php endforeach ?>
            <circle cx="400" cy="110" r="12" class="fill-white stroke-pink [stroke-width:4]" />
        </svg>
        <div data-reveal-group class="grid grid-cols-[1fr] gap-3 md:grid-cols-[1fr_1fr] md:gap-5">
            <?php foreach ([['done', $done, 'medical_done_amount'], ['planned', $planned, 'medical_planned_amount']] as [$status, $items, $amountField]): ?>
                <div class="rounded-[7px] bg-white p-4 md:px-6 md:py-5 motion-safe:transition-shadow motion-safe:duration-250 motion-safe:hover:shadow-card">
                    <div class="mb-[10px] flex items-baseline justify-between gap-[10px] md:gap-4">
                        <h3 class="m-0 flex items-center gap-2 text-[13px] leading-[1.35] text-ink md:text-[15px]"><?php snippet('components/icon', ['name' => 'heart', 'class' => 'size-4 text-pink']) ?><?= t('treatment.' . $status) ?></h3>
                        <?php if ($page->$amountField()->isNotEmpty()): ?><strong data-count-up class="text-right text-pink tabular-nums"><?= esc($currency->formatCurrency(max(0, $page->$amountField()->toFloat()), 'EUR')) ?></strong><?php endif ?>
                    </div>
                    <?php if ($items->isNotEmpty()): ?>
                        <ul class="grid gap-2"><?php foreach ($items as $item): ?><li class="flex items-baseline gap-[10px] text-[13px] md:text-[14px]"><span class="inline-flex size-[18px] shrink-0 items-center justify-center rounded-[50%] border border-pink text-[12px] leading-none <?= $status === 'done' ? 'bg-pink text-white' : 'text-pink' ?>" aria-hidden="true"><?= $status === 'done' ? '✓' : '' ?></span><span><?= $item->heading()->esc() ?></span></li><?php endforeach ?></ul>
                    <?php else: ?><p class="m-0 text-[13px]"><?= t('treatment.unconfirmed') ?></p><?php endif ?>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>