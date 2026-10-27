# Mollie-integratie: taken

Online doneren via Mollie op de homepage, met een optioneel bericht voor Tess en een bedankmail via Brevo. We bouwen dit stap voor stap, fase per fase. Elke fase heeft een "Klaar als" zodat je weet wanneer je verder kunt.

De opdrachtgever heeft nog geen Mollie-account. We bouwen en testen daarom op een eigen testserver met een eigen Mollie-testsleutel. **Zolang er geen sleutel is ingesteld, werkt de site precies zoals nu** (knop naar Steunactie). We kunnen dus veilig deployen voordat Mollie er is.

**Stand van zaken (22-10-2026):** fase 1 tot en met 10 zijn gebouwd en lokaal getest met gesimuleerde Mollie-betalingen (validatie, status, één mail, totalen, Panel-acties, muur). Nog niet getest: een echte checkout met een `test_`-sleutel, de webhook vanaf Mollie (kan pas op de testserver) en het Panel in de browser.

**Lokaal testen met een echte testsleutel:** zet `'tess.mollie.key' => 'test_…'` in `site/config/config.theagency2627-steunactie-tess.test.php`. De webhook wordt lokaal weggelaten; de bedankpagina haalt de status zelf op. Mails gaan naar Mailpit (http://localhost:8025), dat moet dan wel draaien.

## Gemaakte keuzes

| Onderwerp | Keuze |
|---|---|
| Mollie-koppeling | Officiële SDK `mollie/mollie-api-php`. `kirby/` en `vendor/` staan niet in Git: `composer install` bij elke deploy (zie IMPLEMENTATIE.md, Composer-pakketten) |
| API-sleutel | In het hostconfigbestand `site/config/config.{domein}.php`, zoals de Brevo-login. Kirby kent geen `.env`; dit bestand staat in `.gitignore`. |
| Opslag | SQLite-bestand `site/storage/donations.sqlite` via Kirby's ingebouwde `Kirby\Database\Database`. Geen databaseserver, geen extra pakket. |
| Soort donatie | Alleen eenmalig, € 1 tot € 10.000, hele euro's (instelbaar) |
| Betaalstatus | **Webhook** is de bron van waarheid; de bedankpagina controleert één keer extra als vangnet |
| Mails | Alleen een bedankmail naar de donateur, in diens taal. Knop "Opnieuw versturen" in het Panel als verzenden mislukt. |
| Berichtenmuur | Naam + bedrag + bericht; de donateur kan naam en/of bedrag verbergen |
| Moderatie | Berichten verschijnen meteen. Berichten met een link, e-mailadres of telefoonnummer worden automatisch tegengehouden tot iemand ze goedkeurt. |
| Panel | Eigen menu-item "Donaties": tabel met filters, zoeken, tonen/verbergen, tekst bewerken, goedkeuren, mail opnieuw versturen |
| Totaal | Betaalde Mollie-donaties tellen automatisch mee als vierde bron in de voortgangsbalk |
| Testmodus | Met een `test_`-sleutel staat er een duidelijke strook "TESTMODUS" op de site en in het Panel |

## Fase 0: Voorbereiding

- [x] Testserver met HTTPS en PHP 8.2 of hoger, publiek bereikbaar (Mollie moet de webhook kunnen aanroepen). Zie [Testserver](#testserver).
- [x] Op de testserver na elke deploy `composer install --no-dev --optimize-autoloader` uitvoeren (of `kirby/` en `vendor/` lokaal installeren en uploaden). Beide mappen staan niet in Git; zonder die mappen werkt de site niet.
- [x] Combell: SQLite staat standaard aan. Er is geen databaseserver nodig, SQLite is gewoon een bestand.
- [x] Testserver: PHP-extensie `pdo_sqlite` staat aan (`php -m` via SSH).
- [x] Draait de testserver op nginx in plaats van Apache: `/site` blokkeren. Niet nodig: nginx staat vóór Apache en `.htaccess` werkt; `/site`, `/content`, `/kirby`, `/vendor`, `.git` en de `.md`-bestanden geven 403 of 404.
- [x] Eigen Mollie-account aanmaken, testsleutel (`test_…`) kopiëren uit Developers → API keys.
- [x] `config.{testdomein}.php` op de testserver: Brevo-login (zie IMPLEMENTATIE.md) en `'tess.mollie.key' => 'test_…'`.
- [ ] Brevo: testdomein of afzender toegelaten, zodat de bedankmail niet geweigerd wordt. Op de testserver tijdelijk `steunactietess@mctantwerp.be` (de enige geverifieerde afzender); nog testen.

**Klaar als:** het contactformulier op de testserver een mail verstuurt en `pdo_sqlite` beschikbaar is.

### Testserver

**Stand van zaken (27-10-2026):** de site draait op https://samenvoortess.wout.philippo.kdgmt.be (Combell-subsite op de eigen hosting). Getest van buitenaf: geldig Let's Encrypt-certificaat (tot 25-01-2027), http → https met 301, `X-Robots-Tag: noindex, nofollow`, `robots.txt` met `Disallow: /`, alle beveiligingsheaders (CSP nog Report-Only), interne bestanden onbereikbaar, webhook met een verzonnen ID geeft 200 zonder redirect. **Volgende stap: inloggen in het Panel** (zie hieronder).

| Wat | Waarde |
|---|---|
| Adres | https://samenvoortess.wout.philippo.kdgmt.be |
| SSH | `ssh <gebruiker>@<ssh-host>`, wachtwoord van de primaire FTP-gebruiker. `ssh.wout.philippo.kdgmt.be` bestaat (nog) niet in DNS. |
| Map op de server | `<webroot>/samenvoortess.wout.philippo.kdgmt.be` (de repo is hier rechtstreeks gekloond, de webroot) |
| GitHub | Clone via de deploykey met alias `samenvoortess` in `~/.ssh/config` op de server: `git@samenvoortess:mctantwerp/theagency2627-steunactie-tess.git` |
| PHP | 8.4.27, Composer 2.10, `pdo_sqlite`, `gd`, `curl`, `mbstring` |
| Hostconfig | `site/config/config.samenvoortess.wout.philippo.kdgmt.be.php`: lokaal bewerken, uploaden met `scp` (zie hieronder), op de server `chmod 600`. Staat niet in Git. |
| Brevo | Account dat later naar de opdrachtgever gaat. SMTP-key `samenvoortess-staging`. Enige geverifieerde afzender: `steunactietess@mctantwerp.be` (DKIM en DMARC niet ingesteld, mails kunnen in spam belanden). |
| Panel-account | Lokaal aangemaakt (`site/accounts/Xgo5LjyQ`) en geüpload, zonder `.logins`. |

**Deployen** (in de map op de server):

```sh
git pull
composer install --no-dev --optimize-autoloader
```

**Hostconfig uploaden** (lokale PowerShell, vanuit de projectmap):

```powershell
scp site\config\config.samenvoortess.wout.philippo.kdgmt.be.php <gebruiker>@<ssh-host>:<webroot>/samenvoortess.wout.philippo.kdgmt.be/site/config/
```

**Nog te doen, in deze volgorde:**

- [ ] Hostconfig opnieuw uploaden (afzender `steunactietess@mctantwerp.be` toegevoegd voor de inlogcode en het contactformulier) en op de server `chmod 600`.
- [ ] Inloggen op `/panel`. De code komt per mail van `steunactietess@mctantwerp.be`; kijk ook in spam. Komt er niets: Brevo → Transactional → Logs. Zonder de afzender stuurt Kirby de code van `noreply@{host}`, en dat weigert Brevo.
- [ ] Na het inloggen meteen Account → Tweestapsverificatie → authenticator-app instellen.
- [ ] De wijziging in `config.php` committen en pushen (inlogcode van `contact@samenvoortess.be` in plaats van `noreply@…`), daarna op de server deployen.
- [ ] Contactformulier testen (Fase 0, Klaar als).
- [ ] Testbetaling van begin tot eind; webhook-logs in het Mollie-dashboard (B-14, B-15 in TASKS-CONTROLE.md).
- [ ] Daarna de [test]-punten van TASKS-CONTROLE.md, stap 4 van de Volgorde.

**Site niet bereikbaar op het KdG-netwerk?** De DNS van KdG had het oude, foute IP (<ip>) in de cache. Gebruik 4G of een hotspot, of zet tijdelijk `<ip> samenvoortess.wout.philippo.kdgmt.be` in `C:\Windows\System32\drivers\etc\hosts` (Kladblok als administrator) en haal die regel later weer weg.

**Bij de overdracht van het Brevo-account:** e-mailadres, wachtwoord, telefoonnummer, 2FA en facturatiegegevens naar de opdrachtgever zetten, en voor de live site een nieuwe SMTP-key maken (de key van de testserver niet live gebruiken).

## Fase 1: Basis

- [x] `composer require mollie/mollie-api-php` (v3). Alleen `composer.json` en `composer.lock` committen; `kirby/` en `vendor/` staan in `.gitignore`.
- [x] Plugin `site/plugins/tess-donations/index.php` met opties `tess.donations.*`: `min` 1, `max` 10000, `messageLength` 500, `limit` 10 per `window` 60 minuten, `database` (pad naar het SQLite-bestand, standaard `site/storage/donations.sqlite`) en `webhookUrl` (standaard automatisch). De sleutel zelf is `tess.mollie.key`.
- [x] `'tess.mollie.key' => 'test_…'` als voorbeeld toevoegen aan `site/config/config.host.example.php`.
- [x] Site-methodes `mollieEnabled()` (sleutel ingevuld) en `mollieTestMode()` (sleutel begint met `test_`).
- [x] Helper `tessDonationsDb()`: opent of maakt `site/storage/donations.sqlite`, zet `PRAGMA journal_mode=WAL` en `busy_timeout`, voert migraties uit op basis van `PRAGMA user_version`.
- [x] Migratie v1, tabel `donations`:

  | Kolom | Inhoud |
  |---|---|
  | `id` | integer, primary key |
  | `reference` | willekeurige code (uniek), komt in de URL van de bedankpagina |
  | `payment_id` | Mollie-ID `tr_…` (uniek) |
  | `amount_cents` | bedrag in cent (integer, nooit float) |
  | `name`, `email`, `message` | invoer van de donateur |
  | `show_name`, `show_amount` | 0/1, keuze van de donateur |
  | `message_status` | `visible`, `held` of `hidden` |
  | `payment_status` | `open`, `paid`, `failed`, `canceled`, `expired`, `refunded` |
  | `method`, `language` | betaalmethode, taalcode |
  | `created_at`, `updated_at`, `paid_at`, `mailed_at` | tijdstippen (UTC) |
  | `mail_error` | laatste foutmelding bij verzenden, anders leeg |

- [x] `/site/storage` toevoegen aan `.gitignore` (donateursgegevens horen nooit in Git).

**Klaar als:** de site zonder sleutel ongewijzigd werkt en met sleutel een lege database wordt aangemaakt.

## Fase 2: Formulier

Twee stappen: in het donatieblok op de homepage kies je een bedrag, op de donatiepagina `/doneren` vul je de rest in. Alle donatieknoppen (header, hero, oproepblok, footer, kaart "Doneer direct") gaan naar het donatieblok.

- [x] Homepage, `site/snippets/home/donation-form.php`: als `mollieEnabled()`, een `<form method="get">` rond de bedragknoppen en het veld Ander bedrag (`name="amount"`, `required`) met knop "Doneer nu" naar `/doneren?amount=…`.
- [x] Donatiepagina `content/doneren` (blueprint `donate.yml`, template `donate.php`, `noindex`): bedrag (ingevuld, nog aanpasbaar), naam, e-mailadres (verplicht, voor de bedankmail), bericht (optioneel, max. 500 tekens), vinkjes "Toon mijn naam" en "Toon mijn bedrag" (beide standaard aan), knop "Verder naar betalen". Zonder sleutel stuurt de pagina door naar het donatieblok.
- [x] Spambeveiliging zoals het contactformulier: verborgen veld `website`, verborgen `started`, controle op Origin. Hiervoor `tessContactAllow()` in `site/plugins/tess-contact` algemener gemaakt: `tessAllow($plugin)`, `tessForeignOrigin()`, `tessLooksLikeBot($plugin)` en `tessField($key)`.
- [x] Daaronder "Liever op een andere manier?" met IBAN, Steunactie en GoFundMe zoals nu.
- [x] Zonder sleutel: de huidige markup blijft ongewijzigd.
- [x] `assets/js/site.js`: tekenteller bij het bericht, knop uitschakelen tijdens verzenden. Het formulier werkt ook zonder JavaScript.
- [x] Foutmelding via `?donation=…` bovenaan de donatiepagina, het gekozen bedrag blijft ingevuld.
- [x] Nieuwe vertalingen in `site/languages/nl.php` (en waar mogelijk en/fr).

**Klaar als:** het formulier verschijnt met sleutel, verdwijnt zonder sleutel, en ongeldige invoer al in de browser wordt tegengehouden.

## Fase 3: Betaling aanmaken

- [x] Route `POST doneren` (`language: *`).
- [x] Server-side valideren: bedrag geheel getal tussen min en max, naam max. 120 tekens op één regel, geldig e-mailadres, bericht max. 500 tekens, honeypot, minimale invultijd, rate limit.
- [x] Automatisch tegenhouden: bericht met link, e-mailadres of telefoonnummer (regex) krijgt `message_status = held`, anders `visible`.
- [x] Rij invoegen met `payment_status = open` en een nieuwe `reference`.
- [x] `$mollie->payments->create()` met:
  - `amount`: `['currency' => 'EUR', 'value' => '25.00']` (string met twee decimalen)
  - `description`: "Donatie Samen voor Tess"
  - `redirectUrl`: bedankpagina met `?ref={reference}`
  - `webhookUrl`: `{site}/mollie/webhook` (weglaten op lokaal `.test`, Mollie weigert onbereikbare URL's)
  - `locale`: `nl_BE`, `fr_BE` of `en_GB` volgens de taal
  - `metadata`: `['reference' => …]`
  - geen `method`, zodat Mollie alle actieve betaalmethodes toont
- [x] `payment_id` opslaan, 303-redirect naar `$payment->getCheckoutUrl()`.
- [x] Bij een fout van Mollie: loggen en terug naar `#help-tess` met `?donation=error`.

**Klaar als:** je na het formulier op de Mollie-testcheckout landt en de rij in de database staat.

## Fase 4: Webhook en synchronisatie

- [x] Route `POST mollie/webhook`: lees alleen `id` uit de body en haal de betaling **altijd opnieuw op bij Mollie** (webhooks zijn niet ondertekend, de inhoud is dus niet te vertrouwen).
- [x] Donatie zoeken via `metadata.reference` en controleren dat `payment_id` overeenkomt.
- [x] `tessDonationSync($reference, $payment)`, idempotent. In plaats van een `flock` gebruikt het een SQLite-transactie `BEGIN IMMEDIATE`, die maar één proces tegelijk toelaat (webhook en bedankpagina kunnen tegelijk lopen):
  - `payment_status` bijwerken; volledig terugbetaald → `refunded`
  - `paid_at` en `method` invullen bij betaald
  - bij betaald en `mailed_at` leeg: bedankmail versturen (fase 6)
  - cache van het totaal wissen (fase 7)
- [x] Antwoord 200, ook bij een onbekend ID. Bij een fout van de Mollie API antwoord 500, zodat Mollie het later opnieuw probeert.

**Klaar als:** een testbetaling op "Paid" zetten in de Mollie-checkout de status in de database binnen enkele seconden op `paid` zet.

## Fase 5: Bedankpagina

- [x] `content/bedankt/thanks.nl.txt` met blueprint `site/blueprints/pages/thanks.yml`: aparte teksten voor betaald, nog in verwerking en mislukt/geannuleerd, aanpasbaar in het Panel.
- [x] `site/templates/thanks.php`: leest `?ref`, controleert de status één keer bij Mollie (vangnet als de webhook nog niet binnen is), toont de juiste tekst. Onbekende ref: algemene tekst. `<meta name="robots" content="noindex">`.
- [x] Bij mislukt: knop terug naar het donatieblok.

**Klaar als:** elke status uit de Mollie-testcheckout de juiste tekst toont.

## Fase 6: Bedankmail

- [x] `site/templates/emails/donation.html.php` en `donation.text.php`, met de bestaande opmaak `site/snippets/emails/layout.php`.
- [x] Inhoud: bedankje, bedrag, datum, Mollie-referentie, het bericht. Bij `held`: "je bericht verschijnt na controle".
- [x] In de taal van de donateur (`t($key, null, $language)`, valt terug op Nederlands).
- [x] Afzender `tess.contact.from` / `fromName`, Reply-To het contactadres uit Instellingen.
- [x] Gelukt: `mailed_at` invullen. Mislukt: `mail_error` invullen en loggen; de betaling blijft gewoon betaald.
- [x] Nooit twee keer versturen: alleen als `mailed_at` leeg is, binnen de lock.

**Klaar als:** na een betaalde testdonatie precies één mail aankomt (ook als de webhook twee keer komt en de bedankpagina herladen wordt).

## Fase 7: Totaal en overzicht

- [x] Site-methode `mollieTotals()`: `['raised' => …, 'donations' => …]` over alle `paid` rijen, gecachet in `tess.donations` en gewist bij elke statuswijziging.
- [x] `raisedTotal()` en `onlineDonations()` in `site/plugins/tess-fundraisers/index.php` tellen dit mee (alleen als Mollie actief is).
- [x] `site/blueprints/tabs/donations.yml`: alleen-lezen infoveld "Online via de site: € X (n donaties)".
- [x] `site/plugins/tess-panel/index.php` (Overzicht): tegel "X berichten wachten op goedkeuring" als er `held` berichten zijn.

**Klaar als:** de voortgangsbalk en het Overzicht in het Panel hetzelfde totaal tonen, inclusief de testdonaties.

## Fase 8: Panel "Donaties" (beheren)

- [x] Panel-area in de plugin (`'areas' => ['donations' => …]`) met menu-item "Donaties" en icoon `heart`.
- [x] View met een Vue-component in `site/plugins/tess-donations/index.js`. Het Panel laadt de volledige Vue-build, dus een `template`-string werkt zonder buildstap. Opbouw met `k-panel-inside`, `k-header` en `k-table`.
- [x] Kolommen: datum, naam, bedrag, bericht (ingekort), berichtstatus, betaalstatus, mail verstuurd.
- [x] Filters met aantallen: Betaald (standaard) / Te controleren / Verborgen / Niet betaald. Zoeken op naam, e-mail en bericht. Paginering per 25.
- [x] Acties per rij (dropdown):
  - Bekijken / bewerken: PHP-dialog (`k-form-dialog`) met alle gegevens alleen-lezen en het bericht bewerkbaar (bv. om een telefoonnummer weg te halen)
  - Verbergen / weer tonen
  - Goedkeuren (bij `held`)
  - Bedankmail opnieuw versturen (bij elke betaalde donatie, handig als de donateur de mail niet vond)
- [ ] **Nog in de browser nakijken** (lokaal kon ik niet inloggen in het Panel): tabel, filters, zoeken, dialog en acties.
- [x] Bedrag, betaalgegevens en e-mailadres zijn alleen-lezen.
- [x] Alleen ingelogde Panel-gebruikers (standaard bij een area); elke actie controleert dat de rij bestaat.

**Klaar als:** je in het Panel een bericht kunt verbergen, bewerken en goedkeuren en dat meteen op de homepage ziet.

## Fase 9: Donatiemuur

- [x] `site/snippets/home/donation-wall.php` naast het donatieblok (op de gsm eronder): de laatste 10 betaalde donaties.
- [x] Per donatie (`site/snippets/components/donation-item.php`): naam of `t('donation.anonymous')`, bedrag (alleen als `show_amount`), datum en het bericht, maar alleen als het zichtbaar is (tegengehouden of verborgen berichten blijven weg, de donatie zelf staat er wel). Alles wordt ge-escaped.
- [x] Knop "Bekijk alle donaties (n)" opent een popup die per 20 laadt via de route `donaties/lijst` (JSON), met "Meer laden". Zonder JavaScript blijft de knop verborgen.
- [x] Zonder donaties: "Nog geen online donaties. Wees de eerste!"
- [x] Titel en inleiding als velden in `site/blueprints/pages/home.yml`, tabblad Doneren.

**Klaar als:** een betaalde testdonatie meteen op de muur staat, en het bericht met telefoonnummer pas na goedkeuring.

## Fase 10: Testmodus-strook

- [x] Als `mollieTestMode()`: een opvallende strook "TESTMODUS: er worden geen echte betalingen gedaan" bovenaan de site (`site/snippets/layout/header.php`) en in de Panel-area.

**Klaar als:** de strook verschijnt met een `test_`-sleutel en verdwijnt met een `live_`-sleutel.

## Fase 11: Testen op de testserver

- [ ] Zonder sleutel: homepage ziet er exact uit zoals nu.
- [ ] € 25 met bericht, "Paid" kiezen: juiste bedanktekst, mail komt aan (ook zichtbaar in Brevo → Transactional → Logs), totaal +€ 25, bericht op de muur.
- [ ] "Failed", "Canceled" en "Expired": juiste tekst, geen mail, telt niet mee.
- [ ] Webhook twee keer laten komen en bedankpagina herladen: nog steeds één mail.
- [ ] Bericht met telefoonnummer, e-mailadres of link: tegengehouden tot goedkeuring.
- [ ] "Toon mijn naam" uit → "Anoniem"; "Toon mijn bedrag" uit → geen bedrag.
- [ ] Verbergen en bewerken in het Panel past de muur meteen aan.
- [ ] Brevo-login tijdelijk fout: betaling blijft betaald, `mail_error` gevuld, knop opnieuw versturen werkt na herstel.
- [ ] Terugbetaling in het Mollie-dashboard: status `refunded`, telt niet meer mee.
- [ ] Via `/en` doneren: Engelse checkout en mail.
- [ ] Validatie: € 0, € 10.001, € 2,50, bericht van 501 tekens, ongeldig e-mailadres, honeypot ingevuld, 11 keer binnen een uur.
- [ ] `https://{testdomein}/site/storage/donations.sqlite` is niet bereikbaar.

## Fase 12: Live zetten

- [ ] Opdrachtgever maakt een Mollie-account aan en rondt de verificatie (KYC) voor de vzw af.
- [ ] Betaalmethodes activeren in het Mollie-dashboard.
- [ ] `'tess.mollie.key' => 'live_…'` in `config.samenvoortess.be.php` (en `config.www.samenvoortess.be.php`) op de server.
- [ ] `site/storage` schrijfbaar op Combell (SQLite zelf staat er standaard aan).
- [ ] Testdonaties niet meenemen: de database van de testserver nooit naar productie kopiëren.
- [ ] Eén echte donatie van € 1 doen en terugbetalen via het dashboard.
- [ ] Privacytekst aangepast (zie hieronder).
- [ ] IMPLEMENTATIE.md bijwerken: werking, Panel, sleutel, back-up van `site/storage`.

## Open vragen voor de opdrachtgever

- Mollie-account en verificatie voor de vzw: wie doet het en wanneer?
- Welke betaalmethodes: Bancontact, Payconiq, kaart, iDEAL, …?
- Fiscaal attest: kan alleen voor een erkende instelling. Is de vzw erkend? Zo niet, vermelden dat er geen attest is.
- Privacytekst: welke donateursgegevens we bewaren, hoe lang, en dat berichten publiek zichtbaar zijn.
- Wie modereert de berichten en kijkt de tegengehouden berichten na?

## Later / misschien

- Bericht verwijderen en donateur anonimiseren (AVG-verzoek).
- CSV-export voor de boekhouding.
- Mail naar het team bij een tegengehouden bericht.
- Maandelijkse donaties via Mollie-abonnementen.
