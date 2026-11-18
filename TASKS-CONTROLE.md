# Controle toegankelijkheid, SEO en beveiliging: taken

Een volledige controle van de site vóór de livegang, opgesteld na het nalezen van de code en onderzoek naar de geldende normen. Elk onderdeel heeft een "Klaar als".

**Stand van zaken (18-11-2026):** alles wat vóór de eerste deploy klaar moest zijn en alle [code]-punten die lokaal konden, zijn gebouwd en lokaal getest (axe, contrast, audits, formulieren, webhook, cookies). De testserver draait; de webhook van Mollie komt er aan (B-14). Wat overblijft: de [test]-punten op de testserver, de [inhoud]-punten en de vragen onderaan.

Beslist: Nederlands is de hoofdtaal, Frans en Engels volgen als de definitieve tekst er is; de huisstijlkleuren blijven en contrast lossen we op met grootte, gewicht, een witte achtergrond of de donkere tekstkleur; lichtsporen oplossing A; `noindex` alleen op de testserver.

Per punt staat wat het werk is:

- **[code]**: aanpassen in de code.
- **[test]**: nakijken, lokaal of op de testserver, met een tool of met de hand. Een probleem wordt dan een [code]-punt.
- **[inhoud]**: moet de opdrachtgever aanleveren of in het Panel invullen.

## Normen

| Onderdeel | Norm | Waarom |
|---|---|---|
| Toegankelijkheid | WCAG 2.2 niveau AA, als eigen kwaliteitsnorm | De Europese toegankelijkheidswet (EAA, van kracht sinds 28 juni 2025) verwijst via EN 301 549 naar WCAG 2.1 AA. Of de vzw met een donatieformulier eronder valt, hangt af van hoe de dienst juridisch gekwalificeerd wordt: laten bevestigen door de vzw of FOD Economie. Hoe dan ook houden we WCAG 2.2 AA aan; die dekt 2.1 volledig. |
| SEO | Google Search Central-richtlijnen | Zoekmachines, en ook hoe de site eruitziet als ze gedeeld wordt via WhatsApp, Facebook en Instagram. Voor een steunactie is delen even belangrijk als gevonden worden. |
| Beveiliging | OWASP Top 10:2025 en de Kirby-beveiligingsgids | De site verwerkt donateursgegevens en betalingen. |

## Gemaakte keuzes

| Onderwerp | Keuze |
|---|---|
| Tools | Lighthouse en axe DevTools (automatisch), W3C-validator, NVDA met Firefox en VoiceOver op iPhone (met de hand), securityheaders.com, Mozilla Observatory en SSL Labs (beveiliging), PageSpeed Insights (snelheid). Gratis en zonder account. |
| Pagina's die we testen | Home, Haar verhaal, Behandelingsplan, FAQ, Updates, één update, Disclaimer, Doneren, Bedankt (betaald, in behandeling, mislukt), 404. Toegankelijkheid in alle drie de talen; de rest in het Nederlands. |
| Testserver | Volledig `noindex`, zodat Google de testserver nooit opneemt. Geen wachtwoord op de hele site, want Mollie moet de webhook kunnen bereiken. |
| Talen | Een taal verschijnt per pagina pas als die pagina vertaald is (zie T-1 en S-4). Bij het vertalen hoeft er niets meer aan de code te gebeuren. |

## Gevonden bij het nalezen van de code

De belangrijkste punten uit de eerste controle, en hoe ze nu staan:

| Gevonden | Nu |
|---|---|
| `'debug' => true` in `config.php` | Opgelost (B-1) |
| `/vendor`, de `.md`-bestanden, `composer.*` en `package.json` bereikbaar | Opgelost met de deploylijst en `.htaccess` (B-4) |
| Geen beveiligingsheaders behalve `X-Content-Type-Options` | Gebouwd, CSP nog Report-Only |
| Geen `url`-optie: Kirby vertrouwde de Host-header | `url` in elk hostconfigbestand (B-2) |
| Geen favicon, Open Graph, canonical, hreflang, `robots.txt` of sitemap | Gebouwd |
| `/fr` en `/en` toonden Nederlandse tekst met `lang="fr"`/`"en"` | Opgelost (T-1) |
| Kleine roze tekst haalt het contrast niet op het verloop | Opgelost |
| Testupdates gepubliceerd in `content/updates` | **Open:** de oude zijn weg, maar `update1` tot `update4` en "Welkom op de website" zijn ook test- of voorbeeldinhoud |
| Foto's op ware grootte, zonder `srcset` | Opgelost |

---

## Deel 0: Testserver afschermen

`noindex` is een instructie voor zoekmachines: "neem deze pagina niet op in de zoekresultaten". Het staat als `<meta name="robots">` in de pagina of als `X-Robots-Tag`-header. Het is geen beveiliging: wie het adres kent, kan de pagina nog openen. Op de live site mag het nooit voor de hele site staan, anders verdwijnt de site uit Google.

- [x] **[code]** Hele testserver `noindex` via `'tess.noindex' => true` in het hostconfigbestand van de testserver (plugin `tess-seo`), zodat het nooit mee naar productie gaat. Op de testserver nagekeken: `X-Robots-Tag: noindex, nofollow`.
- [x] **[code]** `robots.txt` met `Disallow: /` volgt dezelfde optie. Alleen op de testserver: als Google een pagina niet mag ophalen, ziet het de `noindex` ook niet.
- [x] **[test]** Webhook bereikbaar zonder wachtwoord of redirect (B-14).

**Klaar als:** elke pagina van de testserver `X-Robots-Tag: noindex` meestuurt en de productieconfig dat niet doet.

## Deel 1: Toegankelijkheid (WCAG 2.2 AA)

### Automatisch

- [x] **[test]** axe DevTools: 0 fouten op 12 pagina's, 1280 en 390px breed (Nederlands; Frans en Engels bestaan nog niet). Opgelost: kopniveau op Updates, een fotolink zonder naam, twee regio's met dezelfde naam op de homepage.
- [ ] **[test]** Lighthouse (Toegankelijkheid) op de testserver, en axe opnieuw zodra er Franse of Engelse pagina's zijn.
- [ ] **[test]** W3C-validator (validator.w3.org/nu) op elke paginasoort: geen fouten die toegankelijkheid raken (dubbele id's, ongeldige nesting, ARIA op verkeerde elementen).

### Waarneembaar

- [x] **[code]** Contrast tekst (1.4.3, minimaal 4,5:1, of 3:1 vanaf 24px of 18,66px vet). Nagemeten op 11 pagina's, op 1280 en 390px; alleen de foto-placeholders blijven over (die verdwijnen met de echte foto's).

  | Roze #C43B75 op | Contrast | Kleine tekst |
  |---|---|---|
  | wit #FFFFFF | 4,96 | ok |
  | geel #FFF6CE | 4,57 | net ok |
  | blauw #C8EEF3 | 4,01 | **te laag** |
  | roze #F5D7E6 | 3,72 | **te laag** |

  Beslist: de huisstijlkleuren blijven. Grote tekst heeft maar 3:1 nodig en dat haalt het roze overal. Per plaats:

  | Waar | Oplossing |
  |---|---|
  | Naam Tess in Caveat | `font-bold`: vanaf 18,66px telt de naam als grote tekst. In tekst kleiner dan 15px neemt de naam de tekstkleur over. |
  | Footer-koppen | 15px → 19px vet |
  | Omlijnde knoppen ("Lees haar verhaal", "Bekijk alle updates") en terugknoppen | Witte achtergrond: roze op wit = 4,96 |
  | Contactformulier en donatieblok | In een witte kaart; de labels staan op wit |
  | Actief menu-item, knop "Menu" op de gsm | Donkere tekstkleur met een roze onderlijn |
  | Gsm: "Donatievoortgang" en mijlpaalbedragen | Donkere tekstkleur |
  | Panel-tekst | Subkoppen 17,6px → 19,2px; links donker met roze onderlijn |

- [x] **[code]** Contrast van niet-tekst (1.4.11, 3:1): invoervelden hebben een roze rand op 80% (3,60:1 op wit); de focusrand (inktkleur) haalt 3,14:1 op de roze knoppen en meer elders.
- [x] **[test]** Contrast van hover- en focusstaten. Knoppen met `hover:opacity-85` zakken naar ongeveer 3,95:1 en de roze hoverkleur in menu en footer haalt 3,72 à 4,01. **Bewust aanvaard:** de kleur is maar even zichtbaar en de tekst blijft leesbaar. (3:1 tegenover de niet-gefocuste staat is 2.4.13, niveau AAA.)
- [ ] **[test]** Zeer kleine tekst (9-11px bij mijlpalen, hulpteksten, "Meest gekozen"): geen harde WCAG-fout, maar nakijken of het leesbaar blijft. Voorstel minimaal 12px.
- [ ] **[test]** Zoomen tot 200% (1.4.4) en een scherm van 320px breed (1.4.10): niets valt weg, geen horizontaal scrollen (behalve de stappenbalk, die bewust zijwaarts scrolt).
- [ ] **[test]** Tekstafstand (1.4.12) met de bookmarklet "Text spacing": geen tekst die afgesneden wordt of overlapt.
- [ ] **[inhoud]** Alt-teksten (1.1.1): elke foto in het Panel heeft een beschrijvende alt-tekst. De terugvaloptie (paginatitel of kop) zegt vaak niets over de foto. Logo en decoratieve iconen zijn in orde.
- [x] **[test]** Kopstructuur (1.3.1): één `h1` per pagina; updatekaarten zijn `h2` op Updates en `h3` op de homepage.
- [x] **[test]** Landmarks (1.3.1): `header`, twee benoemde `nav`, `main`, `aside`, `footer`; de testmodus-strook staat in de header.
- [x] **[code]** Invoerdoel (1.3.5): `autocomplete` voor naam en e-mail, `autocomplete="off"` bij het bedrag.

### Bedienbaar

- [ ] **[test]** Alles met alleen het toetsenbord (2.1.1, 2.1.2, 2.4.3): menu op gsm, taalkeuze (ook de grijze, niet-vertaalde talen), uitklapvragen, bedragknoppen, donatieformulier, contactformulier, IBAN kopiëren, stappenbalk met pijlen en pijltjestoetsen.
- [ ] **[test]** Popups (delen, fotolightbox, alle donaties, mobiel menu) met toetsenbord en schermlezer: focus gaat erin en blijft erin, Escape en een klik op de achtergrond sluiten, focus keert terug naar de knop, de pagina erachter scrolt niet mee. De drie popups zijn een native `<dialog>` met `showModal()` (vanzelf modaal, dus geen `aria-modal` nodig); het mobiele menu regelt dit zelf met `inert` en een focusval.
- [ ] **[test]** Focus niet verborgen (2.4.11): `scroll-padding-top` volgt de echte hoogte van de sticky header (gebouwd). Nog testen met Tab en Shift+Tab.
- [ ] **[test]** Focus zichtbaar (2.4.7): de globale focusrand (`outline-3 outline-ink`) verschijnt overal, ook op de roze knoppen en in de popups.
- [ ] **[test]** Skiplink (2.4.1): wordt zichtbaar bij Tab en springt naar `main`.
- [ ] **[test]** Klikdoelen minstens 24×24px (2.5.8): footerlinks, links naar Instagram, checkboxen (het label telt mee), knoppen van de stappenbalk, sluitknoppen. Een kleiner doel slaagt ook met genoeg ruimte errond, een gelijkwaardig groter doel dichtbij, of als link in lopende tekst.
- [x] **[code]** Bewegende inhoud (2.2.2): de lichtsporen startten vanzelf en herhaalden zolang de pagina openstond. **Oplossing A gekozen en gebouwd:** elke knop maakt één ronde van 4-4,8 s als hij in beeld komt, daarna alleen bij hover of focus. Zo blijft het effect zichtbaar en valt het binnen de 5 seconden. (B: alleen bij hover, dan zie je het op de gsm bijna nooit. C: een knop "Animaties uit", die weinig mensen zoeken.)
- [ ] **[test]** Animaties (2.3.3): opbouw bij scrollen, optellende cijfers, weegschaal, confetti op de bedankpagina en lichtsporen volgen allemaal `prefers-reduced-motion`. Inhoud is ook zichtbaar zonder JavaScript en bij afdrukken.
- [ ] **[test]** Zonder of met falend JavaScript: de formulieren werken (gewone POST met redirect) en geven nooit een succesmelding als er niets verstuurd is. (Bij een bot geeft het contactformulier bewust "gelukt".)
- [x] **[test]** Paginatitels (2.4.2) en linkteksten (2.4.4): de homepage heet "Samen voor Tess", andere pagina's "Titel | Samen voor Tess"; een updatekaart is één link met de titel erin.

### Begrijpelijk

- [x] **[code]** **T-1: Taal van de pagina (3.1.1).** Een onvertaalde `/fr/…`- of `/en/…`-pagina verwijst tijdelijk (302) door naar de Nederlandse pagina, met de query-string (plugin `tess-seo`). Zo mengt geen enkele pagina twee talen. De taalkeuze in header en footer toont alle talen; een niet-vertaalde taal staat grijs, niet klikbaar, met "nog niet vertaald" in die taal. Lokaal getest met een tijdelijke Franse FAQ.
- [x] **[code]** Taal van onderdelen (3.1.2): vaste Nederlandse woorden in de templates (zoals "Ondernemingsnummer") staan in `site/languages`.
- [x] **[code]** Foutmeldingen in formulieren (3.3.1, 3.3.3): het donatieformulier komt bij een fout terug (status 422) met een foutenoverzicht dat de focus krijgt, een melding onder elk fout veld (`aria-describedby`, `aria-invalid`) en alles wat ingevuld was. Het contactformulier controleert alles al in de browser; een fout van de server toont één melding.
- [x] **[test]** Labels en instructies (3.3.2): hulptekst met minimum en maximum bij het bedrag; het contactformulier vermeldt dat alle velden verplicht zijn.
- [x] **[code]** Geen `autofocus` meer op de donatiepagina (schermlezers sloegen titel en uitleg over); alleen het foutenoverzicht krijgt de focus.
- [ ] **[test]** Statusmeldingen (4.1.3): "Bezig met versturen", succes/fout van het contactformulier, "IBAN gekopieerd" en "Meer laden" in de popup worden één keer voorgelezen, zonder dat de focus verspringt.
- [x] **[test]** Consistente hulp (3.2.6): contact staat op elke pagina op dezelfde plaats (menu en footer).
- [ ] **[test]** Toegankelijke aanmelding (3.3.8): in het Panel werken plakken en wachtwoordbeheerders, en 2FA met een code-app (zie B-5).

### Robuust en schermlezer

- [ ] **[test]** Volledige ronde met NVDA + Firefox (Windows) en VoiceOver (iPhone): homepage van boven tot onder, een donatie van begin tot eind (tot de Mollie-checkout), contactformulier, een update met foto's.
- [x] **[code]** Ongeldige ARIA: `aria-label` op een gewone `div` in de taalkeuze weggehaald; axe vindt geen ARIA-fouten.
- [ ] **[test]** De e-mails (contact, bedankmail): `lang` per taal, leesbaar contrast, platte-tekstversie (bestaat).
- [ ] **[inhoud]** Toegankelijkheidsverklaring (gebouwd: `/disclaimer#toegankelijkheid`, link in de footer, bewerkbaar in het Panel, antwoordtermijn 5 werkdagen). Open: de datum na de controle op de testserver, en de opdrachtgever bevestigt contactadres en termijn.

**Klaar als:** axe geen fouten geeft op alle testpagina's in de drie talen, het contrast overal klopt en een volledige donatie met alleen het toetsenbord en met NVDA lukt.

## Deel 2: SEO en delen

### Technisch

- [x] **[code]** `robots.txt` voor productie met een verwijzing naar de sitemap. Pagina's met `noindex` (doneren, bedankt) worden niet geblokkeerd, anders ziet Google de `noindex` niet.
- [x] **[code]** `/sitemap.xml`: alle gepubliceerde pagina's en updates, per taal met hreflang, zonder `doneren`, `bedankt`, `error` en de oude routes. `lastmod` alleen bij updates, uit hun datum (bestandsdatums veranderen bij elke upload).
- [x] **[code]** Canonical per pagina zonder query-string (`site/snippets/layout/meta.php`), zodat `?contact=…`, `?donation=…` en `?amount=…` geen aparte pagina's zijn. Niet op pagina's met `noindex`.
- [ ] **[test]** Eén URL overal: menu, knoppen, updatekaarten, sitemap, canonical en redirects wijzen naar dezelfde URL (https, het definitieve domein). Lokaal gebruiken alle links `$page->url()`; nog nakijken op de testserver.
- [x] **[code]** **S-4: hreflang** (`nl|fr|en|x-default`), alleen voor talen waarin de pagina vertaald is; `x-default` is Nederlands. Verschijnt zodra een pagina in twee talen bestaat.
- [ ] **[test]** Eén adres per pagina: `http` → `https` (op de testserver in orde) en `www` → zonder www (of omgekeerd) met een 301 naar één vast domein.
- [x] **[test]** De 404-pagina geeft echt statuscode 404 en staat op `noindex`.
- [x] **[test]** Niet-gelinkte pagina's: alle mappen in `content/` zijn bekend; privacy en algemene-voorwaarde verwijzen door, doneren en bedankt staan op `noindex`.
- [ ] **[inhoud]** De test- en voorbeeldupdates weghalen voor de livegang: nu `20261008_update3`, `20261009_update1`, `20261009_update2`, `20261111_update4` en `20261008_welkom-op-de-website`.

### Snelheid (Core Web Vitals)

- [ ] **[test]** PageSpeed Insights / Lighthouse op gsm voor home, een update en Haar verhaal. Doel: LCP < 2,5 s, CLS < 0,1, INP < 200 ms; Lighthouse Prestaties ≥ 90.
- [x] **[code]** `components/picture` en de sectiefoto's: `srcset` van 480 tot 1600px met `sizes`, in het originele formaat (geen WebP, zodat het niet afhangt van wat de server kan).
- [x] **[code]** Herofoto met `fetchpriority="high"` (de LCP-afbeelding op de homepage).
- [x] **[code]** De woff2-bestanden worden voorgeladen; `font-display: swap`.
- [x] **[code]** Browsercache in `.htaccess`: een jaar voor `media/` en de CSS en JS (die hebben een versie in de URL), een week voor lettertypes en beelden in `assets/`. Nog nakijken op de testserver.

### Op de pagina

- [x] **[code]** Paginatitels: de homepage heet "Samen voor Tess" (niet "Home | Samen voor Tess"), of een eigen titel via "Titel in Google". Maximaal ongeveer 60 tekens.
- [ ] **[inhoud]** Beschrijving voor zoekmachines invullen per pagina (tabblad Menu & zoekmachines).
- [x] **[code]** Open Graph en Twitter Card met een deelafbeelding per pagina: die van de pagina, dan een foto van de pagina, dan de standaard uit Instellingen.
- [ ] **[inhoud]** Standaard deelafbeelding 1200×630 aanleveren (logo + foto).
- [x] **[code]** Favicon (`favicon.svg`, `favicon.ico`, `apple-touch-icon`, `theme-color`) met een tijdelijk wit hartje op roze. Vervangen zodra er een definitief icoon is.
- [x] **[code]** Gestructureerde gegevens (JSON-LD): `NGO` en `WebSite` op de homepage, `BlogPosting` per update.
- [ ] **[test]** URL's per taal: nu `/fr/haar-verhaal`. Als we vertalen, de slugs ook vertalen.

### Bij de livegang (niet op de testserver)

- [ ] **[test]** Google Search Console en Bing Webmaster Tools: domein verifiëren, sitemap indienen. Na een week "Pagina-indexering" nakijken: welke pagina's niet geïndexeerd zijn en waarom, en of Google dezelfde canonical kiest. (Het rapport "Mobiele bruikbaarheid" bestaat sinds december 2023 niet meer.) Stappen in IMPLEMENTATIE.md, Vindbaar in Google.
- [ ] **[test]** Controleren dat de `noindex` van de testserver niet meegekomen is.

**Klaar als:** elke pagina een eigen titel, beschrijving, canonical en deelafbeelding heeft, de sitemap valideert, gedeelde links in WhatsApp een voorbeeld met foto tonen en Lighthouse SEO 100 geeft.

## Deel 3: Beveiliging (OWASP Top 10:2025 en Kirby)

### Configuratie (OWASP A02)

- [x] **[code]** **B-1:** `'debug' => false` in `config.php`; `true` alleen in het lokale configbestand.
- [ ] **[code]** **B-2:** `'url' => 'https://…'` in elk hostconfigbestand (gedaan voor de testserver en de klaargezette live config), zodat een vervalste Host-header de Mollie-URL's, mails en Panel-links niet beïnvloedt. Nog nakijken: `UseCanonicalName On` op de server.
- [x] **[code]** **B-3:** `content.salt` en `cookie.key` als lange willekeurige waarden in het hostconfigbestand (kan vanaf Kirby 5.4.5).
- [x] **[code]** **B-4:** alleen de nodige bestanden deployen (deploylijst in IMPLEMENTATIE.md), met als extra laag `.htaccess`-regels voor `vendor/`, `node_modules/`, `state/`, `tests/`, `src/`, `*.md`, `*.json`, `*.lock`, `phpunit.xml` en `Options -Indexes`. Op de testserver nagekeken: 403 of 404.
- [ ] **[code]** **B-5:** Panel met 2FA (`auth.methods` met `2fa` in `config.php`, lokaal uit). Het eerste admin-account is aangemaakt en naar de testserver geüpload; inloggen en de authenticator-app instellen moet nog. Sterke, unieke wachtwoorden; alleen wie het nodig heeft is admin (rol Editor bestaat).
- [x] **[test]** `panel.vue.compiler` moet aan blijven (Donaties-weergave met `template`-string); genoteerd in IMPLEMENTATIE.md.
- [ ] **[test]** Server: PHP 8.3 of 8.4 (ondersteund), `display_errors` uit, `expose_php` uit (lokaal gaf de header `X-Powered-By: PHP/8.4.27`), foutlog buiten de webmap, geen 777-rechten, SFTP in plaats van FTP.

### Beveiligingsheaders

- [x] **[code]** In `.htaccess` met `Header always set`, zodat ze ook op redirects, 404's en foutpagina's staan. Het Panel krijgt de CSP niet (het heeft inline stijlen nodig); de andere headers wel.

  | Header | Waarde |
  |---|---|
  | `Strict-Transport-Security` | Nu `max-age=300` (alleen via https); live `max-age=31536000`. Geen `includeSubDomains` zolang niet zeker is dat elk subdomein https heeft, geen `preload` (moeilijk terug te draaien). |
  | `Content-Security-Policy` | Nu als Report-Only: `default-src 'self'; script-src 'self'; style-src 'self'; font-src 'self'; img-src 'self' data:; connect-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self' https://www.mollie.com`. Bij het echt aanzetten komt `frame-ancestors 'self'` erbij (wordt genegeerd in Report-Only). Geen Mollie in `connect-src` of `frame-src`: de browser praat nooit rechtstreeks met Mollie en de checkout is een gewone redirect. `form-action` geldt ook voor de redirect na het formulier. |
  | `Referrer-Policy` | `strict-origin-when-cross-origin` (de `?ref=` van de bedankpagina lekt niet) |
  | `Permissions-Policy` | `camera=(), microphone=(), geolocation=(), payment=()` |
  | `X-Frame-Options` | `SAMEORIGIN` (`DENY` breekt de paginavoorbeelden in het Panel) |
  | `Cross-Origin-Opener-Policy` | `same-origin` |
  | `X-Content-Type-Options` | `nosniff` |

  Er staan geen inline scripts of stijlen in de templates. De gloed van de lichtsporen wordt gezet met `element.style.filter` (mag onder `style-src 'self'`), niet met een `style`-attribuut. Tekst uit Panel-blokken kan wel stijlen bevatten.
- [ ] **[test]** CSP-console op de testserver: alle testpagina's, foutpagina's, een volledige testbetaling en het Panel doorlopen, en nakijken naar welke host Mollie precies doorstuurt (`form-action`). Pas als de console schoon is, de echte `Content-Security-Policy` aanzetten.
- [ ] **[test]** securityheaders.com en Mozilla Observatory: doel A of hoger. SSL Labs: doel A (geldig certificaat, TLS 1.2+).

### Toegang (OWASP A01)

- [ ] **[test]** Niet ingelogd: `POST /api/tess/donations/1/hide` en de Panel-dialogen geven 401/403.
- [x] **[test]** `donaties/lijst` stuurt alleen wat publiek mag: naam en bedrag als de donateur dat toestaat, een zichtbaar bericht en de datum (nagelezen in de code).
- [ ] **[test]** Niet bereikbaar: `site/storage/donations.sqlite` (en `-wal`, `-shm`), `site/accounts`, `site/sessions`, `site/cache`, `site/config/config.*.php`, `content/*.txt`, `.git/`. Overweeg de database buiten de webmap (optie `tess.donations.database`).
- [x] **[test]** Bedankpagina: een onbekende of geraden `?ref` (20 willekeurige hex-tekens) toont geen gegevens van iemand anders.

### Invoer en XSS (OWASP A05)

- [ ] **[test]** Nagelezen: invoer van donateurs wordt ge-escaped op de muur, in de popup, op de bedankpagina en in de mails; databasevragen gebruiken parameters; namen zonder regeleindes (geen header-injectie in mails). Nog nakijken: de Panel-tabel in `index.js` (geen `v-html` gevonden) en de JSON van `donaties/lijst`.
- [ ] **[test]** Proberen met `<script>`, `"><img onerror>` en lange teksten in naam, bericht en contactformulier. Lokaal getest met `<b>` en aanhalingstekens op het donatieformulier: ge-escaped.

### Formulieren en betalingen

- [x] **[test]** **B-14: Webhook zonder redirect.** Mollie volgt een 301/302 niet correct (de POST valt weg), dus de webhook-URL moet exact het definitieve domein zijn. Op de testserver komt de webhook van Mollie aan. Live opnieuw nakijken (www of niet).
- [ ] **[test]** **B-15: Webhook-verwerking** (gebouwd in TASKS.md, Fase 4). Mollie ondertekent gewone webhooks niet, dus de webhook leest alleen het `id` en haalt de betaling altijd opnieuw op. Een verzonnen `tr_…` gaf eerst 500 (Mollie probeert dan tot 26 uur opnieuw); nu 200, lokaal getest. Nog testen op de testserver:
  - een echt `id` van een andere betaling: niets verandert;
  - dezelfde webhook twee keer, en tegelijk de bedankpagina herladen: één statuswijziging en één mail;
  - in het foutlog staan alleen het betalings-ID en de technische fout, geen naam, e-mail of bericht.
- [ ] **[test]** Rate limit per IP en per formulier (contact 5 per uur, doneren 10 per uur, elk een eigen teller; unittests in `tests/FormProtectionTest.php`). Kirby gebruikt alleen `REMOTE_ADDR` en vertrouwt `X-Forwarded-For` bewust niet, anders kan een bot zijn IP vervalsen. Op de testserver staat nginx vóór Apache: nakijken met een tijdelijk testbestand of `REMOTE_ADDR` het echte IP is. Zo niet, dan regelt de host het (bv. `mod_remoteip`), niet onze code.
- [x] **[test]** Origin-controle: een formulier vanaf een ander domein wordt geweigerd (contact → 403, doneren → terug met een foutmelding). Een CSRF-token is hier niet nodig: de formulieren hebben geen sessie of rechten, en een token zou een sessiecookie voor elke bezoeker vereisen. Het Panel heeft Kirby's eigen CSRF-bescherming.
- [ ] **[test]** Cookies: Kirby's sessiecookie (alleen in het Panel) heeft `HttpOnly`, `SameSite=Lax` en `Secure` via https. Nakijken na het inloggen op de testserver.
- [x] **[test]** Mollie- en Brevo-sleutels alleen in hostconfigbestanden, niet in Git; de Git-geschiedenis is nagekeken. `test_` op de testserver, `live_` alleen live.
- [x] **[inhoud]** E-maildomein: SPF/DKIM via Brevo en DMARC (`p=quarantine`) voor `samenvoortess.be`.

### Pakketten (OWASP A03)

- [x] **[test]** `composer audit` en `npm audit`: geen bekende lekken. Kirby 5.6.1 is de nieuwste 5.x. Mollie-SDK 4.0 bestaat, maar is een grote update en geen beveiligingsfix: aparte taak.
- [ ] **[test]** Op de server `composer install --no-dev`; `node_modules` niet deployen.
- [x] **[code]** Dependabot (`.github/dependabot.yml`, wekelijks, Composer en npm). Opvolging na de oplevering: iemand van de vzw, zie IMPLEMENTATIE.md, Onderhoud.
- [x] **[test]** Geen externe scripts of lettertypes: de browser maakt alleen verbinding met het eigen domein.

### Fouten, logboek en back-ups (OWASP A09, A10, A08)

- [ ] **[test]** Met debug uit: een fout geeft een nette foutpagina, zonder paden of stacktrace.
- [ ] **[test]** Logboeken bevatten geen e-mailadressen of berichten van donateurs, alleen technische fouten.
- [ ] **[test]** Back-up op de server van `content/`, `site/accounts` en `site/storage` (de donateursgegevens). Afspreken wie dit doet en hoe vaak.
- [ ] **[test]** Herstel testen: één keer een back-up terugzetten in een testkopie en nakijken dat de site, de Panel-accounts en de donaties er weer zijn.
- [ ] **[test]** Monitoring live: een gratis uptimecontrole (bv. UptimeRobot) op de homepage, met een melding naar het team.

### Privacy (raakt alle drie)

- [x] **[test]** Gewone bezoekers krijgen geen cookies (alleen in het Panel), dus geen cookiebanner nodig. Lokaal getest op home, doneren en updates.
- [ ] **[inhoud]** Privacytekst: zie TASKS.md, Fase 12.
- [ ] **[inhoud]** Verwerkersovereenkomsten: Brevo en de host (Combell) verwerken persoonsgegevens voor de vzw. Hun overeenkomst zit meestal in de algemene voorwaarden; nakijken en bewaren. Mollie is voor de betaling zelf grotendeels zelf verantwoordelijk; ook nakijken.
- [ ] **[inhoud]** Bewaartermijn van de donateursgegevens vastleggen (bv. zolang de boekhouding ze nodig heeft) en in de privacytekst zetten.

### Extra

- [x] **[code]** `/.well-known/security.txt` met het contactadres uit Instellingen en een vervaldatum.

**Klaar als:** debug uit staat, geen enkel intern bestand bereikbaar is, securityheaders.com A geeft, `composer audit` schoon is en het Panel alleen met 2FA bereikbaar is.

## Volgorde

1. ~~**Vóór de eerste deploy (lokaal):** Deel 0, B-1 tot B-4 en de headers met de CSP als Report-Only.~~ Klaar.
2. ~~**Tegelijk:** testserver opzetten (TASKS.md, Fase 0).~~ Klaar.
3. ~~**Lokaal:** contrast, lichtsporen, formulierfouten, taal, canonical, hreflang, sitemap, Open Graph, favicon, foto's, toegankelijkheidsverklaring.~~ Klaar.
4. **Op de testserver (nu):** Panel-login met 2FA, B-15, headers en CSP-console, proxy-IP, alle [test]-punten en de schermlezerronde. Daarna de CSP echt aanzetten.
5. **Voor de livegang:** back-up en hersteltest, privacytekst, testupdates weg, Search Console.

## Deel 4: Afronding

- [ ] Korte samenvatting onderaan dit bestand: scores (Lighthouse, axe, securityheaders, SSL Labs), wat aangepast is en wat nog openstaat.
- [x] IMPLEMENTATIE.md bijgewerkt: headers, hostconfig (url, salt, cookie key), deploylijst, 2FA, toegankelijkheidsverklaring.

## Open vragen voor de opdrachtgever

- **Deelafbeelding** (1200 × 630) en een definitief **favicon** (nu een tijdelijk hartje): wie levert ze aan?
- **Back-ups:** doet Combell dit, of moeten we het zelf regelen?

## Tweede controle

Een tweede controle (Perplexity, 23-10-2026) is hierboven verwerkt. Niet of anders overgenomen:

| Opmerking | Waarom niet of anders |
|---|---|
| Mollie-webhook valideren en idempotent maken is "de belangrijkste ontbrekende controle" | Was al gebouwd (TASKS.md, Fase 4): de betaling wordt altijd bij Mollie opgehaald en de verwerking is idempotent. Een handtekening controleren kan niet, want Mollie ondertekent gewone webhooks niet. Het is testpunt B-15 geworden. |
| Een ongeldig webhook-ID "netjes afwijzen" | Mollie raadt 200 aan voor onbekende ID's, zodat een aanvaller niets leert. Bij het testen bleek een verzonnen ID 500 te geven; opgelost (B-15). |
| CSRF-token voor contact- en donatieformulier | Niet nodig zonder ingelogde sessie, en het zou een cookie voor elke bezoeker vereisen. Origin-controle, honeypot en rate limit blijven. |
| Rate limiting "per IP én per formulier" | Is al zo: elk formulier heeft een eigen teller per IP. |
| De forwarded-IP-header laten vertrouwen door Kirby | Zou het IP vervalsbaar maken. Als de host een proxy heeft, lost de server dat op (`mod_remoteip`). |
| `aria-modal` op de popups | Niet nodig bij een native `<dialog>` met `showModal()`. |
| Focusrand 3:1 tegenover de niet-gefocuste staat | Is 2.4.13 (AAA). Voor AA geldt 3:1 tegenover de kleuren ernaast (1.4.11). |
| Search Console: "mobiele bruikbaarheid" nakijken | Dat rapport bestaat sinds december 2023 niet meer. |
| Onvertaalde pagina's: `noindex` plus canonical | Eigen fout gecorrigeerd: de twee spreken elkaar tegen. Alleen de canonical blijft. |

## Bronnen

- W3C, What's new in WCAG 2.2: https://www.w3.org/WAI/standards-guidelines/wcag/new-in-22/
- EAA in België: https://www.craftzing.com/our-insights/articles/european-accessibility-act-in-belgium-compliance-complaints-penalties en https://accesstive.com/compliance-hub/belgium-eaa (de bronnen zijn het niet eens over micro-ondernemingen; nakijken bij FOD Economie als het juridisch belangrijk wordt)
- OWASP Top 10:2025: https://owasp.org/Top10/2025/
- OWASP Secure Headers Project: https://owasp.org/www-project-secure-headers/
- Kirby, Secure your project: https://getkirby.com/docs/guide/security
- Google, gelokaliseerde versies (hreflang): https://developers.google.com/search/docs/specialty/international/localized-versions
- Mollie, webhooks: https://docs.mollie.com/reference/webhooks
