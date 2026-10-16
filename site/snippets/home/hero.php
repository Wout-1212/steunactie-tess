<section data-hero class="home-hero">
    <div class="page-shell hero-layout">
        <div class="hero-copy">
            <h1><?= preg_replace('/\b(Tess)\s+/u', '$1<br>', $page->hero_title()->esc()->value()) ?></h1>
            <p><?= $page->hero_text()->esc() ?></p>
            <div class="hero-actions">
                <a class="design-button" href="#help-tess"><?= t('donation.donate') ?></a>
                <a class="design-button design-button-outline" href="<?= page('haar-verhaal')->url() ?>"><?= t('home.hero.story-link') ?></a>
            </div>
        </div>
        <div class="hero-portrait">
            <?php snippet('picture', ['source' => $page->hero_image(), 'label' => t('home.hero.image'), 'class' => 'hero-portrait']) ?>
        </div>
    </div>
</section>
