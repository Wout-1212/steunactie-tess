# Controle toegankelijkheid, SEO en beveiliging: taken

Een volledige controle van de site voordat ze op de testserver komt, en later live gaat. Eerst keuren we deze lijst goed; pas daarna voeren we de wijzigingen door, onderdeel per onderdeel. Elk onderdeel heeft een "Klaar als".

**Stand van zaken (23-10-2026):** stap 1 en 3 van de [Volgorde](#volgorde) zijn gebouwd: alles wat vóór de eerste deploy klaar moet zijn en alle [code]-punten die lokaal kunnen. Wat lokaal te testen viel, is getest (axe, contrast, audits, formulieren, webhook, cookies). Wat overblijft, zijn tests op de testserver, [inhoud]-punten en de vragen onderaan. Opmerkingen van een tweede controle (Perplexity) verwerkt, zie [Tweede controle](#tweede-controle). Lijst opgesteld na het nalezen van de code en onderzoek naar de geldende normen. Contrast gemeten in de browser op de lokale site. Beslist: Nederlands is de hoofdtaal, Frans en Engels volgen als de definitieve tekst er is; de kleuren blijven, contrast lossen we op met grootte, gewicht, een witte achtergrond of de gewone donkere tekstkleur (tabel goedgekeurd); lichtsporen oplossing A; `noindex` alleen op de testserver.

Per punt staat wat het werk is:

- **[code]**: passen we aan in de code (na goedkeuring).
- **[test]**: nakijken, lokaal of op de testserver, met een tool of met de hand. Een probleem wordt dan een [code]-punt.
- **[inhoud]**: moet de opdrachtgever aanleveren of in het Panel invullen.

Punten met **Gevonden:** zijn al opgemerkt bij het nalezen van de code.

## Normen

| Onderdeel | Norm | Waarom |
|---|---|---|
| Toegankelijkheid | WCAG 2.2 niveau AA, als eigen kwaliteitsnorm | De Europese toegankelijkheidswet (EAA, van kracht sinds 28 juni 2025) verwijst via EN 301 549 naar WCAG 2.1 AA. Of de vzw met een donatieformulier eronder valt, hangt af van hoe de dienst juridisch gekwalificeerd wordt: laten bevestigen door de vzw of FOD Economie. Hoe dan ook houden we WCAG 2.2 AA aan; die dekt 2.1 volledig. |
| SEO | Google Search Central-richtlijnen | Zoekmachines, en ook hoe de site eruitziet als ze gedeeld wordt via WhatsApp, Facebook en Instagram. Voor een steunactie is delen even belangrijk als gevonden worden. |
| Beveiliging | OWASP Top 10:2025 en de Kirby-beveiligingsgids | De site verwerkt donateursgegevens en betalingen. |

## Gemaakte keuzes

| Onderwerp | Voorstel |
|---|---|
| Tools | Lighthouse en axe DevTools (automatisch), W3C-validator, NVDA met Firefox en VoiceOver op iPhone (met de hand), securityheaders.com, Mozilla Observatory en SSL Labs (beveiliging), PageSpeed Insights (snelheid). Gratis en zonder account. |
| Pagina's die we testen | Home, Haar verhaal, Behandelingsplan, FAQ, Updates, één update, Disclaimer, Doneren, Bedankt (betaald, in behandeling, mislukt), 404. Toegankelijkheid in alle drie de talen; de rest in het Nederlands. |
| Testserver | Volledig `noindex`, zodat Google de testserver nooit opneemt. Geen wachtwoord op de hele site, want Mollie moet de webhook kunnen bereiken. |
| Talen | Nederlands is de hoofdtaal; Frans en Engels worden vertaald zodra de definitieve tekst er is. Tot dan biedt de site een taal per pagina alleen aan als die pagina vertaald is (zie T-1 en S-4). Zo hoeft er bij het vertalen niets meer aan de code te gebeuren. |

## Gevonden bij het nalezen van de code

Kort overzicht van de belangrijkste punten; ze staan hieronder ook in de lijst.

1. `'debug' => true` in `site/config/config.php`: bezoekers zien foutdetails en servergegevens.
2. Op `/vendor`, `TASKS.md`, `IMPLEMENTATIE.md`, `composer.json/lock` en `package.json` kan iedereen surfen. `.htaccess` blokkeert alleen `content/`, `site/`, `kirby/` en bestanden die met een punt beginnen.
3. Geen beveiligingsheaders behalve `X-Content-Type-Options` (geen HSTS, CSP, Referrer-Policy, framebeveiliging).
4. Geen `url`-optie: Kirby leidt de domeinnaam af van wat de browser meestuurt. Die domeinnaam komt terecht in de Mollie-redirect, de webhook-URL en de mails.
5. Geen favicon, geen deelafbeelding of Open Graph-tags, geen canonical, geen hreflang in de `<head>`, geen `robots.txt` en geen sitemap.
6. Franse en Engelse pagina's tonen Nederlandse tekst met `lang="fr"`/`lang="en"`: schermlezers lezen dan Nederlands voor met een Franse of Engelse uitspraak.
7. Kleine roze tekst (#C43B75) haalt het minimale contrast niet op de roze bovenkant en de blauwe onderkant van de achtergrond (gemeten 3,72 tot 4,49, nodig is 4,5). Zie de lijst bij Toegankelijkheid.
8. Testupdates ("test", "test1", "test1-blocks") staan gepubliceerd in `content/updates`.
9. Foto's via `components/picture` worden op ware grootte geladen, zonder verkleining of srcset (hero, cover, sectiefoto's).

---

## Deel 0: Testserver afschermen

`noindex` is een instructie voor zoekmachines: "neem deze pagina niet op in de zoekresultaten". Het staat als `<meta name="robots">` in de pagina of als `X-Robots-Tag`-header. Het is geen beveiliging: wie het adres kent, kan de pagina nog openen. Doneren en Bedankt gebruiken het al. Op de live site mag het nooit voor de hele site staan, anders verdwijnt de site uit Google.

- [x] **[code]** Hele testserver `noindex`: header `X-Robots-Tag: noindex, nofollow` vanuit het hostconfigbestand van de testserver, zodat het nooit mee naar productie gaat. Gebouwd: `'tess.noindex' => true` in het hostconfigbestand (plugin `tess-seo`); lokaal getest op pagina's, routes, 404's en de webhook.
- [x] **[code]** Voor de testserver `robots.txt` met `Disallow: /`. Let op: als Google een pagina niet mag ophalen, ziet het de `noindex` ook niet. Daarom alleen op de testserver, als extra naast de header. Gebouwd: `robots.txt` volgt dezelfde optie.
- [ ] **[test]** Webhook bereikbaar zonder wachtwoord of redirect (zie B-14).

**Klaar als:** elke pagina van de testserver `X-Robots-Tag: noindex` meestuurt en de productieconfig dat niet doet.

## Deel 1: Toegankelijkheid (WCAG 2.2 AA)

### Automatisch

- [x] **[test]** axe DevTools en Lighthouse (Toegankelijkheid) op elke testpagina, in nl/fr/en, op desktop- en gsm-breedte. Doel: 0 fouten bij axe. **Lokaal gedaan:** axe 0 fouten op 12 pagina's, 1280 en 390px breed (Nederlands; Frans en Engels bestaan nog niet). Gevonden en opgelost: kopniveau op Updates, een fotolink zonder naam in een fotoblok, twee regio's met dezelfde naam op de homepage. Lighthouse volgt op de testserver.
- [ ] **[test]** W3C-validator (validator.w3.org/nu) op elke paginasoort: geen fouten die toegankelijkheid raken (dubbele id's, ongeldige nesting, ARIA op verkeerde elementen).

### Waarneembaar

- [x] **[code]** Contrast tekst (1.4.3, minimaal 4,5:1, of 3:1 vanaf 24px of 18,66px (14pt) vet). Gemeten met de huisstijlkleuren: **Gedaan:** nagemeten op 11 pagina's, op 1280 en 390px; alleen de foto-placeholders blijven over. Extra gevonden en opgelost: subkoppen (`h3`) in Panel-tekst 17,6px → 19,2px; links in Panel-tekst donker met roze onderlijn; inleiding van het donatieblok en de weegschaal 14px → 15px; datum op een updatepagina donker; "Lees meer"-links en terugknoppen zijn witte omlijnde knoppen.

  | Roze #C43B75 op | Contrast | Kleine tekst |
  |---|---|---|
  | wit #FFFFFF | 4,96 | ok |
  | geel #FFF6CE | 4,57 | net ok |
  | blauw #C8EEF3 | 4,01 | **te laag** |
  | roze #F5D7E6 | 3,72 | **te laag** |

  **Gevonden (gemeten in Chrome op de lokale site, 1280px en 390px breed, achtergrond inclusief verloop):**

  | Waar | Contrast |
  |---|---|
  | Header: het actieve menu-item (12px vet) en op de gsm de knop "Menu ☰" | 3,72 |
  | De naam Tess in Caveat (18-21px) in lopende tekst en koppen, overal op het verloop | 3,75-4,49 |
  | Roze tekstlinks op het verloop: "Lees haar verhaal", "Lees meer over haar →", "Lees meer over het behandelplan →", "Bekijk alle updates ↓", "← Terug naar het donatieblok" | 3,76-4,25 |
  | Labels op het verloop: "Ander bedrag (€)", en Naam, E-mailadres, Vraag/bericht in het contactformulier | 4,06-4,35 |
  | Gsm: titel "Donatievoortgang" en de mijlpaalbedragen | 4,36-4,40 |
  | Footer-koppen "Menu", "Steun", "Over de vzw" (15px) op de blauwe footer (homepage en disclaimer) | 4,01 |
  | Placeholders "Foto van Tess volgt" (verdwijnen als de foto's er zijn) | 4,19-4,34 |

  Wel in orde: grote koppen (vanaf 24px geldt 3:1), wit op de roze knoppen (4,96) en roze tekst op witte kaarten (4,96). Hover-kleuren zijn nog niet gemeten.

  Beslist: de huisstijlkleuren blijven ongewijzigd. Grote tekst (vanaf 24px, of 18,66px vet) heeft maar 3:1 nodig, en dat haalt het roze overal (minimaal 3,72). Per plaats:

  | Waar | Oplossing |
  |---|---|
  | Naam Tess in Caveat | `font-semibold` → `font-bold`: vanaf 18,66px telt de naam dan als grote tekst en slaagt hij. In tekst kleiner dan 15px (inleiding donatieblok, kleine notities) neemt de naam de tekstkleur over, zoals nu al in de footer. |
  | Footer-koppen | 15px → 19px vet. |
  | "Lees haar verhaal", "Bekijk alle updates ↓" (omlijnde knoppen) | Witte achtergrond in plaats van doorzichtig: roze op wit = 4,96. |
  | Contactformulier en donatieblok op de homepage | In een witte kaart, zoals het formulier op /doneren en de donatiemuur. De labels staan dan op wit. |
  | Tekstlinks "Lees meer over haar →", "Lees meer over het behandelplan →", "← Terug naar het donatieblok" | 19px vet, of dezelfde witte knopstijl als hierboven. |
  | Actief menu-item (12px), knop "Menu ☰" op de gsm | Kan niet groot genoeg. Donkere tekstkleur (#27222A, al de gewone tekstkleur) met een roze onderlijn voor het actieve item. |
  | Gsm: "Donatievoortgang" en mijlpaalbedragen | Passen niet groter in vier kolommen. Donkere tekstkleur, of de rij op een witte strook. |
  | Placeholders "Foto van Tess volgt" | Niets doen: ze verdwijnen zodra de foto's er zijn. |
- [x] **[code]** Contrast van niet-tekst (1.4.11, 3:1): randen van invoervelden (`border-blush` #F5D7E6 op wit is nauwelijks zichtbaar), randen van checkboxen, de voortgangsbalk (wit op lichte achtergrond), de focusrand. **Gedaan:** invoervelden hebben een roze rand op 80% (3,60:1 op wit). De focusrand (inktkleur) haalt 3,14:1 op de roze knoppen en meer elders.
- [x] **[test]** Contrast van hover- en focusstaten: tekst bij hover haalt 4,5:1 (bv. roze bij hover in het menu en in de footer); de focusrand haalt 3:1 tegenover de kleuren ernaast (1.4.11). De strengere eis "3:1 tegenover de niet-gefocuste staat" is 2.4.13, niveau AAA: meenemen waar het makkelijk kan, geen harde eis. **Gevonden:** knoppen met `hover:opacity-85` zakken bij hover naar ongeveer 3,95:1; roze hoverkleur in het menu (3,72:1) en in de footer op de homepage (4,01:1). **Beslist:** bewust aanvaard, de hoverstaten blijven zoals ze zijn (de kleur is maar even zichtbaar en de tekst blijft leesbaar). Links in Panel-tekst blijven al donker bij hover.
- [ ] **[test]** Zeer kleine tekst (9-11px bij mijlpalen, hulpteksten, "Meest gekozen"): geen harde WCAG-fout, maar nakijken of het leesbaar blijft. Voorstel minimaal 12px.
- [ ] **[test]** Zoomen tot 200% (1.4.4) en een scherm van 320px breed (1.4.10): niets valt weg, geen horizontaal scrollen (behalve de stappenbalk, die bewust zijwaarts scrolt).
- [ ] **[test]** Tekstafstand (1.4.12) met de bookmarklet "Text spacing": geen tekst die afgesneden wordt of overlapt.
- [ ] **[test]** [inhoud] Alt-teksten (1.1.1): elke foto in het Panel heeft een beschrijvende alt-tekst. De terugvaloptie (paginatitel of kop) zegt vaak niets over de foto. Logo: alt "Samen voor Tess" (ok). Decoratieve iconen staan op `aria-hidden` (ok).
- [x] **[test]** Kopstructuur (1.3.1): één `h1` per pagina, geen niveaus overgeslagen. Nakijken: `h3` van de donatiemuur, koppen in de updatekaarten en in Panel-blokken (redacteurs kunnen zelf h2-h6 kiezen). **Gedaan:** axe vindt geen fouten meer; updatekaarten zijn `h2` op de pagina Updates en `h3` op de homepage.
- [x] **[test]** Landmarks (1.3.1): `header`, twee benoemde `nav`, `main`, `aside`, `footer`. Geen inhoud buiten een landmark (de testmodus-strook staat nu buiten `header`). **Gedaan:** de testmodus-strook staat in de header.
- [x] **[code]** Invoerdoel (1.3.5): `autocomplete` staat er voor naam en e-mail (ok). Nakijken bij bedrag: `autocomplete="off"` voorkomt foute suggesties. **Gedaan.**

### Bedienbaar

- [ ] **[test]** Alles met alleen het toetsenbord (2.1.1, 2.1.2, 2.4.3): menu op gsm, taalkeuze, uitklapvragen, bedragknoppen, donatieformulier, contactformulier, IBAN kopiëren, stappenbalk met pijlen en pijltjestoetsen.
- [ ] **[test]** Popups apart testen (delen, fotolightbox, alle donaties, mobiel menu), met toetsenbord en schermlezer: focus gaat erin, blijft erin, Escape sluit, sluiten via een klik op de achtergrond, focus keert terug naar de knop die de popup opende, de pagina erachter scrolt niet mee. De drie popups zijn een native `<dialog>` met `showModal()`: die is vanzelf modaal (de rest van de pagina is `inert`), dus `aria-modal` is daar niet nodig. Het mobiele menu is geen `<dialog>` en regelt dit zelf met `inert` en een focusval.
- [ ] **[test]** Focus niet verborgen (2.4.11, nieuw in 2.2): de sticky header mag een veld of link met focus niet volledig bedekken, vooral bij terug tabben (Shift+Tab). Waarschijnlijk oplossing: `scroll-padding-top` op `html` gelijk aan de hoogte van de header. **Gebouwd:** `scroll-padding-top` volgt nu de echte hoogte van de header. Nog testen met het toetsenbord.
- [ ] **[test]** Focus zichtbaar (2.4.7): de globale focusrand (`outline-3 outline-ink`) verschijnt overal, ook op de roze knoppen en in de popups.
- [ ] **[test]** Skiplink (2.4.1): wordt zichtbaar bij Tab en springt naar `main`.
- [ ] **[test]** Klikdoelen minstens 24×24px (2.5.8, nieuw in 2.2): footerlinks, links naar Instagram, checkboxen (16px, het label telt mee), knoppen van de stappenbalk, sluitknoppen. Een kleiner doel slaagt ook als er genoeg ruimte rond zit (een cirkel van 24px rond elk doel raakt geen ander doel), als er dichtbij een gelijkwaardig groter doel staat, of als het een link in lopende tekst is.
- [x] **[code]** Bewegende inhoud (2.2.2): beweging die vanzelf start, langer dan 5 seconden duurt en naast andere inhoud staat, moet te pauzeren zijn. De lichtsporen starten vanzelf, een ronde duurt 6,5-8,5 s en ze komen elke 9-16 s terug, zolang de pagina openstaat. "Verminder beweging" (`prefers-reduced-motion`) zet ze al uit, maar telt niet als pauzeknop: weinig mensen kennen die instelling. Oplossingen die wel voldoen:
  - **A (gekozen, gebouwd):** elke knop maakt vanzelf één korte ronde van hoogstens 5 s als hij in beeld komt, daarna alleen nog bij hover of focus. Het effect blijft zichtbaar en valt binnen de 5 seconden. Een ronde duurt nu 4-4,8 s.
  - **B:** alleen bij hover of focus. Dan start niets vanzelf en geldt de regel niet; op de gsm (geen hover) zie je het effect dan bijna nooit.
  - **C:** zoals nu, plus een knop "Animaties uit" bovenaan de pagina, die onthouden wordt. Voldoet, maar voegt een knop toe die weinig mensen zoeken.
- [ ] **[test]** Animaties (2.3.3): opbouw bij scrollen, optellende cijfers, confetti op de bedankpagina en lichtsporen volgen allemaal `prefers-reduced-motion`. Inhoud is ook zichtbaar zonder JavaScript en bij afdrukken.
- [ ] **[test]** Zonder of met falend JavaScript: de formulieren werken (gewone POST met redirect) en geven nooit een succesmelding als er niets verstuurd is. Testen met JavaScript uit, en met een netwerkfout tijdens het verzenden van het contactformulier. (Bij een bot geeft het contactformulier bewust "gelukt"; dat is de bedoeling.)
- [x] **[test]** Paginatitels (2.4.2) en linkteksten (2.4.4): unieke titel per pagina; geen losse links als "lees meer" zonder context (updatekaarten). **Gedaan:** de homepage heet "Samen voor Tess", andere pagina's "Titel | Samen voor Tess". Een updatekaart is één link met de titel erin.

### Begrijpelijk

- [x] **[code]** **T-1: Taal van de pagina (3.1.1).** **Gevonden:** `/fr` en `/en` tonen de Nederlandse tekst uit `*.nl.txt` met `lang="fr"` of `lang="en"`. Aanpak (gebouwd, plugin `tess-seo`): de taalkeuze toont een taal alleen voor pagina's die in die taal bestaan, en verdwijnt zolang er maar één is. Een onvertaalde `/fr/…`- of `/en/…`-pagina verwijst tijdelijk (302) door naar de Nederlandse pagina, met de query-string. Zo mengt geen enkele pagina twee talen. Na het vertalen in het Panel verschijnt de taal vanzelf. **Gedaan**, lokaal getest met een tijdelijke Franse FAQ.
- [x] **[code]** Taal van onderdelen (3.1.2): vaste Nederlandse woorden in de templates, zoals "Ondernemingsnummer:" in de footer, naar `site/languages` verplaatsen. **Gedaan:** "Ondernemingsnummer" staat nu in de vertalingen. Geen andere vaste teksten gevonden.
- [x] **[code]** Foutmeldingen in formulieren (3.3.1, 3.3.3): nu één algemene melding ("Controleer bedrag, naam en e-mailadres") na het versturen. Voorstel: bovenaan een foutenoverzicht met per fout een link naar het veld, de focus naar dat overzicht, en bij elk foutief veld de melding gekoppeld via `aria-describedby` en `aria-invalid`. **Gedaan voor het donatieformulier:** bij een fout komt de pagina meteen terug (status 422) met het foutenoverzicht (krijgt de focus), een melding onder elk fout veld en alles wat ingevuld was. Het contactformulier controleert alles al in de browser (verplicht, e-mailadres, maximale lengte); een fout van de server toont daar één melding.
- [x] **[test]** Labels en instructies (3.3.2): verplichte velden zichtbaar gemarkeerd, of de optionele (bericht) als enige gemarkeerd. Min/max bedrag vermeld bij het veld. **Gedaan:** hulptekst met minimum en maximum onder het bedrag; het contactformulier vermeldt dat alle velden verplicht zijn.
- [x] **[code]** `autofocus` op de donatiepagina: schermlezers slaan dan de titel en uitleg over. Voorstel: weghalen. **Gedaan:** weg; alleen het foutenoverzicht krijgt de focus.
- [ ] **[test]** Statusmeldingen (4.1.3): "Bezig met versturen", succes/fout van het contactformulier, "IBAN gekopieerd" en "Meer laden" in de popup worden voorgelezen, één keer (niet dubbel), en zonder dat de focus verspringt.
- [x] **[test]** Consistente hulp (3.2.6, nieuw in 2.2): contact staat op elke pagina op dezelfde plaats (menu en footer: ok). **In orde.**
- [ ] **[test]** Toegankelijke aanmelding (3.3.8, nieuw in 2.2): in het Panel werken plakken en wachtwoordbeheerders, en 2FA met een code-app is toegestaan (zie B-5).

### Robuust en schermlezer

- [ ] **[test]** Volledige ronde met NVDA + Firefox (Windows) en VoiceOver (iPhone): homepage van boven tot onder, een donatie van begin tot eind (tot de Mollie-checkout), contactformulier, een update met foto's.
- [x] **[code]** Ongeldige ARIA: **Gevonden:** `aria-label` op een gewone `div` in de taalkeuze (wordt genegeerd). Ook de groep met bedragknoppen en de `progress` nakijken. **Gedaan:** `aria-label` weg; axe vindt geen ARIA-fouten.
- [ ] **[test]** De e-mails (contact, bedankmail): `lang` per taal, leesbaar contrast, platte-tekstversie (die bestaat al).
- [ ] **[code] [inhoud]** Toegankelijkheidsverklaring, een vast onderdeel van de oplevering: welke norm we nastreven (WCAG 2.2 AA), welke onderdelen nog beperkt toegankelijk zijn, hoe je een probleem meldt en binnen welke termijn er een antwoord komt. Ook bruikbaar als bewijs voor de opdrachtgever. Wij maken de pagina of het stuk op de disclaimerpagina; de opdrachtgever bevestigt contactadres en termijn. **Gebouwd:** onderdeel op `/disclaimer#toegankelijkheid` met een link in de footer, bewerkbaar in het Panel (Disclaimer). Antwoordtermijn: 5 werkdagen. Open: de datum na de controle op de testserver.

**Klaar als:** axe geen fouten geeft op alle testpagina's in de drie talen, het contrast overal klopt en een volledige donatie met alleen het toetsenbord en met NVDA lukt.

## Deel 2: SEO en delen

### Technisch

- [x] **[code]** `robots.txt` voor productie met een verwijzing naar de sitemap. Pagina's met `noindex` (doneren, bedankt) niet blokkeren, anders ziet Google de `noindex` niet. **Gebouwd** (plugin `tess-seo`).
- [x] **[code]** XML-sitemap (route `sitemap.xml`): alle gepubliceerde pagina's en updates, per taal met hreflang. Zonder `doneren`, `bedankt`, `error` en de oude routes `privacy` en `algemene-voorwaarde`. `lastmod` alleen als de datum een echte inhoudelijke wijziging weergeeft (de wijzigingsdatum van het contentbestand), nooit de datum van een deploy. **Gebouwd:** `/sitemap.xml`; `lastmod` alleen bij updates, uit hun datum (bestandsdatums veranderen bij elke upload).
- [x] **[code]** Canonical per pagina (`<link rel="canonical">`), zonder query-string. Daarmee tellen `?contact=…`, `?donation=…` en `?amount=…` niet als aparte pagina's. **Gebouwd** (`site/snippets/layout/meta.php`); niet op pagina's met `noindex`.
- [ ] **[test]** Eén URL overal: menu, knoppen, updatekaarten, sitemap, canonical en redirects wijzen naar dezelfde URL (https, het definitieve domein, zonder query-parameters). Ankers zoals `#help-tess` zijn geen aparte URL en mogen. **Lokaal:** alle links gebruiken `$page->url()`. Nog nakijken op de testserver met https.
- [x] **[code]** **S-4: hreflang** in de `<head>` (`<link rel="alternate" hreflang="nl|fr|en|x-default">`), met verwijzing naar zichzelf en wederzijds. Alleen voor talen waarin de pagina vertaald is; `x-default` is de Nederlandse versie. Onvertaalde `/fr/…`- en `/en/…`-pagina's verwijzen door naar de Nederlandse pagina (zie T-1), staan niet in de sitemap en geen enkele hreflang wijst ernaar. **Gebouwd**; verschijnt zodra een pagina in twee talen bestaat.
- [ ] **[test]** Eén adres per pagina: `http` → `https` en `www` → zonder www (of omgekeerd) met een 301 naar één vast domein.
- [x] **[test]** De 404-pagina geeft echt statuscode 404 en staat op `noindex`. **Lokaal getest.**
- [x] **[test]** Niet-gelinkte pagina's: elke pagina in `content/` zonder volgnummer is via haar URL bereikbaar. Nakijken of er geen lege conceptpagina's opduiken. **Nagekeken:** alle mappen in `content/` zijn bekend; privacy en algemene-voorwaarde verwijzen door, doneren en bedankt staan op `noindex`.
- [ ] **[inhoud]** **Gevonden:** de testupdates `20261008_test1`, `20261009_test` en `20261009_test1-blocks` weghalen (of op concept zetten) voor de livegang. Ze staan nu gepubliceerd op `/updates`.

### Snelheid (Core Web Vitals)

- [ ] **[test]** PageSpeed Insights / Lighthouse op gsm voor home, een update en Haar verhaal. Doel: LCP < 2,5 s, CLS < 0,1, INP < 200 ms; Lighthouse Prestaties ≥ 90.
- [x] **[code]** **Gevonden:** `components/picture` en de sectiefoto's in `default.php` laden het originele bestand. Voorstel: Kirby-thumbs met `srcset`/`sizes` en WebP (de update- en blokfoto's hebben al een srcset). **Gebouwd:** `srcset` van 480 tot 1600px met `sizes`, in het originele formaat. Geen WebP, zodat het niet afhangt van wat de server kan; lokaal getest met een tijdelijke foto.
- [x] **[code]** Herofoto: `fetchpriority="high"` (is de LCP-afbeelding op de homepage). **Gebouwd.**
- [x] **[code]** Lettertypes: de woff2-bestanden voorladen (`<link rel="preload">`) en `font-display: swap` nakijken in `fonts.css`. **Gebouwd;** `font-display: swap` stond er al.
- [x] **[code]** Browsercache in `.htaccess`: lange `Cache-Control` voor `assets/` en `media/` (de bestanden hebben al een versie in de URL). **Gebouwd:** een jaar voor `media/` en de CSS en JS, een week voor lettertypes en beelden in `assets/` (die houden hun naam). Testen op de testserver.

### Op de pagina

- [x] **[code]** Paginatitel: nu "Pagina | Samen voor Tess", dus de homepage heet "Home | Samen voor Tess". Voorstel: homepage alleen de sitenaam of een eigen SEO-titel. Maximaal ongeveer 60 tekens. **Gedaan:** de homepage heet "Samen voor Tess".
- [ ] **[inhoud]** Beschrijving voor zoekmachines invullen per pagina (tabblad Menu & zoekmachines). Nu valt elke pagina terug op dezelfde sitebeschrijving.
- [x] **[code]** Open Graph en Twitter Card: `og:title`, `og:description`, `og:image` (1200×630) met `og:image:width`, `og:image:height` en `og:image:alt`, `og:url`, `og:type`, `og:locale` (+ alternatieven alleen voor vertaalde pagina's), `twitter:card` = `summary_large_image`. Belangrijk voor delen via WhatsApp en sociale media. Per pagina een deelafbeelding in het Panel, met een standaardafbeelding in Instellingen. **Gebouwd.** Volgorde van de deelafbeelding: die van de pagina, dan een foto van de pagina (updatefoto, cover, herofoto), dan de standaard uit Instellingen. Lokaal getest.
- [ ] **[inhoud]** Standaard-deelafbeelding 1200×630 aanleveren (logo + foto).
- [x] **[code]** Favicon: `favicon.svg` + `favicon.ico` (of png), `apple-touch-icon` (180×180) en `theme-color`. Nu toont de browser een standaardicoon. **Gebouwd** met een tijdelijk wit hartje op roze (het logo is te breed voor een icoontje). Vervangen zodra er een definitief icoon is.
- [x] **[code]** Gestructureerde gegevens (JSON-LD): `Organization`/`NGO` met naam, logo, URL, Instagram (`sameAs`) en ondernemingsnummer, en `WebSite`. Updates als `Article` met datum. Nice to have; helpt Google de organisatie te herkennen. **Gebouwd:** `NGO` en `WebSite` op de homepage, `BlogPosting` per update.
- [ ] **[test]** URL's per taal: nu `/fr/haar-verhaal`. Als we vertalen, de slugs ook vertalen.

### Bij de livegang (niet op de testserver)

- [ ] **[test]** Google Search Console en Bing Webmaster Tools: domein verifiëren, sitemap indienen. Na een week het rapport "Pagina-indexering" nakijken: welke pagina's niet geïndexeerd zijn en waarom, en of Google dezelfde canonical kiest als wij. (Het rapport "Mobiele bruikbaarheid" bestaat sinds december 2023 niet meer; mobiel testen we met Lighthouse.)
- [ ] **[test]** Controleren dat de `noindex` van de testserver niet meegekomen is.

**Klaar als:** elke pagina een eigen titel, beschrijving, canonical en deelafbeelding heeft, de sitemap valideert, gedeelde links in WhatsApp een voorbeeld met foto tonen en Lighthouse SEO 100 geeft.

## Deel 3: Beveiliging (OWASP Top 10:2025 en Kirby)

### Configuratie (OWASP A02)

- [x] **[code]** **B-1:** **Gevonden:** `'debug' => true` in `site/config/config.php`. Naar `false`; `true` alleen in het lokale `config.theagency2627-steunactie-tess.test.php`.
- [ ] **[code]** **B-2:** `'url' => 'https://…'` in elk hostconfigbestand, plus `UseCanonicalName On` als de host dat toelaat. Zo kan een vervalste Host-header de Mollie-URL's, mails en Panel-links niet beïnvloeden. Gebouwd: `url` in `config.host.example.php`. `UseCanonicalName` nakijken op de server.
- [x] **[code]** **B-3:** `content.salt` en `cookie.key` als lange willekeurige waarden in het hostconfigbestand (kan vanaf Kirby 5.4.5; wij gebruiken 5.6.1). Toegevoegd aan `config.host.example.php`.
- [x] **[code]** **B-4:** **Gevonden:** `/vendor`, `node_modules`, `state/`, `*.md`, `composer.json/lock` en `package*.json` zijn bereikbaar. Hoofdmaatregel: alleen de nodige bestanden en mappen deployen (deploylijst in IMPLEMENTATIE.md: `index.php`, `.htaccess`, `assets/`, `content/`, `kirby/`, `media/`, `site/`, `vendor/`). Als extra laag `.htaccess`-regels voor `vendor/` en de overige bestanden, plus `Options -Indexes`. `.claude/` en `.git/` zijn al geblokkeerd omdat ze met een punt beginnen. Gebouwd; de `.htaccess`-regels nog testen op de testserver (lokaal draait nginx, dat `.htaccess` negeert).
- [ ] **[code]** **B-5:** Panel: eerste admin lokaal aanmaken en `site/accounts` uploaden (Kirby staat installeren op een publieke server standaard niet toe). Sterke, unieke wachtwoorden; 2FA met een code-app voor elke gebruiker (`auth.methods` met `2fa`). Alleen wie het nodig heeft is admin. Gebouwd: 2FA staat aan in `config.php` (lokaal uit). Account aanmaken en uploaden gebeurt bij de testserver.
- [x] **[test]** `panel.vue.compiler` moet aan blijven: de Donaties-weergave (`tess-donations/index.js`) gebruikt een `template`-string. Even noteren in IMPLEMENTATIE.md. **Genoteerd** in IMPLEMENTATIE.md.
- [ ] **[test]** Server: PHP 8.3 of 8.4 (ondersteund), `display_errors` uit, `expose_php` uit, foutlog buiten de webmap, geen 777-rechten, SFTP in plaats van FTP. **Gevonden lokaal:** de header `X-Powered-By: PHP/8.4.27`; op de server `expose_php` uitzetten.

### Beveiligingsheaders

- [x] **[code]** In `.htaccess`, met `Header always set` zodat de headers ook op redirects, 404's en foutpagina's staan. Het Panel krijgt de CSP niet (het heeft inline stijlen nodig); de andere headers wel. Gebouwd, met de CSP als Report-Only en `X-Frame-Options: SAMEORIGIN` in plaats van `DENY` (het Panel toont voorbeelden van pagina's in een iframe van de site). Testen op de testserver.

  | Header | Voorstel |
  |---|---|
  | `Strict-Transport-Security` | eerst `max-age=300` op de testserver, live `max-age=31536000`. Geen `includeSubDomains` zolang niet zeker is dat elk subdomein van samenvoortess.be HTTPS heeft. Geen `preload`: dat is moeilijk terug te draaien. |
  | `Content-Security-Policy` | `default-src 'self'; script-src 'self'; style-src 'self'; font-src 'self'; img-src 'self' data:; connect-src 'self'; object-src 'none'; base-uri 'self'; frame-ancestors 'none'; form-action 'self' https://www.mollie.com`. De aparte `script-src`, `style-src`, `font-src` en `connect-src` volgen al uit `default-src`, maar maken de bedoeling duidelijk. Geen Mollie in `connect-src` of `frame-src`: de browser praat nooit rechtstreeks met Mollie en de checkout is een gewone redirect, geen iframe. `form-action` geldt ook voor de redirect na het formulier: bij een testbetaling nakijken naar welke host Mollie precies doorstuurt. |
  | `Referrer-Policy` | `strict-origin-when-cross-origin` (de `?ref=` van de bedankpagina lekt niet) |
  | `Permissions-Policy` | `camera=(), microphone=(), geolocation=(), payment=()` |
  | `X-Frame-Options` | `SAMEORIGIN` (voor oudere browsers; `DENY` breekt de paginavoorbeelden in het Panel) |
  | `Cross-Origin-Opener-Policy` | `same-origin` |
  | `X-Content-Type-Options` | `nosniff` (staat er al) |

  Er staan geen inline scripts of stijlen in de templates. **Gevonden:** `site.js` zet wel een `style`-attribuut op de ster van de lichtsporen (de gloed); `style-src 'self'` blokkeert dat. Aangepast naar `element.style.filter = …` (dat mag wel). Ook tekst uit Panel-blokken kan stijlen bevatten.
- [ ] **[test]** De CSP eerst als `Content-Security-Policy-Report-Only` op de testserver: de browserconsole toont dan wat geblokkeerd zou worden, zonder dat de site breekt. Alle testpagina's, foutpagina's, een volledige testbetaling en het Panel doorlopen. Pas als de console schoon is, de echte `Content-Security-Policy` aanzetten.
- [ ] **[test]** securityheaders.com en Mozilla Observatory: doel A of hoger. SSL Labs: doel A (geldig certificaat, TLS 1.2+).

### Toegang (OWASP A01)

- [ ] **[test]** Niet ingelogd: `POST /api/tess/donations/1/hide` en de Panel-dialogen geven 401/403.
- [x] **[test]** `donaties/lijst` stuurt alleen wat publiek mag: geen e-mail, geen verborgen namen of bedragen, geen tegengehouden berichten. **Nagelezen in de code:** alleen naam en bedrag als de donateur dat toestaat, een zichtbaar bericht en de datum.
- [ ] **[test]** Niet bereikbaar: `site/storage/donations.sqlite` (en `-wal`, `-shm`), `site/accounts`, `site/sessions`, `site/cache`, `site/config/config.*.php`, `content/*.txt`, `.git/`. Overweeg de database buiten de webmap (optie `tess.donations.database`).
- [x] **[test]** Bedankpagina: een onbekende of geraden `?ref` toont geen gegevens van iemand anders (de referentie is 20 hex-tekens, willekeurig). **Lokaal getest:** een onbekende referentie toont de algemene tekst.

### Invoer en XSS (OWASP A05)

- [ ] **[test]** Nagelezen: invoer van donateurs wordt ge-escaped op de muur, in de popup, op de bedankpagina en in de mails; databasevragen gebruiken parameters; namen zonder regeleindes (geen header-injectie in mails). Nog nakijken: de Panel-tabel in `index.js` toont berichten zonder `v-html` (geen gevonden), de JSON van `donaties/lijst`.
- [ ] **[test]** Proberen met `<script>`, `"><img onerror>` en lange teksten in naam, bericht en contactformulier. **Lokaal getest** met `<b>` en aanhalingstekens in naam en bericht op het donatieformulier: ge-escaped.

### Formulieren en betalingen

- [ ] **[test]** **B-14: Webhook zonder redirect.** Mollie volgt een 301/302 niet correct (de POST valt weg). De webhook-URL moet exact het definitieve domein zijn (https, met of zonder www, zoals de redirect). Testen via het Mollie-dashboard.
- [ ] **[test]** **B-15: Webhook-verwerking.** Al gebouwd (TASKS.md, Fase 4); hier testen we het. Mollie ondertekent de gewone webhooks niet, dus er is geen handtekening om te controleren. Daarom leest de webhook alleen het `id` en haalt de betaling altijd opnieuw op bij Mollie. Testen: **Gevonden en opgelost:** een verzonnen `tr_…` gaf 500 (Mollie probeert dan tot 26 uur opnieuw); nu 200. Lokaal getest: verzonnen en leeg ID → 200.
  - een POST met een verzonnen `tr_…` of een ongeldig `id`: antwoord 200, niets verandert (Mollie raadt 200 aan, zodat een aanvaller niets leert);
  - een echt `id` van een andere betaling (andere `reference` of `payment_id`): niets verandert;
  - dezelfde webhook twee keer, en tegelijk de bedankpagina herladen: één statuswijziging en één mail (de verwerking is idempotent via een SQLite-transactie);
  - in het foutlog staan alleen het betalings-ID en de technische fout, geen naam, e-mail of bericht.
- [ ] **[test]** Rate limit: geldt al per IP én per formulier (contact 5 per uur, doneren 10 per uur, elk met een eigen teller). Kirby gebruikt alleen `REMOTE_ADDR` en vertrouwt `X-Forwarded-For` bewust niet, anders kan een bot zijn IP vervalsen en de limiet omzeilen. Zit de host achter een proxy, dan is `REMOTE_ADDR` misschien voor iedereen hetzelfde. Nakijken met een tijdelijk testbestand dat het IP toont. Klopt het niet, dan regelt de host het (bv. Apache `mod_remoteip` met de proxy als vertrouwde bron), niet onze code.
- [x] **[test]** Origin-controle: een formulier vanaf een ander domein insturen wordt geweigerd. Een CSRF-token is hier niet nodig: CSRF misbruikt een ingelogde sessie, en het contact- en donatieformulier hebben geen sessie of rechten. Een vervalste inzending vanaf een andere site is niet erger dan dezelfde inzending rechtstreeks, en daar dienen honeypot, minimale invultijd en rate limit voor. Een token zou ook een sessiecookie voor elke bezoeker vereisen. Het Panel heeft Kirby's eigen CSRF-bescherming. **Lokaal getest:** contact → 403, doneren → terug met een foutmelding.
- [ ] **[test]** Cookies: Kirby's sessiecookie (alleen in het Panel) heeft `HttpOnly` en `SameSite=Lax`, en `Secure` zodra de site via https loopt. Nakijken in de browser na inloggen.
- [x] **[test]** Mollie- en Brevo-sleutels alleen in hostconfigbestanden, niet in Git. De Git-geschiedenis is nagekeken: geen sleutels gevonden. `test_` op de testserver, `live_` alleen live.
- [ ] **[inhoud]** E-maildomein: SPF, DKIM en DMARC voor `samenvoortess.be`, zodat niemand mails kan vervalsen uit naam van de vzw.

### Pakketten (OWASP A03)

- [x] **[test]** `composer audit` en `npm audit`: geen bekende lekken. Kirby op de laatste 5.x-patch (nu 5.6.1, de beveiligingspagina van Kirby nakijken), Mollie-SDK bijgewerkt. **Gedaan:** geen lekken; Kirby 5.6.1 is de nieuwste. Van de Mollie-SDK bestaat versie 4.0: een grote update, geen beveiligingsfix, dus een aparte taak.
- [ ] **[test]** Op de server `composer install --no-dev`; `node_modules` niet deployen.
- [x] **[code]** Updates structureel: Dependabot aanzetten op de GitHub-repository (`mctantwerp/theagency2627-steunactie-tess`) voor Composer en npm, en in IMPLEMENTATIE.md vastleggen wie na de oplevering de update-PR's en Kirby-beveiligingsmeldingen opvolgt. **Gebouwd:** `.github/dependabot.yml` (wekelijks, Composer en npm). Opvolging: iemand van de vzw, zie Onderhoud na de oplevering in IMPLEMENTATIE.md.
- [x] **[test]** Geen externe scripts of lettertypes (alles lokaal: ok). In het netwerkpaneel nakijken dat bezoekers geen verbinding maken met derden. **Lokaal getest:** de browser maakt alleen verbinding met het eigen domein.

### Fouten, logboek en back-ups (OWASP A09, A10, A08)

- [ ] **[test]** Met debug uit: een fout geeft een nette foutpagina, zonder paden of stacktrace.
- [ ] **[test]** Logboeken bevatten geen e-mailadressen of berichten van donateurs, alleen technische fouten.
- [ ] **[test]** Back-up op de server van `content/`, `site/accounts` en `site/storage` (de donateursgegevens). Afspreken wie dit doet en hoe vaak.
- [ ] **[test]** Herstel testen: één keer een back-up terugzetten in een testkopie en nakijken dat de site, de Panel-accounts en de donaties er weer zijn. Een back-up die nooit hersteld is, is geen betrouwbare back-up.
- [ ] **[test]** Monitoring live: een gratis uptimecontrole (bv. UptimeRobot) op de homepage, met een melding naar het team. Daarnaast af en toe in het Mollie-dashboard kijken naar mislukte webhooks, en in Brevo naar geweigerde mails.

### Privacy (raakt alle drie)

- [x] **[test]** Gewone bezoekers krijgen geen cookies (Kirby zet er alleen een in het Panel). Dan is geen cookiebanner nodig. Nakijken in de browser. **Lokaal getest:** geen cookies op home, doneren en updates.
- [ ] **[inhoud]** Privacytekst: staat al bij TASKS.md, Fase 12.
- [ ] **[inhoud]** Verwerkersovereenkomsten: Brevo en de host (Combell) verwerken persoonsgegevens voor de vzw. Hun overeenkomst zit meestal in de algemene voorwaarden; nakijken en bewaren. Mollie is voor de betaling zelf grotendeels zelf verantwoordelijk; ook nakijken in hun voorwaarden.
- [ ] **[inhoud]** Bewaartermijn van de donateursgegevens vastleggen (bv. zolang de boekhouding ze nodig heeft) en in de privacytekst zetten.

### Extra

- [x] **[code]** `/.well-known/security.txt` met een contactadres voor beveiligingsmeldingen en een vervaldatum. Optioneel; `.htaccess` laat `.well-known` al door. **Gebouwd:** met het contactadres uit Instellingen.

**Klaar als:** debug uit staat, geen enkel intern bestand bereikbaar is, securityheaders.com A geeft, `composer audit` schoon is en het Panel alleen met 2FA bereikbaar is.

## Volgorde

De [code]-punten kunnen lokaal; veel [test]-punten kunnen pas op de testserver (HTTPS, headers, webhook, proxy-IP). De testserver opzetten (TASKS.md, Fase 0) loopt daarom parallel.

1. **Vóór de eerste deploy (lokaal):** Deel 0 (`noindex`), B-1 tot B-4 (debug uit, vaste `url`, `content.salt`, `cookie.key`, deploylijst en interne bestanden onbereikbaar) en de headers met de CSP als Report-Only. Zo staat de site nooit onbeschermd online.
2. **Tegelijk:** testserver opzetten volgens TASKS.md, Fase 0.
3. **Lokaal, terwijl de server klaargemaakt wordt:** contrast, lichtsporen, formulierfouten, taal (T-1), canonical, hreflang, sitemap, Open Graph, favicon, foto's verkleinen, toegankelijkheidsverklaring.
4. **Op de testserver:** webhook (B-14, B-15), headers en CSP-console, HSTS kort, proxy-IP, alle [test]-punten en de schermlezerronde. Daarna de CSP echt aanzetten.
5. **Voor de livegang:** back-up en hersteltest, privacytekst, Search Console.

## Deel 4: Afronding

- [ ] Korte samenvatting onderaan dit bestand: scores (Lighthouse, axe, securityheaders, SSL Labs), wat aangepast is en wat nog openstaat.
- [ ] IMPLEMENTATIE.md bijwerken: headers, hostconfig (url, salt, cookie key), deploylijst, 2FA, toegankelijkheidsverklaring.

## Open vragen voor de opdrachtgever

- **Deelafbeelding** (1200 × 630) en een definitief **favicon** (nu een tijdelijk hartje): wie levert ze aan?
- **Back-ups:** doet Combell dit, of moeten we het zelf regelen?

## Tweede controle

Een tweede controle (Perplexity, 23-10-2026) is hierboven verwerkt. Niet of anders overgenomen:

| Opmerking | Waarom niet of anders |
|---|---|
| Mollie-webhook valideren en idempotent maken is "de belangrijkste ontbrekende controle" | Dat is al gebouwd (TASKS.md, Fase 4): de betaling wordt altijd bij Mollie opgehaald en de verwerking is idempotent. Een handtekening controleren kan niet, want Mollie ondertekent de gewone webhooks niet. Het is een testpunt geworden (B-15). |
| Een ongeldig webhook-ID "netjes afwijzen" | Mollie raadt 200 aan voor onbekende ID's, zodat een aanvaller niets leert. Ik schreef dat de code dat al deed; bij het testen bleek een verzonnen ID 500 te geven. Opgelost (B-15). |
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
