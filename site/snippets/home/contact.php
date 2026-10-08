<section class="scroll-mt-28 py-[42px] md:py-[72px]" id="contact">
    <div
        class="mx-auto w-[calc(100%-40px)] max-w-[1120px] lg:w-[calc(100%-64px)] grid grid-cols-1 items-center gap-[26px] md:grid-cols-2 md:gap-8 lg:gap-16">
        <div>
            <p class="mb-4 text-xs font-bold tracking-[.12em] uppercase"><?= $page->contact_kicker()->esc() ?></p>
            <h2><?= $page->contact_title()->esc() ?></h2>
            <p><?= $page->contact_text()->esc() ?></p>
        </div>
        <form
            class="contact-form [&_label]:mb-[5px] [&_label]:block [&_label]:text-sm [&_label]:text-pink-dark [&_input]:mb-[15px] [&_input]:w-full [&_input]:rounded-[5px] [&_input]:border [&_input]:border-line [&_input]:bg-white [&_input]:px-3 [&_input]:py-[10px] [&_textarea]:mb-[15px] [&_textarea]:w-full [&_textarea]:rounded-[5px] [&_textarea]:border [&_textarea]:border-line [&_textarea]:bg-white [&_textarea]:px-3 [&_textarea]:py-[10px] [&_fieldset:disabled]:opacity-55 [&_button:disabled]:cursor-default [&_.contact-note]:text-muted"
            data-contact-copy="<?= esc(json_encode(['subject' => t('contact.mail-subject'), 'name' => t('contact.name'), 'email' => t('contact.email'), 'status' => t('contact.mail-status')], JSON_UNESCAPED_UNICODE)) ?>"
            data-contact-email="<?= $site->contactemail()->esc() ?>">
            <fieldset class="min-w-0 border-0 p-0" <?= $site->contactemail()->isEmpty() ? 'disabled' : '' ?>>
                <legend class="sr-only"><?= t('contact.form') ?></legend>
                <div class="grid grid-cols-1 gap-0 md:grid-cols-2 md:gap-[18px]">
                    <div><label for="contact-name"><?= t('contact.name') ?></label><input id="contact-name"
                            name="name" autocomplete="name" required maxlength="120"></div>
                    <div><label for="contact-email"><?= t('contact.email') ?></label><input
                            id="contact-email" name="email" type="email" autocomplete="email" required maxlength="254">
                    </div>
                </div><label for="contact-message"><?= t('contact.message') ?></label><textarea
                    id="contact-message" name="message" rows="5" required maxlength="5000"></textarea><button
                    class="inline-flex min-h-11 items-center justify-center rounded-[5px] border px-[21px] py-[11px] text-sm font-bold border-transparent bg-pink text-donation-ink hover:bg-pink-dark hover:text-white"
                    type="submit"><?= t('contact.submit') ?></button>
            </fieldset>
            <?php if ($site->contactemail()->isEmpty()): ?>
                <p class="contact-note mt-3 text-xs">
                    <?= t('contact.unavailable') ?></p><?php else: ?>
                <p class="contact-note mt-3 text-xs">
                    <?= t('contact.mailto-note') ?></p><noscript>
                    <p><?= t('contact.noscript') ?> <a
                            href="mailto:<?= $site->contactemail()->esc() ?>"><?= $site->contactemail()->esc() ?></a>.</p>
                </noscript><?php endif ?>
            <p class="contact-note mt-3 text-xs" data-contact-status role="status"></p>
        </form>
    </div>
</section>