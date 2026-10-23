<?php
// Step 2 of an online donation: the amount comes from the donation block on the homepage (?amount=…)
// and can still be changed here. Posting this form (route 'doneren' in plugins/tess-donations) creates
// the Mollie payment. Without a Mollie key the form is replaced by the other ways to give.
$mollie = $site->mollieEnabled();
$min = option('tess.donations.min');
$max = option('tess.donations.max');
$amount = (int) get('amount');
$amount = $amount >= $min && $amount <= $max ? $amount : null;
$status = in_array(get('donation'), ['invalid', 'rate-limited', 'error'], true) ? get('donation') : null;
$messageLength = option('tess.donations.messageLength');
$field = 'block w-full rounded-[5px] border border-pink/80 bg-white px-[14px] py-[10px] text-ink';
$label = 'mb-[5px] block text-[13px] text-pink';

// After a failed POST (route 'doneren') this page is shown again with $errors: the fields that are wrong.
// What the visitor typed stays filled in; each wrong field gets a message, and a summary links to them.
$errors ??= [];
$old = fn (string $key) => $errors !== [] && is_string(get($key)) ? get($key) : null;
$number = new NumberFormatter(strtok($kirby->language()?->locale(LC_ALL) ?? 'nl_BE', '.'), NumberFormatter::DECIMAL);
$limits = ['min' => $number->format($min), 'max' => $number->format($max)];
$messages = [
    'amount' => tt('donation.error-amount', $limits),
    'name' => t('donation.error-name'),
    'email' => t('donation.error-email'),
    'message' => tt('donation.error-message', ['max' => $messageLength]),
];
// aria-invalid and aria-describedby for a field, plus any help text it already has.
$describe = function (string $key, string|null $help = null) use ($errors): string {
    $ids = trim((in_array($key, $errors, true) ? "donation-$key-error " : '') . $help);
    return (in_array($key, $errors, true) ? ' aria-invalid="true"' : '') . ($ids ? ' aria-describedby="' . $ids . '"' : '');
};
$error = fn (string $key) => in_array($key, $errors, true)
    ? '<p class="-mt-2 mb-[15px] text-[13px] font-bold text-pink" id="donation-' . $key . '-error">' . esc($messages[$key]) . '</p>'
    : '';
?>
<?php snippet('layout/header', ['noindex' => true]) ?>
<section class="min-h-[45vh] py-[42px] md:py-[72px]">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[660px]">
        <?php snippet('components/button', ['href' => page('home')->url() . '#help-tess', 'text' => t('donation.back'), 'variant' => 'outline', 'size' => 'small']) ?>
        <h1 class="mt-6 text-[clamp(30px,4vw,44px)]"><?= $page->heading()->or($page->title())->esc() ?></h1>
        <?php if ($page->text()->isNotEmpty()): ?><p class="text-[15px] md:text-[16px]"><?= $page->text()->esc() ?></p><?php endif ?>
        <?php if (!$mollie): ?>
            <p class="mt-6 rounded-[6px] bg-white p-4 text-[14px] font-bold text-pink"><?= t('donation.unavailable') ?></p>
            <div class="text-center"><?php snippet('components/donation-alternatives') ?></div>
        <?php else: ?>
        <?php if ($errors !== []): ?>
            <?php /* Focused on load (site.js), so it is read out once; no role="alert", which would read it twice. */ ?>
            <div class="mt-4 rounded-[6px] border-2 border-pink bg-white p-4" tabindex="-1" data-error-summary aria-labelledby="donation-errors-title">
                <p class="mb-2 text-[14px] font-bold text-pink" id="donation-errors-title"><?= t('donation.errors-title') ?></p>
                <ul class="m-0 list-disc pl-5 text-[14px]">
                    <?php foreach ($errors as $key): ?><li><a class="text-ink underline decoration-pink decoration-2 underline-offset-2 hover:text-pink" href="#donation-<?= $key ?>"><?= esc($messages[$key]) ?></a></li><?php endforeach ?>
                </ul>
            </div>
        <?php elseif ($status): ?><p class="mt-4 rounded-[6px] bg-white p-4 text-[14px] font-bold text-pink" role="alert"><?= t('donation.' . $status) ?></p><?php endif ?>

        <form class="mt-7 rounded-lg bg-white p-5 md:p-7" method="post" action="<?= $page->url() ?>" data-donation-form data-sending="<?= t('donation.sending') ?>">
            <div class="sr-only" aria-hidden="true"><label for="donation-website"><?= t('contact.honeypot') ?></label><input id="donation-website" name="website" tabindex="-1" autocomplete="off"></div>
            <input type="hidden" name="started" value="<?= time() ?>">

            <label class="<?= $label ?>" for="donation-amount"><?= t('donation.amount') ?> (€)</label>
            <input class="<?= $field ?> mb-1 text-[18px] font-bold text-pink tabular-nums" id="donation-amount" name="amount" type="number" required
                min="<?= $min ?>" max="<?= $max ?>" step="1" inputmode="numeric" autocomplete="off" value="<?= esc((string) ($old('amount') ?? $amount), 'attr') ?>"<?= $describe('amount', 'donation-amount-help') ?>>
            <p class="mb-[15px] text-[12px]" id="donation-amount-help"><?= tt('donation.amount-help', $limits) ?></p>
            <?= $error('amount') ?>
            <div class="grid grid-cols-1 md:grid-cols-2 md:gap-[18px]">
                <div><label class="<?= $label ?>" for="donation-name"><?= t('donation.name') ?></label><input class="<?= $field ?> mb-[15px]" id="donation-name" name="name" autocomplete="name" required maxlength="120" value="<?= esc($old('name') ?? '', 'attr') ?>"<?= $describe('name') ?>><?= $error('name') ?></div>
                <div><label class="<?= $label ?>" for="donation-email"><?= t('donation.email') ?></label><input class="<?= $field ?> mb-[15px]" id="donation-email" name="email" type="email" autocomplete="email" required maxlength="254" value="<?= esc($old('email') ?? '', 'attr') ?>"<?= $describe('email') ?>><?= $error('email') ?></div>
            </div>
            <label class="<?= $label ?>" for="donation-message"><?= t('donation.message') ?></label>
            <textarea class="<?= $field ?> mb-1" id="donation-message" name="message" rows="4" maxlength="<?= $messageLength ?>"<?= $describe('message', 'donation-message-help') ?>><?= esc($old('message') ?? '') ?></textarea>
            <p class="mb-4 flex justify-between gap-3 text-[11px]" id="donation-message-help"><span><?= t('donation.message-help') ?></span><span class="shrink-0 tabular-nums" data-donation-counter data-max="<?= $messageLength ?>" aria-hidden="true" hidden>0 / <?= $messageLength ?></span></p>
            <?= $error('message') ?>
            <fieldset class="mb-6 flex flex-wrap gap-x-6 gap-y-2 border-0 p-0 text-[13px]">
                <legend class="mb-2 text-[13px] text-pink"><?= t('donation.wall-options') ?></legend>
                <label class="flex items-center gap-2"><input class="size-4 accent-pink" type="checkbox" name="show_name" value="1"<?= $errors === [] || get('show_name') ? ' checked' : '' ?>><?= t('donation.show-name') ?></label>
                <label class="flex items-center gap-2"><input class="size-4 accent-pink" type="checkbox" name="show_amount" value="1"<?= $errors === [] || get('show_amount') ? ' checked' : '' ?>><?= t('donation.show-amount') ?></label>
            </fieldset>
            <?php snippet('components/button', ['donation' => true, 'text' => t('donation.pay'), 'size' => 'block', 'attrs' => ['type' => 'submit']]) ?>
            <p class="mt-[14px] mb-0 text-center text-[11px]"><?= t('donation.mollie-note') ?></p>
            <p class="mt-2 mb-0 text-center text-[13px] font-bold text-pink" role="status" data-donation-status></p>
        </form>
        <?php endif ?>
    </div>
</section>
<?php snippet('layout/footer') ?>
