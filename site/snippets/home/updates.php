<section class="py-[42px] md:py-[72px] bg-blush">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)]">
        <h2><?= $page->updates_title()->esc() ?></h2><?php snippet('update-cards', ['limit' => 3]) ?><a
            class="inline-flex min-h-11 items-center justify-center rounded-[5px] border px-[21px] py-[11px] text-sm font-bold border-transparent bg-pink text-donation-ink hover:bg-pink-dark hover:text-white"
            href="<?= page('updates')->url() ?>"><?= t('home.updates.all') ?></a>
    </div>
</section>