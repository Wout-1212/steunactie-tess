<?php
// Medical scale: confirmed treatment steps only (Panel: Traject → Weegschaal), never money; the donations
// have their own progress bar further down. Every step is one ball, so the tilt always matches the count.
$steps = $site->medical_steps()->toStructure();
$sides = [
    'done' => $steps->filterBy('status', 'done'),
    'planned' => $steps->filterBy('status', 'planned'),
];
['done' => $doneCount, 'total' => $total] = $site->medicalProgress();
// Up to 16° towards the side with more steps: the left pan (done) sinks as Tess gets further.
$tilt = $total ? round((0.5 - $doneCount / $total) * 32, 2) : 0;
// Half the beam, from the pivot at 320,64. The pans hang from the beam ends, so they move with them;
// site.js uses the same numbers to tilt the scale from level once it scrolls into view.
$beam = 190;
$shiftX = round($beam * (1 - cos(deg2rad($tilt))), 2);
$shiftY = round($beam * sin(deg2rad($tilt)), 2);
$pans = [
    'done' => ['x' => 130, 'transform' => "translate($shiftX " . -$shiftY . ')'],
    'planned' => ['x' => 510, 'transform' => 'translate(' . -$shiftX . " $shiftY)"],
];
// Balls pile up in rows of 4, 3 and 2, centred on the pan; more than 9 shows as "+n".
$pile = function (int $count): array {
    $positions = [];
    foreach ([4, 3, 2] as $row => $size) {
        $inRow = min($size, $count - count($positions));
        for ($i = 0; $i < $inRow; $i++) {
            $positions[] = [($i - ($inRow - 1) / 2) * 38, 217 - $row * 33];
        }
    }
    return $positions;
};
?>
<section class="py-9 md:py-16" aria-labelledby="medical-title">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] md:w-[calc(100%-64px)]">
        <?php snippet('components/heading', ['text' => $site->medical_title()->or(t('treatment.title')), 'id' => 'medical-title']) ?>
        <?php if ($total === 0): ?>
            <p class="max-w-[68ch]"><?= t('treatment.unconfirmed') ?></p>
        <?php else: ?>
            <p class="max-w-[68ch]"><?= $site->medical_intro()->or(t('treatment.intro'))->esc() ?></p>
            <div class="mt-[22px] md:mt-[30px]">
                <svg data-scale="<?= $tilt ?>" data-scale-pivot="320 64" data-scale-beam="<?= $beam ?>" class="mx-auto block h-auto w-full max-w-[640px] overflow-visible" viewBox="0 0 640 344" role="img" aria-labelledby="medical-scale-title medical-scale-description">
                    <title id="medical-scale-title"><?= t('treatment.scale-label') ?></title>
                    <desc id="medical-scale-description"><?= tt('treatment.scale-description', ['done' => $doneCount, 'planned' => $total - $doneCount, 'total' => $total]) ?></desc>
                    <path d="M320 64V320" class="fill-none stroke-ink [stroke-width:10] [stroke-linecap:round]" />
                    <path d="M236 334Q320 300 404 334Z" class="fill-ink" />
                    <rect x="212" y="330" width="216" height="10" rx="5" class="fill-ink" />
                    <g data-scale-part="beam" transform="rotate(<?= $tilt ?> 320 64)">
                        <path d="M130 64H510" class="stroke-pink [stroke-width:12] [stroke-linecap:round]" />
                    </g>
                    <?php foreach ($pans as $status => ['x' => $x, 'transform' => $transform]): ?>
                        <?php $items = $sides[$status]; $isDone = $status === 'done' ?>
                        <g data-scale-part="<?= $status ?>" transform="<?= $transform ?>">
                            <path d="M<?= $x ?> 64L<?= $x - 106 ?> 234M<?= $x ?> 64L<?= $x + 106 ?> 234" class="fill-none stroke-ink [stroke-width:1.8]" />
                            <path d="M<?= $x - 110 ?> 234Q<?= $x ?> 292 <?= $x + 110 ?> 234Z" class="stroke-pink [stroke-width:2.5] <?= $isDone ? 'fill-blush' : 'fill-white' ?>" />
                            <?php foreach ($pile($items->count()) as $index => [$dx, $y]): ?>
                                <?php $item = $items->nth($index); $cx = $x + $dx; $icon = $item->kind()->or($isDone ? 'check' : '')->value() ?>
                                <g>
                                    <title><?= $item->heading()->esc() ?></title>
                                    <?php if ($isDone): ?>
                                        <circle cx="<?= $cx ?>" cy="<?= $y ?>" r="17" class="fill-pink" />
                                        <path d="M<?= $cx - 10.5 ?> <?= $y - 3.4 ?>A11.2 11.2 0 0 1 <?= $cx - 1.7 ?> <?= $y - 11.6 ?>" class="fill-none stroke-white opacity-35 [stroke-width:2.4] [stroke-linecap:round]" />
                                    <?php else: ?>
                                        <circle cx="<?= $cx ?>" cy="<?= $y ?>" r="16" class="fill-white stroke-pink [stroke-dasharray:4.5_3.5] [stroke-width:2]" />
                                    <?php endif ?>
                                    <?php snippet('components/icon', ['name' => $icon, 'class' => $isDone ? 'text-white' : 'text-pink', 'attrs' => 'x="' . ($cx - 10) . '" y="' . ($y - 10) . '" width="20" height="20"']) ?>
                                </g>
                            <?php endforeach ?>
                            <?php if ($items->count() > 9): ?>
                                <text x="<?= $x + 104 ?>" y="212" text-anchor="end" class="fill-ink text-[15px] font-bold">+<?= $items->count() - 9 ?></text>
                            <?php endif ?>
                            <circle cx="<?= $x ?>" cy="64" r="5.5" class="fill-ink" />
                        </g>
                    <?php endforeach ?>
                    <circle cx="320" cy="64" r="15" class="fill-white stroke-pink [stroke-width:4]" />
                </svg>
                <p class="mt-3 mb-5 text-center text-[15px] md:mb-7"><?= tt('treatment.summary', ['count' => '<strong class="mr-[6px] text-[24px] tabular-nums md:text-[28px]">' . tt('treatment.count', ['done' => $doneCount, 'total' => $total]) . '</strong>']) ?></p>
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 md:gap-5">
                    <?php foreach ($sides as $status => $items): ?>
                        <?php $isDone = $status === 'done' ?>
                        <div class="rounded-[7px] p-4 md:px-5 <?= $isDone ? 'bg-blush' : 'bg-white' ?>">
                            <h3 class="mb-3 flex items-baseline justify-between gap-3 text-[11px] tracking-[.12em] text-ink uppercase md:text-[12px]"><span><?= t('treatment.' . $status) ?></span><span class="text-[24px] tracking-normal tabular-nums"><?= $items->count() ?></span></h3>
                            <?php if ($items->isNotEmpty()): ?>
                                <ul class="grid gap-2">
                                    <?php foreach ($items as $item): ?>
                                        <li class="flex items-center gap-[10px] text-[13px] leading-[1.4] md:text-[14px]">
                                            <span class="inline-flex size-6 shrink-0 items-center justify-center rounded-full <?= $isDone ? 'bg-pink text-white' : 'border border-dashed border-pink bg-white text-pink' ?>" aria-hidden="true"><?php snippet('components/icon', ['name' => $item->kind()->or($isDone ? 'check' : '')->value(), 'class' => 'size-[14px]']) ?></span>
                                            <span class="min-w-0"><?= $item->heading()->esc() ?></span>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            <?php else: ?>
                                <p class="m-0 text-[13px]"><?= t('treatment.none-' . $status) ?></p>
                            <?php endif ?>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif ?>
    </div>
</section>
