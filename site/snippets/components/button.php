<?php
// Pink button: a link when $href is given, otherwise a <button>.
// $text:    the label
// $variant: 'solid' (default) or 'outline'
// $size:    'default', 'block' (full width), 'small', 'header' (mobile header only) or 'large' (call to action)
// $attrs:   extra HTML attributes, such as data-* hooks for JavaScript
$donation ??= false;
$donationEffect = $donation ? 'donation-light' : '';
$variant ??= 'solid';
$size ??= 'default';
$colours = [
    'solid' => 'bg-pink text-white',
    'outline' => 'bg-transparent text-pink',
];
// Each size is a complete set, so no two classes compete for the same property.
$sizes = [
    'default' => 'inline-flex min-h-11 rounded-[5px] px-4 py-[11px] text-[12px] leading-[1.3] font-semibold hover:opacity-85 md:px-[22px] md:text-[13px] motion-safe:lift',
    'block' => 'flex min-h-11 w-full rounded-[5px] px-4 py-[11px] text-[12px] leading-[1.3] font-semibold hover:opacity-85 md:px-[22px] md:text-[13px] motion-safe:lift',
    'small' => 'inline-flex min-h-11 rounded-[5px] px-3 py-2 text-[11px] leading-[1.3] font-semibold hover:opacity-85 md:min-h-9 motion-safe:lift',
    'header' => 'hidden rounded-[5px] leading-[1.3] font-semibold hover:opacity-85 max-md:ml-auto max-md:inline-flex max-md:min-h-[38px] max-md:px-[13px] max-md:py-[9px] max-md:text-[11px] motion-safe:lift',
    'large' => 'inline-flex min-h-[54px] w-full shrink-0 self-stretch rounded-md px-5 py-4 text-[16px] leading-[1.3] font-bold shadow-[0_6px_18px_#C43B7533] transition-[transform,box-shadow] duration-160 hover:[transform:translateY(-2px)] hover:shadow-[0_10px_24px_#C43B7552] md:min-h-14 md:w-auto md:self-auto md:px-[34px] motion-safe:active:[transform:translateY(0)_scale(.98)] motion-safe:active:shadow-none motion-safe:active:duration-80 motion-reduce:transition-none motion-reduce:hover:transform-none',
];
$attrs = array_merge($attrs ?? [], ['class' => "{$donationEffect} items-center justify-center gap-2 border border-pink text-center no-underline {$colours[$variant]} {$sizes[$size]}"]);
?>
<?php if (isset($href)): ?><a href="<?= esc($href) ?>"<?= attr($attrs, ' ') ?>><?= $text ?></a><?php else: ?><button type="button"<?= attr($attrs, ' ') ?>><?= $text ?></button><?php endif ?>
