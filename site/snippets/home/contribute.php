<?php $cardClass = 'relative rounded-lg border border-line bg-white p-[22px] text-left text-ink transition-transform duration-150 hover:-translate-y-[3px] hover:border-pink motion-reduce:transition-none [&_p]:text-base [&_p]:text-muted [&>span:last-child]:absolute [&>span:last-child]:top-[19px] [&>span:last-child]:right-[17px] [&>span:last-child]:text-pink' ?>
<?php $cards = $page->contribute_cards()->toStructure() ?>
<section class="home-contribute py-[42px] md:py-[72px]">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)]">
        <h2><?= $page->contribute_title()->esc() ?></h2>
        <div class="mt-7 grid grid-cols-1 gap-[18px] md:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($cards as $card): ?>
                <?php $action = $card->action()->value() ?>
                <?php $icon = $card->icon()->or(match ($action) { 'donate' => 'heart', 'share' => 'share', default => 'megaphone' })->value() ?>
                <?php if ($action === 'share'): ?>
                    <button class="<?= $cardClass ?>" data-share data-share-title="<?= $site->title()->esc() ?>"
                        data-share-url="<?= $page->url() ?>" aria-haspopup="dialog">
                <?php else: ?>
                    <a class="<?= $cardClass ?>" href="<?= $action === 'donate' ? '#help-tess' : '#contact' ?>">
                <?php endif ?>
                    <span class="mb-5 flex size-[34px] items-center justify-center rounded-[5px] bg-blush text-pink-dark"
                        aria-hidden="true"><?php snippet('icon', ['name' => $icon]) ?></span>
                    <h3><?= $card->title()->esc() ?></h3>
                    <p><?= $card->text()->esc() ?></p><span aria-hidden="true">↗</span>
                <?= $action === 'share' ? '</button>' : '</a>' ?>
            <?php endforeach ?>
        </div>
    </div>
</section>
<?php if ($cards->findBy('action', 'share')): ?>
    <?php $shareUrl = urlencode($page->url()) ?>
    <dialog data-share-dialog aria-labelledby="share-title"
        class="m-auto w-[min(100%-32px,440px)] rounded-lg border border-line bg-white p-0 text-ink shadow-xl backdrop:bg-ink/60">
        <div class="p-6">
            <div class="mb-5 flex items-start justify-between gap-4">
                <h2 id="share-title" class="mb-0 text-[1.4rem]"><?= t('share.title') ?></h2>
                <form method="dialog"><button
                        class="flex size-9 items-center justify-center rounded-[5px] text-pink-dark hover:bg-blush"
                        aria-label="<?= t('share.close') ?>"><?php snippet('icon', ['name' => 'close']) ?></button></form>
            </div>
            <div
                class="grid grid-cols-3 gap-3 [&_a]:flex [&_a]:flex-col [&_a]:items-center [&_a]:gap-2 [&_a]:rounded-lg [&_a]:bg-blush [&_a]:px-2 [&_a]:py-4 [&_a]:text-sm [&_a]:font-bold [&_a]:text-pink-dark [&_a:hover]:bg-pink [&_a:hover]:text-white">
                <a href="https://wa.me/?text=<?= $shareUrl ?>" target="_blank" rel="noopener"><?php snippet('icon', ['name' => 'message', 'class' => 'size-6']) ?>WhatsApp</a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" rel="noopener"><?php snippet('icon', ['name' => 'facebook', 'class' => 'size-6']) ?>Facebook</a>
                <a href="mailto:?subject=<?= rawurlencode($site->title()->value()) ?>&amp;body=<?= $shareUrl ?>"><?php snippet('icon', ['name' => 'mail', 'class' => 'size-6']) ?><?= t('share.email') ?></a>
            </div>
            <label for="share-link" class="mt-5 mb-[6px] block text-sm text-muted"><?= t('share.link-label') ?></label>
            <div class="flex gap-2">
                <input id="share-link" type="text" readonly value="<?= $page->url() ?>"
                    class="min-h-11 min-w-0 flex-1 rounded-[5px] border border-line bg-paper px-3 text-sm">
                <button type="button" data-share-copy
                    class="inline-flex min-h-11 shrink-0 items-center gap-2 rounded-[5px] bg-pink px-4 text-sm font-bold text-donation-ink hover:bg-pink-dark hover:text-white"><?php snippet('icon', ['name' => 'copy', 'class' => 'size-4']) ?><?= t('share.copy') ?></button>
            </div>
            <p class="mt-3 min-h-[1.5em] text-sm text-pink-dark" data-share-status data-copied="<?= t('share.copied') ?>"
                role="status"></p>
        </div>
    </dialog>
<?php endif ?>
