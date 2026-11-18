# Samen voor Tess: werking, beheer en livegang

Hoe de site in elkaar zit, hoe je de inhoud beheert en wat er moet gebeuren om ze live te zetten en te onderhouden. Voor een kort overzicht en de startcommando's: zie [README.md](README.md).

**Stand van zaken (18-11-2026):** de site draait op de testserver (https://samenvoortess.wout.philippo.kdgmt.be, zie [TASKS.md](TASKS.md#testserver)) en is nog niet live. Online doneren via Mollie is gebouwd en werkt daar met een testsleutel.

## Lokaal ontwikkelen

Gebruik de Herd-site, of start vanuit de projectmap `composer start` (PHP en Composer moeten beschikbaar zijn). Open daarna de lokale site en `/panel`. Na een verse `git clone` eerst `composer install` en `npm install` uitvoeren.

- **Mail:** lokaal gaat alle mail naar Mailpit in plaats van Brevo, via `site/config/config.theagency2627-steunactie-tess.test.php` (host `127.0.0.1`, poort `1025`, zonder login of versleuteling). Verstuurde berichten bekijk je op http://localhost:8025.
- **Bedankmail bekijken:** `/dev/mail` toont de bedankmail voor donateurs zonder te versturen, in alle talen en varianten, met voorbeeldgegevens. Die route bestaat alleen met `debug` aan; op de testserver en live geeft ze 404.
- **Lokaal config:** zet `debug` aan en de Panel-inlogcode uit. Met `'tess.mollie.key' => 'test_…'` erin werkt doneren ook lokaal; de webhook wordt dan weggelaten en de bedankpagina haalt de status zelf op.
- **Tests:** `composer test` voert de unittests in `tests/` uit: donatieberichten (tegenhouden en opschonen), Mollie-status, het uitlezen van Steunactie en GoFundMe, de databasemigraties en de formulierbeveiliging (rate limit, Origin-controle). Ze gebruiken een database en caches in het geheugen en roepen Mollie niet aan. PHPUnit is een ontwikkelpakket (PHP 8.4 of hoger) en komt met `--no-dev` niet op de server.

## Composer-pakketten (kirby en vendor)

De mappen `kirby/` en `vendor/` staan **niet** in Git (zie `.gitignore`). Composer installeert ze:

- `kirby/`: Kirby zelf (pakket `getkirby/cms`, via `getkirby/composer-installer` in deze map gezet);
- `vendor/`: Kirby's afhankelijkheden (onder meer PHPMailer voor de mails), de Mollie-SDK en de autoloader.

Welke versies precies, staat vast in `composer.lock`; dat bestand staat wél in Git, zodat iedereen exact dezelfde versies krijgt (nu Kirby 5.6.1 en Mollie-SDK 3.0.0).

Na elke `git clone` of `git pull` waarin `composer.json` of `composer.lock` veranderd is, en bij elke deploy:

```sh
composer install                                   # lokaal
composer install --no-dev --optimize-autoloader    # op de server
```

**Let op:** zonder `kirby/` en `vendor/` werkt de site helemaal niet. Kan de server geen Composer uitvoeren (bijvoorbeeld bij een deploy via FTP), voer dan het servercommando lokaal uit en upload beide mappen mee.

Een nieuw pakket toevoegen gaat met `composer require naam/pakket`. Commit daarna `composer.json` en `composer.lock`, niet `kirby/` of `vendor/`.

Kirby bijwerken gaat ook via Composer, bijvoorbeeld `composer update getkirby/cms` (blijft binnen de versie in `composer.json`, nu `^5.2`). Test daarna lokaal, commit `composer.lock` en voer op de server opnieuw `composer install` uit. Pas nooit bestanden in `kirby/` zelf aan: die worden bij elke installatie overschreven.

## Frontend: Tailwind en JavaScript

```sh
npm run dev     # Tailwind en esbuild in watch-modus (stoppen met Ctrl+C)
npm run build   # gecomprimeerde productiebestanden
```

`npm run build` schrijft `assets/css/site.css` en `assets/js/site.js`. Voer het uit voor je commit of deployt: `npm run dev` schrijft niet-gecomprimeerde bestanden. Bewerk deze gegenereerde bestanden niet rechtstreeks. Ze blijven in Git, zodat de site zonder Node.js op de PHP-host draait. Er wordt geen Tailwind-CDN gebruikt en de fonts zitten lokaal in dezelfde CSS-bundel.

### Tailwind

- **In de templates en snippets** (`site/templates`, `site/snippets`): utility-klassen voor alles, dus layout, ruimte, kleur, typografie, responsive gedrag, hover- en focusstijlen en transities.
- **In `assets/css/tailwind.css`** staat alleen wat utilities niet (overzichtelijk) kunnen:
  1. design tokens in `@theme` (kleuren, lettertypes, breekpunten, animaties) en basisstijlen voor gewone HTML-elementen;
  2. inhoud die de templates niet zelf opbouwen: rijke tekst uit het Panel (`.prose`) en de automatische opmaak van de naam Tess (`.tess-name`, toegevoegd door de plugin `tess-typography`);
  3. pseudo-elementen die iets tekenen (stapnummers, lijnen in de tijdlijnen, het +/−-teken bij uitklapvragen) en onderdelen van de browser zelf (de voortgangsbalk, het standaardpijltje van `details`);
  4. beweging die JavaScript aanzet (het verschijnen bij scrollen, `.reveal`);
  5. drie herbruikbare effecten als eigen utility: `lift` (knop komt omhoog bij hover), `link-underline` (onderlijn schuift in) en `donation-light` (de lichtsporen op de donatieknoppen).
- **Markup die op meerdere plaatsen terugkomt** is een Kirby-snippet met de utility-klassen erin, geen CSS-klasse: `snippet('components/button')` voor de roze knop (varianten `solid`/`outline`, maten `default`, `block`, `small`, `header`, `large`), `snippet('components/heading')` voor de koppen op de homepage en `snippet('components/picture')` voor foto's met placeholder en `srcset`.
- **Mappen in `site/snippets`:** `layout/` (header, footer, oproepblok en `meta.php`, op elke pagina), `components/` (herbruikbare onderdelen zoals knop, kop, foto, icoon, updatekaart en donatie), `home/` en `disclaimer/` (onderdelen die alleen op die pagina staan), `blocks/` (weergave van Panel-blokken; deze mapnaam is een vaste Kirby-afspraak) en `emails/` (opmaak van de e-mails).
- **HTML-e-mails** gebruiken inline stijlen, omdat mailprogramma's geen stylesheets ondersteunen.
- **Breekpunten:** `xs` (361px), `md` (681px), `lg` (901px), `xl` (1001px) en `2xl` (1101px).

### JavaScript

De JavaScript staat in `src/js`, één ES-module per onderdeel (menu, taalkeuze, donatieformulier, donatiemuur, lightbox, delen, de ster op de donatieknoppen, de weegschaal, confetti enzovoort). `src/js/main.js` importeert ze allemaal; esbuild bundelt dat tot één `assets/js/site.js`, ingepakt in een functie zodat niets in de globale scope belandt.

Een bundel in plaats van losse modules in de browser, omdat `.htaccess` alles in `assets/js` een jaar laat cachen: alleen `site.js` krijgt `?v=` met de bestandsdatum, losse geïmporteerde bestanden zouden na een update oud blijven. De browserdoelen van esbuild (`--target` in `package.json`) zijn dezelfde als die van Tailwind 4.

Elk onderdeel zoekt zijn eigen HTML via `data-`-attributen (zoals `data-menu-toggle`, `data-reveal-group` en `data-donation-bar`), nooit via stijlklassen, en doet niets op pagina's zonder die HTML. De site werkt ook zonder JavaScript. Beweging (opbouw bij scrollen, optellende cijfers, weegschaal, lichtsporen) staat uit bij `prefers-reduced-motion` (`src/js/motion.js`).

## Design en typografie

De frontend volgt het goedgekeurde ontwerp (samen-voor-tess-preview-1.pdf, 16-10-2026). De kleuren uit Kleurenpallet.pdf staan als tokens in `@theme` in `assets/css/tailwind.css`:

| Gebruik | Kleur |
|---|---|
| Titels en accenten (`pink`) | #C43B75 |
| Gewone tekst (`ink`) | #27222A |
| Kaarten (`paper`) | #FFFFFF |
| Achtergrond boven, midden, onder | #F5D7E6, #FFF6CE, #C8EEF3 |

De verticale pastelgradiënt loopt over de hele homepage; de header is transparant bovenaan en wit na scrollen. De desktophero heeft tekst en een losse portretfoto naast elkaar; op mobiel staan ze onder elkaar.

Montserrat wordt gebruikt voor alle tekst. Alleen het losse woord Tess wordt automatisch in Caveat gezet, ook in inhoud uit het Panel (plugin `tess-typography`). De lokale WOFF2-bestanden en fontlicenties staan in `assets/fonts`; bezoekers doen geen externe fontverzoeken.

## Inhoud beheren in het Panel

Het Panel opent op de site met deze tabbladen, de vaakst gebruikte eerst:

- **Overzicht:** opgehaald bedrag (inclusief Steunactie, GoFundMe en de online donaties), percentage van het doel, aantal donaties, aantal updates, de trajectstap die op Nu bezig staat, de stand van de weegschaal en het aantal berichten dat op goedkeuring wacht. Elk cijfer linkt naar het tabblad waar je het aanpast (plugin `tess-panel`). Daaronder staan de pagina's en de juridische pagina's.
- **Updates:** voeg een update toe, vul datum en tekst (blokken) in en zet de update op Gepubliceerd. Alleen gepubliceerde updates verschijnen; de homepage toont de drie meest recente, de pagina Updates alle.
- **Donaties:** titel van de voortgangsbalk, doelbedrag, opgehaalde bedragen per bron, mijlpalen en (alleen-lezen) het totaal van de online donaties.
- **Traject:** de stappen van de tijdlijn met hun status (homepage en de pagina Ziekte & behandeling), en de weegschaal (alleen homepage). Titel en inleiding van de tijdlijn staan bij Home → Traject.
- **Instellingen:** logo, organisatiegegevens, contactadres, rekeningnummer, Instagram, de links naar Steunactie en GoFundMe, de standaard deelafbeelding en het oproepblok onderaan de pagina's.

Daarnaast heeft het Panel een eigen menu-item **Donaties** voor de online donaties (zie [Online doneren](#online-doneren-mollie)).

Elke pagina heeft een eigen blueprint met alleen de velden die ze echt toont (`site/blueprints/pages`). Wat op meerdere plaatsen terugkomt staat in `site/blueprints/fields` (`euro`, `photo`, `text-sections`, `questions`), `sections` (de updatelijst) en `tabs` (de tabbladen van de site en het tabblad Menu & zoekmachines).

**Waar de teksten staan.** De homepage gebruikt `content/home/home.nl.txt`; de andere pagina's hun eigen contentbestand (`story`, `treatment`, `faq`, `legal`, `disclaimer`, `donate`, `thanks`). Pagina's zonder eigen template gebruiken `site/templates/default.php`. Footer, oproepblok, donatiebedragen en het traject staan in `content/site.nl.txt`. Vaste interfaceteksten (knoppen, formulierlabels, meldingen, mails) staan per taal in `site/languages`. PHP bevat alleen de opbouw en logica.

**Foto's:** upload afbeeldingen en geef elke foto een beschrijvende alternatieve tekst. Zonder foto toont de site een placeholder.

### Donatievoortgang en rekeningnummer

Zonder doelbedrag blijft de voortgangsbalk zichtbaar met de voorbeeldwaarden € XX.XXX, € XXX.XXX en 14%. Vul het doelbedrag en de bedragen per bron in bij Donaties om de echte voortgang te tonen. Het totaal telt automatisch mee wat van de publieke pagina's van Steunactie en GoFundMe gelezen wordt (plugin `tess-fundraisers`: server-side, elk uur, bij een fout blijft het laatste bekende resultaat staan) en de betaalde online donaties. Mijlpalen zijn afzonderlijk bewerkbaar.

Het rekeningnummer (nu BE97 7340 5272 3549) staat bij het donatieblok en in de footer en is aanpasbaar in Instellingen. Met JavaScript is er een kopieerknop; lukt kopiëren niet, dan blijft het nummer zichtbaar om zelf te kopiëren.

### Medische weegschaal

Ga in het Panel naar Traject, onder Weegschaal. Vul alleen bevestigde onderzoeken en behandelingen in, kies per stap Al gedaan of Nog te doen, en optioneel een Soort (onderzoek, consult, scan, bloedafname, behandeling, medicatie, operatie, opname of nazorg). Elke stap is één bolletje in de schaal met het icoon van zijn soort; zonder soort krijgt een gedane stap een vinkje. Onder de stappenlijst toont het Panel wat er nu op de homepage staat ("3 van 7 stappen gedaan").

De weegschaal helt maximaal 16° over naar de kant met de meeste stappen en kantelt bij het scrollen vanuit evenwicht naar die stand (niet bij verminderde beweging). Onder de schaal staan de zin "3 van 7 behandelstappen gedaan" en de lijstjes Al gedaan en Nog te doen. De weegschaal toont nooit bedragen: het geld staat alleen in de voortgangsbalk. Zonder stappen verschijnt alleen de titel met de zin dat de stappen volgen zodra ze bevestigd zijn.

## Online doneren (Mollie)

De keuzes en de testlijst staan in [TASKS.md](TASKS.md); hier staat hoe het werkt.

**Aan en uit.** Online doneren staat aan zodra `'tess.mollie.key'` in het hostconfigbestand staat. Zonder sleutel blijft de bedragkeuze op de homepage staan, maar zonder donatiemuur, en toont `/doneren` de melding "Online doneren is binnenkort beschikbaar" met de andere manieren (overschrijving, Steunactie, GoFundMe). Met een `test_`-sleutel staat er een strook "TESTMODUS" bovenaan de site en in het Panel; `test_` hoort alleen op de testserver en lokaal, `live_` alleen op de live site.

**Verloop.**

1. Op de homepage kies je een bedrag (alle donatieknoppen gaan naar het donatieblok `#help-tess`).
2. Op `/doneren` vul je naam, e-mailadres, een optioneel bericht en de vinkjes "Toon mijn naam" en "Toon mijn bedrag" in. Fouten komen terug op dezelfde pagina (status 422) met een foutenoverzicht.
3. `POST doneren` controleert alles opnieuw (ook honeypot, invultijd, Origin en rate limit), slaat de donatie op als `open` en stuurt door naar de Mollie-checkout.
4. Mollie stuurt de donateur terug naar `/bedankt?ref=…` (bij annuleren met `&canceled=1`) en roept de webhook `POST mollie/webhook` aan. De webhook is de bron van waarheid: hij leest alleen het ID en haalt de betaling altijd opnieuw op bij Mollie. De bedankpagina (`site/controllers/thanks.php`) kijkt één keer zelf als vangnet en toont de tekst voor betaald, in verwerking of mislukt.
5. Bij betaald gaat er precies één bedankmail naar de donateur, in diens taal (`lib/sync.php`, in een SQLite-transactie zodat webhook en bedankpagina elkaar niet in de weg zitten).

**Code** in `site/plugins/tess-donations`: `index.php` (opties, routes, Panel-area), `lib/database.php` (database en migraties), `lib/payment.php` (formulier en betaling aanmaken), `lib/sync.php` (status bijwerken en mail), `lib/mail.php`, `lib/panel.php` (Panel-weergave en acties), `lib/preview.php` (`/dev/mail`) en `index.js` (de Vue-weergave in het Panel).

**Opties** (`tess.donations.*` in een configbestand): `min` 1 en `max` 10000 (hele euro's), `messageLength` 500, `limit` 10 pogingen per `window` 60 minuten per IP, `minSeconds` 3, `database` (pad naar het SQLite-bestand; standaard `site/storage/donations.sqlite`, buiten de webmap is veiliger) en `webhookUrl` (standaard `{site}/mollie/webhook`, lokaal weggelaten).

**Gegevens.** Donaties staan in `site/storage/donations.sqlite` (met de bestanden `-wal` en `-shm` ernaast). Die map staat niet in Git, wordt door `.htaccess` geblokkeerd en moet in de back-up. Kopieer de database van de testserver nooit naar de live site.

**Panel → Donaties.** Tabel met filters (Betaald, Te controleren, Verborgen, Niet betaald), zoeken op naam, e-mail en bericht, en per donatie: bekijken en het bericht bewerken, verbergen of weer tonen, goedkeuren en de bedankmail opnieuw versturen. Bedrag, betaalgegevens en e-mailadres zijn alleen-lezen. Berichten verschijnen meteen op de donatiemuur, behalve berichten met een link, e-mailadres of telefoonnummer: die wachten op goedkeuring.

**Donatiemuur.** Naast het donatieblok de laatste 10 betaalde donaties, met "Bekijk alle donaties" voor de rest (route `donaties/lijst`, alleen wat publiek mag). Wie zijn naam verbergt, staat er als "Anoniem"; wie zijn bedrag verbergt, zonder bedrag.

## Contactformulier en e-mail (Brevo)

Het contactformulier verstuurt berichten via de SMTP-relay van Brevo naar het contactadres uit Instellingen. Antwoorden gaat rechtstreeks naar de bezoeker (Reply-To). Zolang er geen contactadres is ingevuld, staat het formulier uitgeschakeld. Berichten worden niet bewaard op de server. Spam wordt tegengehouden met een verborgen veld, een minimale invultijd, een Origin-controle en maximaal 5 berichten per IP-adres per uur.

- **Code:** `site/plugins/tess-contact` (ook de gedeelde spambeveiliging voor het donatieformulier: `tessAllow()`, `tessForeignOrigin()`, `tessLooksLikeBot()`).
- **Mails:** `site/templates/emails/contact.*.php` en `donation.*.php`, elk als HTML en als platte tekst. De gedeelde opmaak (kop, kader, voettekst) staat in `site/snippets/emails/layout.php`.
- **Afzender:** `contact@samenvoortess.be` (`tess.contact.from` in `config.php`), ook voor de inlogcode van het Panel (`auth.challenge.email.from`).
- **Login en SMTP-key** staan in het hostconfigbestand, niet in Git (zie [Live zetten](#live-zetten)). Ze komen uit Brevo, Settings → SMTP & API → SMTP.
- **Testserver:** `'tess.contact.to'` in het hostconfigbestand stuurt contactberichten naar een eigen adres, zodat testberichten niet bij de opdrachtgever terechtkomen. Op de live site staat die optie er niet in.

In Brevo is het domein `samenvoortess.be` geauthenticeerd (DKIM via `brevo1`/`brevo2._domainkey`, DMARC `p=quarantine`) en `contact@samenvoortess.be` als afzender toegevoegd. Zonder dat wordt de mail geweigerd of als spam gemarkeerd.

## Zoekmachines, delen en talen

- **Titel, beschrijving en deelafbeelding per pagina:** tabblad Menu & zoekmachines. Zonder eigen titel is die "Paginatitel | Samen voor Tess", op de homepage "Samen voor Tess – " met de websitebeschrijving. Zonder eigen beschrijving gebruikt de site het begin van de introductie, en anders de websitebeschrijving uit Instellingen. De deelafbeelding is het voorbeeld bij delen via WhatsApp, Facebook of Instagram (liefst 1200 × 630). Zonder eigen afbeelding gebruikt de site een foto van de pagina, en anders de standaard deelafbeelding uit Instellingen.
- `robots.txt`, `sitemap.xml` en `.well-known/security.txt` maakt de site zelf (`site/plugins/tess-seo`). Alle tags in `<head>` (canonical, hreflang, Open Graph, JSON-LD) staan in `site/snippets/layout/meta.php`.
- **`tess.noindex`:** in het hostconfigbestand van de testserver houdt deze optie de hele site uit zoekmachines (`X-Robots-Tag` en `robots.txt` met `Disallow: /`). Op de live site nooit.
- **Talen:** Nederlands is de hoofdtaal. Een pagina verschijnt pas in het Frans of Engels als ze in het Panel in die taal is ingevuld; tot dan verwijst `/fr/…` of `/en/…` door naar de Nederlandse pagina. De taalkeuze in de header en de footer toont alle talen; een taal waarin de pagina nog niet vertaald is, staat grijs en niet klikbaar met "nog niet vertaald". Na het vertalen hoeft er niets aan de code te gebeuren. Let op: teksten uit Instellingen (footer, oproepblok) moeten ook vertaald worden, anders blijven die Nederlands.
- **Toegankelijkheidsverklaring:** onderaan de pagina Disclaimer (`/disclaimer#toegankelijkheid`), met een link in de footer. Werk de datum bij na elke controle.
- **Favicon:** `favicon.ico` (root), `assets/images/favicon.svg` en `assets/images/apple-touch-icon.png`. Nu een tijdelijk hartje; vervang alle drie als er een definitief icoon is.
- **Oude adressen:** `/privacy` en `/algemene-voorwaarde` verwijzen met een 301 naar de juiste plek op de Disclaimer (`config.php`).

## Live zetten

Deze stappen gebeuren niet automatisch bij een deploy en moeten op de server zelf gebeuren:

- [ ] **Composer-pakketten installeren.** Op de server bij elke deploy `composer install --no-dev --optimize-autoloader`, of de lokaal geïnstalleerde mappen `kirby/` en `vendor/` meeuploaden (zie [Composer-pakketten](#composer-pakketten-kirby-en-vendor)).
- [ ] **Configbestand voor de server.** Kopieer `site/config/config.host.example.php` naar `site/config/config.samenvoortess.be.php` (en `config.www.samenvoortess.be.php` als www gebruikt wordt) en vul in: `url` (met https), `content.salt` en `cookie.key` (elk `php -r "echo bin2hex(random_bytes(32));"`), de Brevo-login en een **nieuwe** SMTP-key (niet die van de testserver), en de Mollie-sleutel (zie [TASKS.md, Fase 12](TASKS.md#fase-12-live-zetten)). Dit bestand staat bewust niet in Git: met de hand uploaden en `chmod 600`. Er staat al een lokale versie klaar.
- [ ] **Niet** in het live configbestand: `'tess.noindex'` en `'tess.contact.to'`. Het lokale `config.theagency2627-steunactie-tess.test.php` (Mailpit) en het configbestand van de testserver niet naar de server kopiëren.
- [ ] **Alleen deze bestanden en mappen deployen:** `index.php`, `.htaccess`, `favicon.ico`, `assets/`, `content/`, `kirby/`, `media/` (alleen `media/index.html`), `site/` en `vendor/`. **Niet** naar de server: `.git/`, `.claude/`, `.github/`, `.vscode/`, `node_modules/`, `state/`, `src/`, `tests/`, `phpunit.xml`, de `.md`-bestanden, `composer.json/lock`, `package*.json` en `.editorconfig`. `.htaccess` blokkeert ze ook, maar alleen als extra laag. `site/accounts`, `site/sessions`, `site/cache` en `site/storage` van de server nooit overschrijven met een lokale versie.
- [x] `'debug'` staat uit in `site/config/config.php`; alleen het lokale configbestand zet het aan. Zet het op de server nooit aan.
- [ ] **Panel-accounts:** de eerste admin lokaal aanmaken en `site/accounts` uploaden (Kirby staat installeren op een publieke server niet toe). Inloggen vraagt na het wachtwoord een code (`auth.methods` in `config.php`): via een authenticator-app als die ingesteld is (Account → Tweestapsverificatie), anders per mail. Daarna de accounts voor de vzw aanmaken en overdragen, zie [Panel-accounts en overdracht](#panel-accounts-en-overdracht).
- [ ] `panel.vue.compiler` niet uitzetten: de Donaties-weergave in het Panel (`site/plugins/tess-donations/index.js`) gebruikt een `template`-string.
- [ ] **Beveiligingsheaders** in `.htaccess` voor live: HSTS van `max-age=300` naar `max-age=31536000`, en de CSP van `Content-Security-Policy-Report-Only` naar `Content-Security-Policy` (met `frame-ancestors 'self'` erbij) zodra de console op de testserver schoon is (zie [TASKS-CONTROLE.md](TASKS-CONTROLE.md#beveiligingsheaders)).
- [ ] **Kirby-licentie.** Een publieke site heeft een licentie nodig (lokaal en op een testserver niet). Kirby geeft vzw's een gratis licentie of korting, afhankelijk van hun financiering: mail naar licensing@getkirby.com met als onderwerp "Free/discounted license for a non-profit organization" en beschrijf het project en de financiering. De licentie activeer je in het Panel; ze komt in `site/config/.license` op de server (staat niet in Git).
- [ ] Contactadres invullen bij Instellingen in het Panel.
- [ ] Back-up van `content/`, `site/accounts` en `site/storage` regelen (wie, hoe vaak) en één keer een herstel testen.
- [ ] Het contactformulier en één echte donatie op de live site testen en de mail controleren in de inbox en in Brevo (Transactional → Logs).

**Vindbaar in Google** (pas na de bovenstaande stappen):

- [ ] Controleren dat de live site niet geblokkeerd is: `https://samenvoortess.be/robots.txt` toont `Allow: /` en een `Sitemap:`-regel (niet `Disallow: /`), en de pagina's sturen geen `X-Robots-Tag: noindex` mee (`curl -I https://samenvoortess.be`). Anders staat `tess.noindex` nog in het live configbestand.
- [ ] De echte teksten staan erin: geen "De definitieve tekst volgt." of "binnenkort toegevoegd" meer op Haar verhaal, Ziekte & behandeling en de FAQ, en de test- en voorbeeldupdates zijn verwijderd (nu `update1` tot `update4` en "Welkom op de website"). Google toont wat er bij de eerste bezoeken staat.
- [ ] **Beschrijving voor zoekmachines** invullen op Home, Haar verhaal, Ziekte & behandeling en FAQ (tabblad Menu & zoekmachines, 120 à 160 tekens). Dat is het grijze tekstje in Google, ook onder de sitelinks. Leeg = het begin van de introductie.
- [ ] De **websitebeschrijving** in Instellingen nakijken: op de homepage staat die ook in de titel ("Samen voor Tess – …"). Een eigen titel kan via "Titel in Google" op het tabblad Menu & zoekmachines.
- [ ] **Definitieve favicon** (drie bestanden, zie [hierboven](#zoekmachines-delen-en-talen)). Google toont dit icoon naast de naam in de zoekresultaten.
- [ ] **Standaard deelafbeelding** uploaden in Instellingen (1200 × 630), voor het voorbeeld bij delen via WhatsApp en Facebook.
- [ ] **Google Search Console** (search.google.com/search-console): eigendom `samenvoortess.be` toevoegen als domein en bevestigen met het TXT-record bij Combell (DNS). Het account op naam van de vzw zetten of de vzw als eigenaar toevoegen.
- [ ] In Search Console bij Sitemaps `https://samenvoortess.be/sitemap.xml` indienen, en bij URL-inspectie de homepage invoeren en "Indexering aanvragen" kiezen.
- [ ] Links naar de site plaatsen: Instagram-bio's (@samen_voor_tess en @tess_on_a_mission), de GoFundMe en de steunactie. Links van andere sites helpen Google de site sneller te vinden.
- [ ] Na één à twee weken in Search Console (Pagina's) nakijken of de pagina's geïndexeerd zijn en zoeken op "Samen voor Tess". De sitelinks onder het resultaat (Haar verhaal, FAQ, …) kiest Google zelf; die verschijnen meestal pas na enkele weken tot maanden.

## Panel-accounts en overdracht

Er zijn twee rollen:

| Rol | Wie | Wat |
|---|---|---|
| Admin | Eén vertrouwenspersoon van de vzw (en het team tot de overdracht) | Alles, ook gebruikers aanmaken en verwijderen |
| Editor | Iedereen die inhoud bijhoudt | Inhoud, updates, bestanden en Donaties. Geen gebruikers, talen of systeeminstellingen. Eigen naam, e-mailadres, wachtwoord en tweestapsverificatie wel. Rechten in `site/blueprints/users/editor.yml`. |

**Accounts aanmaken.** Kirby heeft geen registratie of uitnodigingsmail: alleen een admin maakt accounts aan (Panel → Gebruikers → Toevoegen). "Wachtwoord vergeten" werkt ook niet, omdat Kirby dat uitschakelt zodra tweestapsverificatie aanstaat. Per nieuw account:

1. Een tijdelijk wachtwoord kiezen en apart van het e-mailadres bezorgen (bijvoorbeeld via Teams of persoonlijk, niet in dezelfde mail).
2. De eerste inlogcode komt per mail. Kijk ook in spam of quarantaine.
3. Na het inloggen: Account → wachtwoord wijzigen en Tweestapsverificatie → authenticator-app instellen. Daarna is er geen mailcode meer nodig.

Wachtwoord vergeten: een admin zet een nieuw tijdelijk wachtwoord via Gebruikers → de persoon → Wachtwoord wijzigen.

**Overdracht aan de vzw:**

- [ ] Voor één vertrouwenspersoon van de vzw een Admin-account aanmaken. Die maakt en verwijdert daarna zelf de accounts van de vzw.
- [ ] Voor de andere mensen van de vzw een Editor-account aanmaken (of dat aan hun admin overlaten).
- [ ] Controleren dat de admin van de vzw kan inloggen en een gebruiker kan aanmaken.
- [ ] Daarna de accounts van het team verwijderen, behalve als het team de site nog onderhoudt.
- [ ] Ook de andere accounts overdragen: Brevo (e-mailadres, wachtwoord, telefoonnummer, 2FA en facturatie naar de opdrachtgever), Mollie, de host, Google Search Console en GitHub.

## Onderhoud na de oplevering

Iemand van de vzw volgt dit op. De meeste taken duren een paar minuten per maand. De punten met (technisch) vragen toegang tot de server en Composer; laat je daarvoor helpen als dat nodig is.

**Wekelijks of zodra er iets binnenkomt**
- Berichten bij donaties nakijken: Panel → Donaties, filter "Te controleren". Berichten met een link, telefoonnummer of e-mailadres verschijnen pas na goedkeuring.
- Meldingen van de uptimecontrole opvolgen als de site onbereikbaar is.
- (technisch) Update-voorstellen van Dependabot op GitHub (`.github/dependabot.yml`, wekelijks voor Composer en npm): lokaal testen, deployen en op de server `composer install --no-dev --optimize-autoloader`. Beveiligingsupdates eerst.

**Maandelijks**
- (technisch) Kirby-beveiligingsupdates: getkirby.com/security nakijken of de nieuwsbrief volgen. Kritieke fixes binnen enkele dagen installeren (zie [Composer-pakketten](#composer-pakketten-kirby-en-vendor)).
- Mollie-dashboard: mislukte webhooks. Anders kan een betaalde donatie op "open" blijven staan.
- Brevo → Transactional → Logs: geweigerde bedankmails. Opnieuw versturen kan in het Panel bij Donaties.

**Jaarlijks of af en toe**
- (technisch) PHP-versie bij de host: tijdig overstappen voor een versie geen beveiligingsupdates meer krijgt.
- (technisch) Herstel van een back-up testen op een testkopie.
- Panel → Gebruikers: accounts van mensen die niet meer meewerken verwijderen. Iedereen die geen gebruikers moet beheren is Editor, niet Admin.
- Toegankelijkheidsverklaring: datum bijwerken na een nieuwe controle.
- Domeinnaam en hosting verlengen; het certificaat (https) één keer nakijken.

## Nog aan te leveren door de opdrachtgever

- Goedgekeurde teksten (Haar verhaal, Ziekte & behandeling, FAQ, privacy en voorwaarden; die laatste bevatten nu duidelijk aangegeven conceptinhoud), contactadres en definitieve foto's.
- Doelbedrag en bevestigde donatietotalen. Tot dan toont de voortgangsbalk voorbeeldwaarden (zie [Donatievoortgang](#donatievoortgang-en-rekeningnummer)).
- Bevestigde stappen voor tijdlijn en weegschaal. Er worden geen verzonnen medische gegevens als echte getoond.
- Een definitief favicon en een standaard deelafbeelding (1200 × 630).
- Voor online doneren en privacy: zie de open vragen in [TASKS.md](TASKS.md#open-vragen-voor-de-opdrachtgever) en [TASKS-CONTROLE.md](TASKS-CONTROLE.md#open-vragen-voor-de-opdrachtgever).
