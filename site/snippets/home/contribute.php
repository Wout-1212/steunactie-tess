<?php $cardClass = 'group relative grid grid-cols-[38px_1fr] content-start gap-x-[14px] rounded-[6px] border-0 bg-white p-[18px] text-left text-ink hover:-translate-y-[3px] md:px-[18px] md:py-5 motion-safe:transition-[transform,translate,box-shadow,border-color] motion-safe:duration-250 motion-safe:hover:shadow-card-raised' ?>
<?php $cards = $page->contribute_cards()->toStructure() ?>
<section class="py-9 md:py-16">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)]">
        <?php snippet('components/heading', ['text' => $page->contribute_title()]) ?>
        <div data-reveal-group class="mt-[26px] grid grid-cols-[1fr] gap-3 md:grid-cols-2 md:gap-[18px] lg:grid-cols-4">
            <?php foreach ($cards as $card): ?>
                <?php $action = $card->action()->value() ?>
                <?php $icon = $card->icon()->or(match ($action) { 'donate' => 'heart', 'share' => 'share', default => 'megaphone' })->value() ?>
                <?php if ($action === 'share'): ?>
                    <button class="<?= $cardClass ?>" data-share data-share-title="<?= $site->title()->esc() ?>"
                        data-share-url="<?= $page->url() ?>" aria-haspopup="dialog">
                <?php else: ?>
                    <a class="<?= $cardClass ?>" href="<?= $action === 'donate' ? '#help-tess' : '#contact' ?>">
                <?php endif ?>
                    <span class="col-start-1 row-[1/3] m-0 flex size-[34px] items-center justify-center rounded-[5px] bg-blush text-pink motion-safe:transition-[transform] motion-safe:duration-250 motion-safe:group-hover:[transform:scale(1.08)]"
                        aria-hidden="true"><?php snippet('components/icon', ['name' => $icon]) ?></span>
                    <h3 class="col-start-2 mb-[7px] text-[14px] leading-[1.35]"><?= $card->title()->esc() ?></h3>
                    <p class="col-start-2 m-0 text-[12px] leading-[1.5] text-ink"><?= $card->text()->esc() ?></p>
                <?= $action === 'share' ? '</button>' : '</a>' ?>
            <?php endforeach ?>
        </div>
    </div>
</section>
<?php if ($cards->findBy('action', 'share')): ?>
    <?php $shareUrl = urlencode($page->url()) ?>
    <dialog data-share-dialog aria-labelledby="share-title"
        class="m-auto w-[min(100%-32px,440px)] rounded-lg border border-line bg-white p-0 text-ink shadow-xl backdrop:bg-ink/60 motion-safe:open:animate-pop motion-safe:open:backdrop:animate-fade">
        <div class="p-6">
            <div class="mb-5 flex items-start justify-between gap-4">
                <?php snippet('components/heading', ['text' => t('share.title'), 'id' => 'share-title']) ?>
                <form method="dialog"><button
                        class="flex size-9 items-center justify-center rounded-[5px] text-pink-dark hover:bg-blush"
                        aria-label="<?= t('share.close') ?>"><?php snippet('components/icon', ['name' => 'close']) ?></button></form>
            </div>
            <div
                class="grid grid-cols-3 gap-3 [&_a]:flex [&_a]:flex-col [&_a]:items-center [&_a]:gap-2 [&_a]:rounded-lg [&_a]:bg-blush [&_a]:px-2 [&_a]:py-4 [&_a]:text-sm [&_a]:font-bold [&_a]:text-pink-dark [&_a:hover]:bg-pink [&_a:hover]:text-white">
                <a href="https://wa.me/?text=<?= $shareUrl ?>" target="_blank" rel="noopener"><?php snippet('components/icon', ['name' => 'message', 'class' => 'size-6']) ?>WhatsApp</a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" rel="noopener"><?php snippet('components/icon', ['name' => 'facebook', 'class' => 'size-6']) ?>Facebook</a>
                <a href="mailto:?subject=<?= rawurlencode($site->title()->value()) ?>&amp;body=<?= $shareUrl ?>"><?php snippet('components/icon', ['name' => 'mail', 'class' => 'size-6']) ?><?= t('share.email') ?></a>
            </div>
            <label for="share-link" class="mt-5 mb-[6px] block text-sm text-muted"><?= t('share.link-label') ?></label>
            <div class="flex gap-2">
                <input id="share-link" type="text" readonly value="<?= $page->url() ?>"
                    class="min-h-11 min-w-0 flex-1 rounded-[5px] border border-line bg-paper px-3 text-sm">
                <button type="button" data-share-copy
                    class="inline-flex min-h-11 shrink-0 items-center gap-2 rounded-[5px] bg-pink px-4 text-sm font-bold text-donation-ink hover:bg-pink-dark hover:text-white motion-safe:lift"><?php snippet('components/icon', ['name' => 'copy', 'class' => 'size-4']) ?><?= t('share.copy') ?></button>
            </div>
            <p class="mt-3 min-h-[1.5em] text-sm text-pink-dark" data-share-status data-copied="<?= t('share.copied') ?>"
                role="status"></p>
        </div>
    </dialog>
<?php endif ?>
