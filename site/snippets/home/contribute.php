<section class="py-[42px] md:py-[72px]">
    <div class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)]">
        <h2><?= $page->contributiontitle()->esc() ?></h2>
        <div class="mt-7 grid grid-cols-1 gap-[18px] md:grid-cols-2 lg:grid-cols-4">
            <a class="relative rounded-lg border border-line bg-white p-[22px] text-left text-ink transition-transform duration-150 hover:-translate-y-[3px] hover:border-pink motion-reduce:transition-none [&_p]:text-base [&_p]:text-muted [&>span:last-child]:absolute [&>span:last-child]:top-[19px] [&>span:last-child]:right-[17px] [&>span:last-child]:text-pink"
                href="#help-tess"><span
                    class="mb-5 flex size-[34px] items-center justify-center rounded-[5px] bg-blush text-pink-dark"
                    aria-hidden="true">♡</span>
                <h3><?= $page->copydoneerdirect()->esc() ?></h3>
                <p><?= $page->copydraagbijaandebehandelingvantess()->esc() ?></p><span aria-hidden="true">↗</span>
            </a>
            <button
                class="relative rounded-lg border border-line bg-white p-[22px] text-left text-ink transition-transform duration-150 hover:-translate-y-[3px] hover:border-pink motion-reduce:transition-none [&_p]:text-base [&_p]:text-muted [&>span:last-child]:absolute [&>span:last-child]:top-[19px] [&>span:last-child]:right-[17px] [&>span:last-child]:text-pink"
                data-share data-share-title="<?= $site->title()->esc() ?>"
                data-share-copied="<?= $page->sharecopied()->esc() ?>"
                data-share-fallback="<?= $page->sharefallback()->esc() ?>"><span
                    class="mb-5 flex size-[34px] items-center justify-center rounded-[5px] bg-blush text-pink-dark"
                    aria-hidden="true">↗</span>
                <h3><?= $page->copydeelhaarverhaal()->esc() ?></h3>
                <p><?= $page->copydeelditverhaalenvergroothetbereik()->esc() ?></p><span aria-hidden="true">↗</span>
            </button>
            <a class="relative rounded-lg border border-line bg-white p-[22px] text-left text-ink transition-transform duration-150 hover:-translate-y-[3px] hover:border-pink motion-reduce:transition-none [&_p]:text-base [&_p]:text-muted [&>span:last-child]:absolute [&>span:last-child]:top-[19px] [&>span:last-child]:right-[17px] [&>span:last-child]:text-pink"
                href="#contact"><span
                    class="mb-5 flex size-[34px] items-center justify-center rounded-[5px] bg-blush text-pink-dark"
                    aria-hidden="true">↗</span>
                <h3><?= $page->copykominactie()->esc() ?></h3>
                <p><?= $page->copyorganiseereeninzamelactiesponsorloopquizofconcert()->esc() ?></p><span
                    aria-hidden="true">↗</span>
            </a>
            <a class="relative rounded-lg border border-line bg-white p-[22px] text-left text-ink transition-transform duration-150 hover:-translate-y-[3px] hover:border-pink motion-reduce:transition-none [&_p]:text-base [&_p]:text-muted [&>span:last-child]:absolute [&>span:last-child]:top-[19px] [&>span:last-child]:right-[17px] [&>span:last-child]:text-pink"
                href="#contact"><span
                    class="mb-5 flex size-[34px] items-center justify-center rounded-[5px] bg-blush text-pink-dark"
                    aria-hidden="true">↗</span>
                <h3><?= $page->copysteunenalsbedrijf()->esc() ?></h3>
                <p><?= $page->copyneemcontactopomtebesprekenhoejekuntbijdragen()->esc() ?></p><span
                    aria-hidden="true">↗</span>
            </a>
        </div>
        <p data-share-status role="status"></p>
    </div>
</section>
