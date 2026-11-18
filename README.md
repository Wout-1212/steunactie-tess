# Samen voor Tess

Website van de steunactie Samen voor Tess (samenvoortess.be): het verhaal van Tess, haar behandeltraject, updates, een donatievoortgang en online doneren via Mollie. Redacteurs beheren alles in het Kirby Panel (`/panel`).

## Techniek

| Onderdeel | Gebruikt |
|---|---|
| CMS | Kirby 5.6.1 (PHP 8.2 of hoger), inhoud als tekstbestanden in `content/` |
| Styling | Tailwind CSS 4, gebouwd naar `assets/css/site.css` |
| JavaScript | Modules in `src/js`, met esbuild gebundeld naar `assets/js/site.js` |
| Online doneren | Mollie (`mollie/mollie-api-php` v3), donaties in SQLite (`site/storage/donations.sqlite`) |
| E-mail | Brevo SMTP-relay (contactformulier, bedankmail, inlogcode van het Panel); lokaal Mailpit |
| Tests | PHPUnit 13 (PHP 8.4 of hoger) in `tests/` |

## Snel starten

```sh
composer install   # kirby/ en vendor/ staan niet in Git
npm install
npm run dev        # Tailwind en esbuild in watch-modus
composer start     # in een tweede terminal, of gebruik de Herd-site
composer test      # unittests van de plugins
npm run build      # voor je commit of deployt: gecomprimeerde CSS en JS
```

## Mappen

| Map | Inhoud |
|---|---|
| `site/templates`, `site/snippets` | Opbouw van de pagina's en herbruikbare onderdelen |
| `site/blueprints` | Velden en tabbladen in het Panel |
| `site/plugins` | Eigen code: `tess-donations` (Mollie), `tess-contact` (formulier en spambeveiliging), `tess-fundraisers` (Steunactie en GoFundMe uitlezen), `tess-seo`, `tess-panel`, `tess-typography`, `tess-dates` |
| `site/languages` | Vaste interfaceteksten per taal (nl, fr, en) |
| `site/config` | `config.php` in Git; de hostconfigbestanden met sleutels niet |
| `src/js`, `assets/css/tailwind.css` | Bronbestanden van de JavaScript en de styling |

## Documentatie

- [IMPLEMENTATIE.md](IMPLEMENTATIE.md): hoe alles werkt, inhoud beheren, deployen, live zetten en onderhoud.
- [TASKS.md](TASKS.md): online doneren via Mollie: keuzes en wat nog getest moet worden.
- [TASKS-CONTROLE.md](TASKS-CONTROLE.md): controle van toegankelijkheid, SEO en beveiliging.
