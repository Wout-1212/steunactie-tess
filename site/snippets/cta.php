<section class="py-8 md:py-[42px] bg-pink text-donation-ink [&_h2]:text-white">
    <div
        class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)] flex flex-col items-start justify-between gap-[30px] md:flex-row md:items-center [&_p]:text-base">
        <div>
            <h2><?= $site->copywiljetesshelpen()->esc() ?></h2>
            <p><?= $site->copyelkebijdragegrootofkleinmaakteenverschil()->esc() ?></p>
        </div><a
            class="inline-flex min-h-11 items-center justify-center rounded-[5px] border px-[21px] py-[11px] text-sm font-bold border-transparent bg-white text-pink-dark hover:bg-pink-dark hover:text-white"
            href="<?= page('home')->url() ?>#help-tess"><?= $site->copyhelptess()->esc() ?></a>
    </div>
</section>