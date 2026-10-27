# Wireframes Tess in Kirby

De PDF-wireframes zijn vertaald naar PHP-templates met responsive Tailwind CSS en kleine JavaScript-interacties. Betalingen zijn nog niet aangesloten. Het aangeleverde transparante PNG-logo wordt gebruikt in header en footer. Definitieve foto's kun je later via het Panel uploaden. De gekozen hoofdkleur staat in `assets/css/tailwind.css` in `@theme` bij `--color-pink` (#C43B75).

## Lokaal bekijken

Gebruik je bestaande Herd-site, of start vanuit de projectmap `composer start` (PHP en Composer moeten beschikbaar zijn). Open daarna de lokale site en `/panel`. Na een verse `git clone` eerst `composer install` uitvoeren (zie [Composer-pakketten](#composer-pakketten-kirby-en-vendor)).

## Composer-pakketten (kirby en vendor)

De mappen `kirby/` en `vendor/` staan **niet** in Git (zie `.gitignore`). Composer installeert ze:

- `kirby/`: Kirby zelf (pakket `getkirby/cms`, via `getkirby/composer-installer` in deze map gezet);
- `vendor/`: Kirby's afhankelijkheden (onder meer PHPMailer voor de mails), de Mollie-SDK voor online donaties en de autoloader.

Welke versies precies, staat vast in `composer.lock`; dat bestand staat wél in Git, zodat iedereen exact dezelfde versies krijgt (nu Kirby 5.6.1).

Na elke `git clone` of `git pull` waarin `composer.json` of `composer.lock` veranderd is, en bij elke deploy:

```sh
composer install
```

Op de server zonder ontwikkelpakketten en met een geoptimaliseerde autoloader:

```sh
composer install --no-dev --optimize-autoloader
```

**Let op:** zonder `kirby/` en `vendor/` werkt de site helemaal niet. Kan de server geen Composer uitvoeren (bijvoorbeeld bij een deploy via FTP), voer dan `composer install --no-dev --optimize-autoloader` lokaal uit en upload beide mappen mee. Wil je ze later toch weer in Git, haal dan de regels `/kirby` en `/vendor` uit `.gitignore` en commit de mappen opnieuw.

Een nieuw pakket toevoegen gaat met `composer require naam/pakket`. Commit daarna `composer.json` en `composer.lock`, niet `kirby/` of `vendor/`.

Kirby bijwerken gaat ook via Composer, bijvoorbeeld `composer update getkirby/cms` (blijft binnen de versie in `composer.json`, nu `^5.2`). Test daarna lokaal, commit `composer.lock` en voer op de server opnieuw `composer install` uit. Pas nooit bestanden in `kirby/` zelf aan: die worden bij elke installatie overschreven.

## Inhoud aanpassen

Het Panel opent op de site met deze tabbladen, de vaakst gebruikte eerst:

- **Overzicht:** opgehaald bedrag (inclusief wat automatisch van Steunactie en GoFundMe gelezen wordt), percentage van het doel, aantal online donaties, aantal updates en de trajectstap die op Nu bezig staat. Elk cijfer linkt naar het tabblad waar je het aanpast. Daaronder staan de pagina's en de juridische pagina's.
- **Updates:** voeg een update toe, vul datum en tekst in en zet de update op Gepubliceerd. Alleen listed updates verschijnen in het overzicht en op de homepage.
- **Donaties:** doelbedrag, opgehaalde bedragen per bron en mijlpalen.
- **Traject:** de stappen van het behandeltraject met hun status, en de items van de medische weegschaal. Homepage en de pagina Ziekte & behandeling lezen allebei hieruit.
- **Instellingen:** logo, organisatiegegevens, contact, Instagram, de links naar Steunactie en GoFundMe, en het oproepblok onderaan de pagina's.

Elke pagina heeft een eigen blueprint met alleen de velden die ze echt toont (`site/blueprints/pages`). Wat op meerdere plaatsen terugkomt staat in `site/blueprints/fields` (bijvoorbeeld `euro`, `photo`, `text-sections`, `questions`), `sections` (de updatelijst) en `tabs` (de tabbladen van de site en het tabblad Menu & zoekmachines). De cijfers op Overzicht komen uit `site/plugins/tess-panel`.

Foto's: upload afbeeldingen en voeg een beschrijvende alternatieve tekst toe.

De bestaande lege conceptpagina's zijn lokaal naar unlisted verplaatst zodat de navigatie werkt. Hun UUID's zijn behouden. Er is niets naar GitHub gepusht of online gedeployd.

## Nog aan te leveren

Goedgekeurde inhoud, contactgegevens, definitieve foto's en bevestigde donatietotalen. Privacy en voorwaarden bevatten duidelijk aangegeven conceptinhoud. Er worden geen voorbeeldbedragen of verzonnen medische updates als echte gegevens getoond.

## Later

Betaalintegratie en een bevestigingsmail na een donatie zijn nog niet geïmplementeerd. De bedragkeuze is alleen een visuele voorbereiding en verricht geen betaling; het gekozen bedrag wordt ook niet aan een externe steunactie doorgegeven.

## Contactformulier en e-mail (Brevo)

Het contactformulier verstuurt berichten via de SMTP-relay van Brevo naar het Contactadres uit Instellingen. Antwoorden gaat rechtstreeks naar de bezoeker (Reply-To). De afhandeling staat in `site/plugins/tess-contact`, de mail in `site/templates/emails/contact.html.php` (opgemaakt) en `contact.text.php` (platte tekst, voor mailprogramma's zonder HTML). De gedeelde opmaak (kop, kader, voettekst) staat in `site/snippets/emails/layout.php` en is herbruikbaar voor latere mails, zoals de donatiebevestiging. Berichten worden niet bewaard op de server. Spam wordt tegengehouden met een verborgen veld, een minimale invultijd en maximaal 5 berichten per IP-adres per uur.

De SMTP-instellingen staan in `site/config/config.php`. Login en SMTP-key horen niet in Git: kopieer `site/config/config.host.example.php` naar `config.samenvoortess.be.php` (en `config.www.samenvoortess.be.php` als www gebruikt wordt) op de server en vul daarin de gegevens uit Brevo in (Settings → SMTP & API → SMTP). Zie ook [Live zetten](#live-zetten).

Lokaal gaat de mail naar Mailpit in plaats van Brevo, via `site/config/config.theagency2627-steunactie-tess.test.php` (host `127.0.0.1`, poort `1025`, zonder login of versleuteling). Verstuurde berichten bekijk je op http://localhost:8025.

In Brevo moet het domein `samenvoortess.be` geauthenticeerd zijn (SPF/DKIM) en `contact@samenvoortess.be` als afzender toegevoegd zijn, anders wordt de mail geweigerd of als spam gemarkeerd.

## Live zetten

Deze stappen gebeuren niet automatisch bij een deploy en moeten op de server zelf gebeuren:

- [ ] **Composer-pakketten installeren.** `kirby/` en `vendor/` staan niet in Git: voer op de server bij elke deploy `composer install --no-dev --optimize-autoloader` uit, of upload de lokaal geïnstalleerde mappen `kirby/` en `vendor/` mee (zie [Composer-pakketten](#composer-pakketten-kirby-en-vendor)). Zonder deze mappen werkt de site niet.
- [ ] **Nieuw configbestand voor de server aanmaken.** Kopieer `site/config/config.host.example.php` naar `site/config/config.samenvoortess.be.php` op de server en vul de Brevo SMTP-login en SMTP-key in. Wordt de site ook via www bereikt, maak dan dezelfde kopie als `config.www.samenvoortess.be.php`. Dit bestand staat bewust niet in Git en moet dus met de hand geüpload worden. Zonder dit bestand verstuurt het contactformulier geen mail.
- [ ] **Alleen deze bestanden en mappen deployen:** `index.php`, `.htaccess`, `favicon.ico`, `assets/`, `content/`, `kirby/`, `media/` (alleen `media/index.html`), `site/` en `vendor/`. **Niet** naar de server: `.git/`, `.claude/`, `.vscode/`, `node_modules/`, `state/`, de `.md`-bestanden, `composer.json/lock`, `package*.json` en `.editorconfig`. `.htaccess` blokkeert ze ook, maar alleen als extra laag. `site/accounts`, `site/sessions`, `site/cache` en `site/storage` van de server nooit overschrijven met een lokale versie.
- [ ] In het serverconfigbestand ook `url` (het ene adres van de site, met https), `content.salt` en `cookie.key` invullen (zie `config.host.example.php`). Op de testserver daarnaast `'tess.noindex' => true`; op de live site **nooit**.
- [ ] Het lokale bestand `config.theagency2627-steunactie-tess.test.php` (Mailpit) **niet** naar de server kopiëren.
- [ ] In Brevo het domein `samenvoortess.be` authenticeren (SPF/DKIM-records bij de domeinbeheerder) en `contact@samenvoortess.be` als afzender toevoegen. Ook de inlogcode van het Panel komt van dat adres (`auth.challenge.email.from` in `config.php`); zonder geverifieerde afzender kan niemand inloggen zolang er geen authenticator-app ingesteld is.
- [ ] Contactadres invullen bij Instellingen in het Panel.
- [x] `'debug'` staat uit in `site/config/config.php`; alleen het lokale configbestand zet het aan. Zet het op de server nooit aan.
- [ ] **Panel-accounts:** de eerste admin lokaal aanmaken en `site/accounts` uploaden (Kirby staat installeren op een publieke server niet toe). Inloggen vraagt na het wachtwoord een code (`auth.methods` in `config.php`): via een authenticator-app als die ingesteld is (Account → Tweestapsverificatie), anders per mail. Daarvoor moet de mail via Brevo werken. Lokaal staat de code uit.
- [ ] `panel.vue.compiler` niet uitzetten: de Donaties-weergave in het Panel (`site/plugins/tess-donations/index.js`) gebruikt een `template`-string.
- [ ] **Kirby-licentie.** Een publieke site heeft een licentie nodig (lokaal en op een testserver niet). Kirby geeft vzw's een gratis licentie of korting, afhankelijk van hun financiering: mail naar licensing@getkirby.com met als onderwerp "Free/discounted license for a non-profit organization" en beschrijf het project en de financiering. De licentie activeer je in het Panel; ze komt in `site/config/.license` op de server (staat niet in Git).
- [ ] Het contactformulier op de live site testen en de mail controleren in de inbox en in Brevo (Transactional → Logs).

## Onderhoud na de oplevering

Iemand van de vzw volgt dit op. De meeste taken duren een paar minuten per maand. De punten met (technisch) vragen toegang tot de server en Composer; laat je daarvoor helpen als dat nodig is.

**Wekelijks of zodra er iets binnenkomt**
- Berichten bij donaties nakijken: Panel → Donaties, filter "Te controleren". Berichten met een link, telefoonnummer of e-mailadres verschijnen pas na goedkeuring.
- Meldingen van de uptimecontrole opvolgen als de site onbereikbaar is.
- (technisch) Update-voorstellen van Dependabot op GitHub: lokaal testen, deployen en op de server `composer install --no-dev --optimize-autoloader`. Beveiligingsupdates eerst.

**Maandelijks**
- (technisch) Kirby-beveiligingsupdates: getkirby.com/security nakijken of de nieuwsbrief volgen. Kritieke fixes binnen enkele dagen installeren (zie [Composer-pakketten](#composer-pakketten-kirby-en-vendor)).
- Mollie-dashboard: mislukte webhooks. Anders kan een betaalde donatie op "open" blijven staan.
- Brevo → Transactional → Logs: geweigerde bedankmails. Opnieuw versturen kan in het Panel bij Donaties.

**Jaarlijks of af en toe**
- (technisch) PHP-versie bij de host: tijdig overstappen voor een versie geen beveiligingsupdates meer krijgt.
- (technisch) Herstel van een back-up testen op een testkopie.
- Toegankelijkheidsverklaring: datum bijwerken na een nieuwe controle.
- Domeinnaam en hosting verlengen; het certificaat (https) één keer nakijken.
- Panel-accounts: wie geen toegang meer nodig heeft, verwijderen.

## Zoekmachines, delen en talen

- **Beschrijving en deelafbeelding per pagina:** tabblad Menu & zoekmachines. De deelafbeelding is het voorbeeld bij delen via WhatsApp, Facebook of Instagram (liefst 1200 × 630). Zonder eigen afbeelding gebruikt de site een foto van de pagina, en anders de standaard deelafbeelding uit Instellingen.
- `robots.txt`, `sitemap.xml` en `.well-known/security.txt` maakt de site zelf (`site/plugins/tess-seo`). Alle tags in `<head>` staan in `site/snippets/layout/meta.php`.
- **Talen:** Nederlands is de hoofdtaal. Een pagina verschijnt pas in het Frans of Engels als ze in het Panel in die taal is ingevuld; tot dan verwijst `/fr/…` of `/en/…` door naar de Nederlandse pagina en toont de taalkeuze die taal niet. Na het vertalen hoeft er niets aan de code te gebeuren. Let op: teksten uit Instellingen (footer, oproepblok) moeten ook vertaald worden, anders blijven die Nederlands.
- **Toegankelijkheidsverklaring:** onderaan de pagina Disclaimer (`/disclaimer#toegankelijkheid`), met een link in de footer. Werk de datum bij na elke controle.
- **Favicon:** `favicon.ico` (root), `assets/images/favicon.svg` en `assets/images/apple-touch-icon.png`. Nu een tijdelijk hartje; vervang alle drie als er een definitief icoon is.

## Typografie

Montserrat wordt gebruikt voor alle tekst. Alleen het losse woord Tess wordt automatisch in Caveat gezet, ook in inhoud uit het Panel. De lokale WOFF2-bestanden en fontlicenties staan in `assets/fonts`; er worden geen externe fontverzoeken gedaan door bezoekers.

De homepage heeft de goedgekeurde doorlopende pastelgradiënt; de header is transparant bovenaan en wit na scrollen. Het aangeleverde logo heeft witte letters; er staat tijdelijk een fijne donkere schaduw om de letters leesbaar te houden op lichte vlakken. Voor de definitieve versie is een transparante variant met donkere letters wenselijk.

## Donatievoortgang en rekeningnummer

De voortgangsbalk blijft zichtbaar zonder bevestigde cijfers, met XX.XXX, 14% en voorbeeldmijlpalen die expliciet als voorlopig worden aangeduid. Vul opgehaald bedrag en doelbedrag in bij Donaties om de echte voortgang te tonen. Mijlpalen zijn afzonderlijk bewerkbaar. Het rekeningnummer BE97 7340 5272 3549 staat bij Help Tess en in de footer en is via Instellingen aanpasbaar.

## Tailwind ontwikkelen

De styling gebruikt Tailwind CSS. De regel is:

- **In de templates en snippets** (`site/templates`, `site/snippets`): utility-klassen voor alles, dus layout, ruimte, kleur, typografie, responsive gedrag, hover- en focusstijlen en transities.
- **In `assets/css/tailwind.css`** staat alleen wat utilities niet (overzichtelijk) kunnen:
  1. design tokens in `@theme` (kleuren, lettertypes, breekpunten, animaties) en basisstijlen voor gewone HTML-elementen;
  2. inhoud die de templates niet zelf opbouwen: rijke tekst uit het Panel (`.prose`) en de automatische opmaak van de naam Tess (`.tess-name`, toegevoegd door de plugin `tess-typography`);
  3. pseudo-elementen die iets tekenen (stapnummers, lijnen in de tijdlijnen, het +/−-teken bij uitklapvragen) en onderdelen van de browser zelf (de voortgangsbalk, het standaardpijltje van `details`);
  4. beweging die JavaScript aanzet (het verschijnen bij scrollen, `.reveal`);
  5. twee herbruikbare effecten als eigen utility: `lift` (knop komt omhoog bij hover) en `link-underline` (onderlijn schuift in).
- **Markup die op meerdere plaatsen terugkomt** is een Kirby-snippet met de utility-klassen erin, geen CSS-klasse: `snippet('components/button')` voor de roze knop (varianten `solid`/`outline`, maten `default`, `block`, `small`, `header`, `large`), `snippet('components/heading')` voor de koppen op de homepage en `snippet('components/picture')` voor foto's met placeholder.
- **Mappen in `site/snippets`:** `layout/` (header, footer en oproepblok, op elke pagina), `components/` (herbruikbare onderdelen zoals knop, kop, foto, icoon en updatekaart), `home/` en `disclaimer/` (onderdelen die alleen op die pagina staan), `blocks/` (weergave van Panel-blokken; deze mapnaam is een vaste Kirby-afspraak) en `emails/` (opmaak van de e-mails).
- **HTML-e-mails** gebruiken inline stijlen, omdat mailprogramma's geen stylesheets ondersteunen.

Klassenamen zonder utility-vorm bestaan alleen voor die vijf gevallen. JavaScript vindt elementen via `data-`-attributen (zoals `data-menu-toggle`, `data-reveal-group` en `data-donation-bar`), niet via stijlklassen. Breekpunten: `xs` (361px), `md` (681px), `lg` (901px), `xl` (1001px) en `2xl` (1101px).

```sh
npm install
npm run dev
```

`npm run dev` houdt de Tailwind-build actief tijdens het ontwikkelen. Start Kirby daarnaast in een andere terminal met `composer start`, of gebruik je bestaande Herd-site.

```sh
npm run build
```

De productiebuild schrijft de gecomprimeerde CSS naar `assets/css/site.css`. Bewerk dit gegenereerde bestand niet rechtstreeks. Het bestand blijft in Git zodat de website zonder Node.js op de PHP-host kan draaien. Node.js en de npm-pakketten zijn alleen nodig om de styling opnieuw te bouwen. Er wordt geen Tailwind-CDN gebruikt. Fonts worden lokaal in dezelfde CSS-bundel geladen.

## Teksten beheren in Kirby

Alle redactionele teksten staan in de contentbestanden en hebben velden in het Panel. De homepage gebruikt `content/home/home.nl.txt`; de andere pagina's gebruiken hun eigen contentbestand (`story`, `treatment`, `faq`, `legal`, `disclaimer`). Zonder eigen template gebruiken ze `site/templates/default.php`. Footer, oproepblok, donatiebedragen en het traject staan in `content/site.nl.txt`. Vaste interfaceteksten (knoppen, formulierlabels, meldingen) staan per taal in `site/languages`. PHP bevat de opbouw en logica.


## Goedgekeurd design en kleuren (16 oktober 2026)

De frontend volgt samen-voor-tess-preview-1.pdf. De exacte kleuren uit Kleurenpallet.pdf zijn: achtergrond boven #F5D7E6, midden #FFF6CE en onder #C8EEF3; titels/accenten #C43B75, gewone tekst #27222A en kaarten #FFFFFF. De verticale gradiënt loopt over de hele homepage. De desktophero heeft tekst en een losse portretfoto naast elkaar; op mobiel staan ze onder elkaar. Foto's blijven via de bestaande Kirby-velden instelbaar. Bestaande voorbeeldinhoud en logo-assets zijn niet vervangen.

### Medische weegschaal beheren

Ga in het Panel naar het tabblad Traject. Vul onder Medische weegschaal alleen bevestigde onderzoeken/behandelingen in en kies per item Al gedaan of Nog te doen. Titel en uitleg boven de weegschaal staan bij Home → Traject. Gewicht staat standaard op 1. Een hoger gewicht telt een stap zwaarder mee op de schaal: dit is geen bedrag en geen percentage genezing. De schaal reageert uitsluitend op deze behandelstappen; opgehaalde donaties blijven afzonderlijk in de bestaande progressbar.

De optionele bevestigde bedragen staan uitsluitend in de informatiekaarten Al gedaan en Nog te doen. Onder de schaal staan geen geldlabels. Zonder medische invoer staat de schaal neutraal en tonen de kaarten dat voortgang nog niet bevestigd is.
De homepage behoudt de drie meest recente gepubliceerde updates (de PDF toont er twee). Het gedeelde overzicht blijft alle gepubliceerde updates tonen. Dit behoudt de bestaande inhoudsselectie.

### Bestaande werking

Betalen blijft via de bestaande externe steunactie verlopen; er is geen nieuwe betaalintegratie of bevestigingsmail toegevoegd. De bedragkeuze wordt nog steeds niet doorgestuurd naar de externe site. Het contactformulier blijft uitgeschakeld zolang geen contactadres is ingevuld. Delen, taalkeuze, updategalerijen en het mobiele menu blijven behouden. Nieuw is de progressief verbeterde IBAN-kopieerknop; bij mislukken blijft het nummer zichtbaar om zelf te kopiëren.
