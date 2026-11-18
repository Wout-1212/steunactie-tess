# Online doneren via Mollie: keuzes en taken

Online doneren via Mollie op de homepage, met een optioneel bericht voor Tess en een bedankmail via Brevo. Gebouwd in fases; elke fase had een "Klaar als". Hoe het nu werkt, staat in [IMPLEMENTATIE.md](IMPLEMENTATIE.md#online-doneren-mollie).

De opdrachtgever heeft nog geen Mollie-account. We testen daarom op een eigen testserver met een eigen Mollie-testsleutel. **Zonder sleutel wordt er niets via Mollie betaald:** `/doneren` meldt dat online doneren binnenkort beschikbaar is en toont de andere manieren (overschrijving, Steunactie en GoFundMe). Deployen kan dus veilig voordat Mollie er is.

**Stand van zaken (18-11-2026):** fase 1 tot en met 10 zijn gebouwd. Op de testserver werken het contactformulier en een testbetaling van begin tot eind (echte checkout met een `test_`-sleutel, webhook van Mollie, bedankmail). Nog open: inloggen in het Panel op de testserver, het menu Donaties in de browser nakijken en de rest van [Fase 11](#fase-11-testen-op-de-testserver). De plugin heeft unittests (`composer test`) voor het tegenhouden van berichten, de Mollie-status en de databasemigraties.

## Gemaakte keuzes

| Onderwerp | Keuze |
|---|---|
| Mollie-koppeling | Officiële SDK `mollie/mollie-api-php` v3, via Composer (zie IMPLEMENTATIE.md, Composer-pakketten) |
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

## Fase 0: Voorbereiding (klaar)

Testserver met HTTPS en PHP 8.4 bij Combell, publiek bereikbaar zodat Mollie de webhook kan aanroepen. `pdo_sqlite` staat aan. nginx staat er vóór Apache, maar `.htaccess` werkt: interne mappen en bestanden geven 403 of 404. Eigen Mollie-account met testsleutel. Brevo-domein `samenvoortess.be` geauthenticeerd; contactmail en bedankmail komen aan vanaf `contact@samenvoortess.be`.

### Testserver

| Wat | Waarde |
|---|---|
| Adres | https://samenvoortess.wout.philippo.kdgmt.be |
| SSH | `ssh <gebruiker>@<ssh-host>`, wachtwoord van de primaire FTP-gebruiker. `ssh.wout.philippo.kdgmt.be` bestaat (nog) niet in DNS. |
| Map op de server | `<webroot>/samenvoortess.wout.philippo.kdgmt.be` (de repo is hier rechtstreeks gekloond, de webroot) |
| GitHub | Clone via de deploykey met alias `samenvoortess` in `~/.ssh/config` op de server: `git@samenvoortess:mctantwerp/theagency2627-steunactie-tess.git` |
| PHP | 8.4.27, Composer 2.10, `pdo_sqlite`, `gd`, `curl`, `mbstring` |
| Certificaat | Let's Encrypt, geldig tot 25-01-2027; http → https met 301 |
| Hostconfig | `site/config/config.samenvoortess.wout.philippo.kdgmt.be.php`: lokaal bewerken, uploaden met `scp` (zie hieronder), op de server `chmod 600`. Bevat `tess.noindex` en `tess.contact.to`. Staat niet in Git. |
| Brevo | Account dat later naar de opdrachtgever gaat (login nu `steunactietess@mctantwerp.be`). SMTP-key `samenvoortess-staging`. Domein `samenvoortess.be` geauthenticeerd (DKIM via `brevo1`/`brevo2._domainkey`, DMARC `p=quarantine`); afzender `contact@samenvoortess.be`, de standaard uit `config.php`. |
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

**Nog te doen op de testserver:**

- [ ] Inloggen op `/panel`. De code komt per mail van `contact@samenvoortess.be`; kijk ook in spam. Komt er niets: Brevo → Transactional → Logs.
- [ ] Na het inloggen meteen Account → Tweestapsverificatie → authenticator-app instellen.
- [ ] Panel → Donaties nakijken (zie [Fase 8](#fase-8-panel-donaties)).
- [ ] De rest van [Fase 11](#fase-11-testen-op-de-testserver) en de [test]-punten van TASKS-CONTROLE.md (stap 4 van de Volgorde).

**Site niet bereikbaar op het KdG-netwerk?** De DNS van KdG had het oude, foute IP (<ip>) in de cache. Gebruik 4G of een hotspot, of zet tijdelijk `<ip> samenvoortess.wout.philippo.kdgmt.be` in `C:\Windows\System32\drivers\etc\hosts` (Kladblok als administrator) en haal die regel later weer weg.

## Fase 1: Basis (klaar)

Plugin `site/plugins/tess-donations` met de opties `tess.donations.*` (zie IMPLEMENTATIE.md) en de sleutel `tess.mollie.key`. Site-methodes `mollieEnabled()` (sleutel ingevuld) en `mollieTestMode()` (sleutel begint met `test_`). `tessDonationsDb()` opent of maakt de database, zet `PRAGMA journal_mode=WAL` en `busy_timeout`, en voert migraties uit op basis van `PRAGMA user_version`; een mislukte migratie laat niets achter. `/site/storage` staat in `.gitignore`: donateursgegevens horen nooit in Git.

Tabel `donations` (migratie v1):

| Kolom | Inhoud |
|---|---|
| `id` | integer, primary key |
| `reference` | willekeurige code (20 hex-tekens, uniek), komt in de URL van de bedankpagina |
| `payment_id` | Mollie-ID `tr_…` (uniek) |
| `amount_cents` | bedrag in cent (integer, nooit float) |
| `name`, `email`, `message` | invoer van de donateur |
| `show_name`, `show_amount` | 0/1, keuze van de donateur |
| `message_status` | `visible`, `held` of `hidden` |
| `payment_status` | `open`, `paid`, `failed`, `canceled`, `expired`, `refunded` |
| `method`, `language` | betaalmethode, taalcode |
| `created_at`, `updated_at`, `paid_at`, `mailed_at` | tijdstippen (UTC) |
| `mail_error` | laatste foutmelding bij verzenden, anders leeg |

## Fase 2: Formulier (klaar)

Twee stappen: in het donatieblok op de homepage (`site/snippets/home/donation-form.php`) kies je een bedrag, op `/doneren` (blueprint `donate.yml`, template `donate.php`, `noindex`) vul je naam, e-mailadres, een optioneel bericht (max. 500 tekens) en de vinkjes "Toon mijn naam" en "Toon mijn bedrag" in. Alle donatieknoppen gaan naar het donatieblok. Spambeveiliging zoals het contactformulier; daarvoor zijn de functies in `tess-contact` algemeen gemaakt (`tessAllow($plugin)`, `tessForeignOrigin()`, `tessLooksLikeBot($plugin)`, `tessField($key)`). Zonder sleutel toont `/doneren` in plaats van het formulier de melding "binnenkort beschikbaar" met de andere manieren, en staat er geen donatiemuur op de homepage. Het formulier werkt zonder JavaScript; met JavaScript is er een tekenteller en wordt de knop uitgeschakeld tijdens het verzenden.

## Fase 3: Betaling aanmaken (klaar)

Route `POST doneren` (`language: *`, `lib/payment.php`). Alles wordt server-side opnieuw gecontroleerd (bedrag, naam max. 120 tekens op één regel, e-mailadres, bericht, honeypot, invultijd, rate limit); bij een fout komt de pagina terug met status 422 en een foutenoverzicht. Berichten met een link, e-mailadres of telefoonnummer krijgen `message_status = held`. De rij wordt ingevoegd als `open`, daarna `$mollie->payments->create()` met:

- `amount` als string met twee decimalen (`'25.00'`), `description` "Donatie Samen voor Tess";
- `redirectUrl` naar de bedankpagina met `?ref={reference}`, en `cancelUrl` met `&canceled=1` erbij;
- `webhookUrl` `{site}/mollie/webhook` (weggelaten op lokaal `.test`: Mollie weigert onbereikbare URL's);
- `locale` `nl_BE`, `fr_BE` of `en_GB`, `metadata` met de `reference`, en geen `method`, zodat Mollie alle actieve betaalmethodes toont.

Daarna 303-redirect naar de checkout. Bij een fout van Mollie: loggen en terug met `?donation=error`.

## Fase 4: Webhook en synchronisatie (klaar)

`POST mollie/webhook` leest alleen `id` en haalt de betaling **altijd opnieuw op bij Mollie**: webhooks zijn niet ondertekend, de inhoud is dus niet te vertrouwen. De donatie wordt gezocht via `metadata.reference` (de SDK v3 geeft die soms als JSON-string; die wordt gedecodeerd) en `payment_id` moet overeenkomen. `tessDonationSync()` (`lib/sync.php`) is idempotent en draait in een SQLite-transactie `BEGIN IMMEDIATE`, die maar één proces tegelijk toelaat (webhook en bedankpagina kunnen tegelijk lopen): status bijwerken (volledig terugbetaald → `refunded`), `paid_at` en `method` invullen, bij betaald en lege `mailed_at` de bedankmail versturen, en de cache van het totaal wissen. Antwoord 200, ook bij een onbekend of verzonnen ID (anders probeert Mollie tot 26 uur opnieuw); bij een fout van de Mollie API 500, zodat Mollie het later opnieuw probeert.

## Fase 5: Bedankpagina (klaar)

`content/bedankt` (blueprint `thanks.yml`) met aparte titel en tekst voor betaald, in verwerking en mislukt/geannuleerd, aanpasbaar in het Panel, met `{{ name }}` en `{{ amount }}` als plaatshouders. De logica staat in `site/controllers/thanks.php`: `?ref` lezen, één keer bij Mollie controleren als de webhook nog niet binnen is, en de status kiezen. `open` telt als "in verwerking" na een gewone terugkeer van de checkout, maar als mislukt na annuleren (`&canceled=1`). Onbekende ref: de mislukt-tekst, zonder gegevens. Bij mislukt een knop terug naar het donatieblok. `noindex`.

## Fase 6: Bedankmail (klaar)

`site/templates/emails/donation.html.php` en `donation.text.php`, met de gedeelde opmaak `site/snippets/emails/layout.php` en het logo `assets/images/logo-email.png`. Inhoud: "Dag {voornaam},", bedankje met bedrag, overzicht (bedrag, datum, betaalmethode, Mollie-ID), het bericht (bij `held`: "verschijnt na controle"), de volgende mijlpaal, deelknoppen, Instagram en een voettekst met contactadres, privacy en donatieverklaring. In de taal van de donateur (valt terug op Nederlands), afzender `tess.contact.from`, Reply-To het contactadres. Gelukt: `mailed_at`; mislukt: `mail_error` en loggen, de betaling blijft betaald. Nooit twee keer: alleen als `mailed_at` leeg is, binnen de transactie. Voorbeeld zonder versturen op `/dev/mail` (alleen met `debug` aan).

## Fase 7: Totaal en overzicht (klaar)

Site-methode `mollieTotals()` (som en aantal van alle `paid` rijen), gecachet en gewist bij elke statuswijziging. `raisedTotal()` en `onlineDonations()` in `tess-fundraisers` tellen het mee zolang Mollie actief is. Alleen-lezen infoveld in het tabblad Donaties; tegel "X berichten wachten op goedkeuring" op het Overzicht (`tess-panel`).

## Fase 8: Panel "Donaties"

Gebouwd: Panel-area met menu-item "Donaties" (icoon `heart`) en een Vue-component in `site/plugins/tess-donations/index.js` (`template`-string, dus `panel.vue.compiler` moet aan blijven). Kolommen datum, naam, bedrag, bericht, berichtstatus, betaalstatus en mail. Filters met aantallen (Betaald, Te controleren, Verborgen, Niet betaald), zoeken en paginering per 25. Acties per rij: bekijken en het bericht bewerken (dialoog met de rest alleen-lezen), verbergen/tonen, goedkeuren en de bedankmail opnieuw versturen. Alleen voor ingelogde gebruikers; elke actie controleert dat de rij bestaat (`lib/panel.php`).

- [ ] **In de browser nakijken op de testserver:** tabel, filters, zoeken, dialoog (de alleen-lezen velden tonen hun waarde) en elke actie.

**Klaar als:** je in het Panel een bericht kunt verbergen, bewerken en goedkeuren en dat meteen op de homepage ziet.

## Fase 9: Donatiemuur (klaar)

`site/snippets/home/donation-wall.php` naast het donatieblok (op de gsm eronder): de laatste 10 betaalde donaties (`components/donation-item.php`) met naam of "Anoniem", bedrag alleen als de donateur dat toestaat, datum en het bericht als het zichtbaar is. Alles ge-escaped. "Bekijk alle donaties (n)" opent een popup die per 20 laadt via `donaties/lijst` (JSON, alleen wat publiek mag); zonder JavaScript blijft die knop verborgen. Titel en inleiding in het Panel (Home → Doneren).

## Fase 10: Testmodus-strook (klaar)

Met een `test_`-sleutel een strook "TESTMODUS: er worden geen echte betalingen gedaan" bovenaan de site (in de header) en in de Panel-area.

## Fase 11: Testen op de testserver

- [x] Testbetaling met "Paid": checkout, webhook van Mollie en bedankmail komen aan.
- [ ] Bij diezelfde betaling: juiste bedanktekst, totaal + het bedrag in de voortgangsbalk en op het Overzicht, bericht op de muur, mail zichtbaar in Brevo → Transactional → Logs.
- [ ] "Failed", "Canceled" en "Expired": juiste tekst, geen mail, telt niet mee.
- [ ] Webhook twee keer laten komen en bedankpagina herladen: nog steeds één mail.
- [ ] Bericht met telefoonnummer, e-mailadres of link: tegengehouden tot goedkeuring.
- [ ] "Toon mijn naam" uit → "Anoniem"; "Toon mijn bedrag" uit → geen bedrag.
- [ ] Verbergen en bewerken in het Panel past de muur meteen aan.
- [ ] Brevo-login tijdelijk fout: betaling blijft betaald, `mail_error` gevuld, knop opnieuw versturen werkt na herstel.
- [ ] Terugbetaling in het Mollie-dashboard: status `refunded`, telt niet meer mee.
- [ ] Via `/en` doneren: Engelse checkout en mail.
- [ ] Validatie: € 0, € 10.001, € 2,50, bericht van 501 tekens, ongeldig e-mailadres, honeypot ingevuld, 11 keer binnen een uur.
- [ ] Zonder sleutel: geen donatiemuur, en `/doneren` toont de melding "binnenkort beschikbaar" met de andere manieren.
- [ ] `https://{testdomein}/site/storage/donations.sqlite` is niet bereikbaar.

## Fase 12: Live zetten

- [ ] Opdrachtgever maakt een Mollie-account aan en rondt de verificatie (KYC) voor de vzw af.
- [ ] Betaalmethodes activeren in het Mollie-dashboard.
- [ ] `'tess.mollie.key' => 'live_…'` in `config.samenvoortess.be.php` (en `config.www.samenvoortess.be.php`) op de server. Er staat lokaal al een versie met een `live_`-sleutel klaar; nakijken dat die van het account van de vzw is.
- [ ] `site/storage` schrijfbaar op de server.
- [ ] Testdonaties niet meenemen: de database van de testserver nooit naar productie kopiëren.
- [ ] Eén echte donatie van € 1 doen en terugbetalen via het dashboard.
- [ ] Privacytekst aangepast (zie hieronder).
- [ ] De algemene stappen in IMPLEMENTATIE.md, [Live zetten](IMPLEMENTATIE.md#live-zetten).

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
- Mollie-SDK 4.0 (grote update, geen beveiligingsfix).
