<section class="py-9 md:py-16">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)]">
        <?php snippet('components/heading', ['text' => $page->updates_title()]) ?><?php snippet('components/update-cards', ['limit' => 3, 'homeLayout' => true]) ?><a
            class="inline-flex min-h-11 items-center justify-center rounded-[5px] border border-pink bg-transparent px-[21px] py-[11px] text-sm font-bold text-pink motion-safe:lift"
            href="<?= page('updates')->url() ?>"><?= t('home.updates.all') ?></a>
    </div>
</section>
