# Samen voor Tess

> [!NOTE]
> **Work in progress.** Aan dit project wordt nog gewerkt. Online doneren werkt van begin tot eind in testmodus, maar de site staat nog niet live: de laatste tests, de echte inhoud van de opdrachtgever en de overstap naar echte betalingen moeten nog gebeuren. Niet alles is al afgewerkt.

Een website voor **Samen voor Tess**, een steunactie die geld inzamelt voor de medische behandeling van Tess. Bezoekers lezen haar verhaal en het behandelingsplan, volgen de updates van de actie, zien hoeveel er al is opgehaald en kunnen online doneren. Het team achter de actie beheert alles zelf in een CMS, zonder code aan te raken.

Het is een klantproject van een studententeam van twee, gemaakt in *The Agency*, een opleidingsonderdeel van de bachelor Multimedia & Creatieve Technologie aan de Karel de Grote Hogeschool (KdG) in Antwerpen (2026–2027).

## Live demo (staging)

**https://samenvoortess.wout.philippo.kdgmt.be**

- Dit is de **stagingserver**, niet de echte site van de actie. Zoekmachines indexeren hem niet.
- **De teksten en afbeeldingen zijn voorbeeldinhoud**, niet de definitieve inhoud.
- **Doneren loopt in de testmodus van Mollie:** je kunt de hele checkout doorlopen, maar er wordt geen echt geld betaald. Een banner op de site meldt dat ook.
- De server kan achterlopen op de code hier, of offline zijn, want hij draait op studentenhosting.

## Functies

- **Online doneren via Mollie.** Donateurs kiezen een bedrag en betalen in de checkout van Mollie. Een webhook houdt de betaalstatus bij, en donateurs krijgen een bedankpagina en een bedankmail. Mislukte, geannuleerde of terugbetaalde betalingen tellen niet mee in het totaal.
- **Donatiemuur.** Donateurs kunnen een bericht achterlaten en kiezen of hun naam en bedrag zichtbaar zijn. Berichten met een link, e-mailadres of telefoonnummer verschijnen pas nadat een beheerder ze heeft goedgekeurd.
- **Voortgang van de inzameling.** Het totaal telt de online donaties op bij de bedragen van de externe inzamelpagina's van de actie. Het voedt de voortgangsbalken op de homepage en in het behandelingsplan.
- **Inhoud beheren in het Kirby Panel.** Redacteurs beheren de pagina's, de updates (opgebouwd uit contentblokken), het behandelingsplan en het donatieoverzicht. Er zijn aparte rollen voor redacteurs en beheerders, en inloggen vraagt een tweede stap (2FA).
- **Contactformulier** met spambeveiliging (honeypot, origin-controle, limiet op het aantal inzendingen), verstuurd via Brevo SMTP.
- **Meertalige interface** (Nederlands, Frans, Engels).
- **Toegankelijkheid, SEO en beveiliging** zijn nagekeken aan de hand van WCAG 2.2 AA en een uitgeschreven checklist: semantische HTML, toetsenbordnavigatie, metadata en previews bij het delen, security headers, en geen geheimen in de repository.
- **Unittests** dekken de logica van de plugins: de betaalstatus van donaties, het modereren van berichten, de formulierbeveiliging, het uitlezen van de inzamelpagina's en de databasemigraties.

## Techniek

| Onderdeel | Gebruikt |
|---|---|
| CMS | [Kirby](https://getkirby.com) 5 (PHP), inhoud als tekstbestanden |
| Styling | Tailwind CSS 4 |
| JavaScript | Vanilla ES-modules, gebundeld met esbuild |
| Betalingen | Mollie API (PHP-client), donaties in SQLite |
| E-mail | Brevo SMTP-relay; Mailpit voor lokale ontwikkeling |
| Tests | PHPUnit |

Alle eigen logica zit in Kirby-plugins in `site/plugins/`: `tess-donations` (Mollie, donatiemuur, Panel-weergave), `tess-contact`, `tess-fundraisers`, `tess-seo`, `tess-panel`, `tess-typography` en `tess-dates`.

## Team

| | Belangrijkste bijdragen |
|---|---|
| **Wout Philippo** ([@Wout-1212](https://github.com/Wout-1212)) | Doneren via Mollie en de donatiemuur, opzet van het Panel en de rollen, contactformulier en e-mail, totalen uitlezen van de inzamelpagina's, controle van toegankelijkheid, SEO en beveiliging, unittests, stagingserver, en daarnaast templates, styling en animaties |
| **Ceyda Yilmaz** | Eerste uitwerking van de wireframes in Kirby en Tailwind, navigatie en mobiel menu, lay-out en leesbaarheid, logo, ontwerp van de footer en de donatiesectie, animaties |

## Gebruik van AI

Mijn deel van het project heb ik AI-first gebouwd: het grootste deel van de code is geschreven met Claude Code (Anthropic). Ik bepaalde wat er gebouwd werd en hoe, las alle code na, zorgde dat ik begreep wat ze doet en testte ze met unittests en op de stagingserver.

## Status en volgende stappen

**Klaar:** de structuur en het ontwerp van de site, de opzet van het CMS, de volledige donatieflow in testmodus (checkout, webhook, bedankpagina en -mail, donatiemuur, totalen), het contactformulier, de vertaalde interfaceteksten, een stagingserver, en het grootste deel van het werk rond toegankelijkheid, SEO en beveiliging.

**Nog te doen:**
- Alles van begin tot eind testen op de stagingserver: elke betaalstatus, terugbetalingen, moderatie en de Engelstalige checkout
- De resterende controles van toegankelijkheid en beveiliging
- De voorbeeldinhoud vervangen door de echte inhoud van de opdrachtgever
- Live gaan op het eigen domein van de actie met echte Mollie-betalingen, en daarna de accounts overdragen aan de opdrachtgever

## Lokaal draaien

Je hebt PHP 8.2 of hoger, Composer en Node.js nodig. Voor de tests is PHP 8.4 of hoger nodig.

```sh
composer install   # installeert Kirby en de PHP-afhankelijkheden
npm install
npm run dev        # Tailwind en esbuild in watch-modus
composer start     # PHP-ontwikkelserver op http://localhost:8000
composer test      # PHPUnit
```

Betalingen en e-mail hebben sleutels nodig in een hostspecifiek configbestand dat niet in deze repository staat. Zie `site/config/config.host.example.php`.

## Documentatie

- [IMPLEMENTATIE.md](IMPLEMENTATIE.md): hoe alles werkt, inhoud beheren, deployen en onderhoud
- [TASKS.md](TASKS.md): online doneren via Mollie, de gemaakte keuzes en het testplan
- [TASKS-CONTROLE.md](TASKS-CONTROLE.md): de checklist voor toegankelijkheid, SEO en beveiliging

## Over deze repository

Dit is een publieke portfoliokopie van de privérepository van het team op school. In deze kopie zijn de e-mailadressen in de commits geanonimiseerd en zijn de inloggegevens van de server in de documentatie vervangen door placeholders. De code wordt gedeeld om ons werk te tonen; ze mag niet hergebruikt worden. De naam en de inhoud van de actie zijn van de opdrachtgever.
