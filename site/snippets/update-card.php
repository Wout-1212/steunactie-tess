<a href="<?= $update->url() ?>"
    class="group flex flex-col overflow-hidden rounded-lg border border-line bg-white text-ink transition-transform duration-150 hover:-translate-y-[3px] hover:border-pink motion-reduce:transition-none">
    <?php if ($photo = $update->photos()->toFile()): ?>
        <img class="aspect-[4/3] w-full object-cover" src="<?= $photo->crop(640, 480)->url() ?>"
            srcset="<?= $photo->crop(640, 480)->url() ?> 640w, <?= $photo->crop(960, 720)->url() ?> 960w"
            sizes="(min-width: 56.3125rem) 360px, (min-width: 42.5625rem) 50vw, 100vw" width="640" height="480"
            alt="" loading="lazy">
    <?php else: ?>
        <div class="flex aspect-[4/3] items-center justify-center bg-blush text-pink-dark" aria-hidden="true">
            <?php snippet('icon', ['name' => 'heart', 'class' => 'size-10']) ?></div>
    <?php endif ?>
    <div class="flex flex-1 flex-col p-5 md:p-6">
        <time datetime="<?= $update->date()->toDate('Y-m-d') ?>"><?= $update->date()->toLocalDate() ?></time>
        <h3><?= $update->title()->esc() ?></h3>
        <p class="text-base text-muted"><?= $update->text()->excerpt(140)->esc() ?></p>
        <span class="mt-auto pt-3 text-sm font-bold text-pink-dark group-hover:underline"><?= t('updates.read') ?></span>
    </div>
</a>
