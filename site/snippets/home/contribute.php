<?php $cardClass = 'relative rounded-lg border border-line bg-white p-[22px] text-left text-ink transition-transform duration-150 hover:-translate-y-[3px] hover:border-pink motion-reduce:transition-none [&_p]:text-base [&_p]:text-muted [&>span:last-child]:absolute [&>span:last-child]:top-[19px] [&>span:last-child]:right-[17px] [&>span:last-child]:text-pink' ?>
<section class="py-[42px] md:py-[72px]">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)]">
        <h2><?= $page->contribute_title()->esc() ?></h2>
        <div class="mt-7 grid grid-cols-1 gap-[18px] md:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($page->contribute_cards()->toStructure() as $card): ?>
                <?php $action = $card->action()->value() ?>
                <?php if ($action === 'share'): ?>
                    <button class="<?= $cardClass ?>" data-share data-share-title="<?= $site->title()->esc() ?>"
                        data-share-copied="<?= t('share.copied') ?>" data-share-fallback="<?= t('share.fallback') ?>">
                <?php else: ?>
                    <a class="<?= $cardClass ?>" href="<?= $action === 'donate' ? '#help-tess' : '#contact' ?>">
                <?php endif ?>
                    <span class="mb-5 flex size-[34px] items-center justify-center rounded-[5px] bg-blush text-pink-dark"
                        aria-hidden="true"><?= $action === 'donate' ? '♡' : '↗' ?></span>
                    <h3><?= $card->title()->esc() ?></h3>
                    <p><?= $card->text()->esc() ?></p><span aria-hidden="true">↗</span>
                <?= $action === 'share' ? '</button>' : '</a>' ?>
            <?php endforeach ?>
        </div>
        <p data-share-status role="status"></p>
    </div>
</section>
