# Wireframes Tess in Kirby

De PDF-wireframes zijn vertaald naar PHP-templates met responsive Tailwind CSS en kleine JavaScript-interacties. Betalingen zijn nog niet aangesloten. Het aangeleverde transparante PNG-logo wordt gebruikt in header en footer. Definitieve foto's kun je later via het Panel uploaden. De gekozen hoofdkleur staat in `assets/css/tailwind.css` in `@theme` bij `--color-pink` (#DA5050).

## Lokaal bekijken

Gebruik je bestaande Herd-site, of start vanuit de projectmap `composer start` (PHP en Composer moeten beschikbaar zijn). Open daarna de lokale site en `/panel`.

## Inhoud aanpassen

- Site-instellingen: huidig logo, contactadres, organisatiegegevens, Instagram-links, externe steunactie, doelbedrag en opgehaald bedrag.
- Home: titel, intro, foto's en stappen.
- Inhoudspagina's: introductie, tekstonderdelen, stappen en uitklapbare onderdelen.
- Updates: voeg een update toe, vul datum en tekst in en zet de update op Gepubliceerd. Alleen listed updates verschijnen in het overzicht en op de homepage.
- Foto's: upload afbeeldingen en voeg een beschrijvende alternatieve tekst toe.

De bestaande lege conceptpagina's zijn lokaal naar unlisted verplaatst zodat de navigatie werkt. Hun UUID's zijn behouden. Er is niets naar GitHub gepusht of online gedeployd.

## Nog aan te leveren

Goedgekeurde inhoud, contactgegevens, definitieve foto's en bevestigde donatietotalen. Privacy en voorwaarden bevatten duidelijk aangegeven conceptinhoud. Er worden geen voorbeeldbedragen of verzonnen medische updates als echte gegevens getoond.

## Later

Betaalintegratie en een bevestigingsmail na een donatie zijn nog niet geïmplementeerd. De bedragkeuze is alleen een visuele voorbereiding en verricht geen betaling; het gekozen bedrag wordt ook niet aan een externe steunactie doorgegeven.

## Contactformulier en e-mail (Brevo)

Het contactformulier verstuurt berichten via de SMTP-relay van Brevo naar het Contactadres uit Site-instellingen. Antwoorden gaat rechtstreeks naar de bezoeker (Reply-To). De afhandeling staat in `site/plugins/tess-contact`, de mail in `site/templates/emails/contact.html.php` (opgemaakt) en `contact.text.php` (platte tekst, voor mailprogramma's zonder HTML). De gedeelde opmaak (kop, kader, voettekst) staat in `site/snippets/emails/layout.php` en is herbruikbaar voor latere mails, zoals de donatiebevestiging. Berichten worden niet bewaard op de server. Spam wordt tegengehouden met een verborgen veld, een minimale invultijd en maximaal 5 berichten per IP-adres per uur.

De SMTP-instellingen staan in `site/config/config.php`. Login en SMTP-key horen niet in Git: kopieer `site/config/config.host.example.php` naar `config.samenvoortess.be.php` (en `config.www.samenvoortess.be.php` als www gebruikt wordt) op de server en vul daarin de gegevens uit Brevo in (Settings → SMTP & API → SMTP). Zie ook [Live zetten](#live-zetten).

Lokaal gaat de mail naar Mailpit in plaats van Brevo, via `site/config/config.theagency2627-steunactie-tess.test.php` (host `127.0.0.1`, poort `1025`, zonder login of versleuteling). Verstuurde berichten bekijk je op http://localhost:8025.

In Brevo moet het domein `samenvoortess.be` geauthenticeerd zijn (SPF/DKIM) en `contact@samenvoortess.be` als afzender toegevoegd zijn, anders wordt de mail geweigerd of als spam gemarkeerd.

## Live zetten

Deze stappen gebeuren niet automatisch bij een deploy en moeten op de server zelf gebeuren:

- [ ] **Nieuw configbestand voor de server aanmaken.** Kopieer `site/config/config.host.example.php` naar `site/config/config.samenvoortess.be.php` op de server en vul de Brevo SMTP-login en SMTP-key in. Wordt de site ook via www bereikt, maak dan dezelfde kopie als `config.www.samenvoortess.be.php`. Dit bestand staat bewust niet in Git en moet dus met de hand geüpload worden. Zonder dit bestand verstuurt het contactformulier geen mail.
- [ ] Het lokale bestand `config.theagency2627-steunactie-tess.test.php` (Mailpit) **niet** naar de server kopiëren.
- [ ] In Brevo het domein `samenvoortess.be` authenticeren (SPF/DKIM-records bij de domeinbeheerder) en `contact@samenvoortess.be` als afzender toevoegen.
- [ ] Contactadres invullen in Site-instellingen in het Panel.
- [ ] `'debug' => true` in `site/config/config.php` uitzetten (of `'debug' => false` in het serverconfigbestand zetten), zodat bezoekers geen foutdetails te zien krijgen.
- [ ] Het contactformulier op de live site testen en de mail controleren in de inbox en in Brevo (Transactional → Logs).

## Typografie

Montserrat wordt gebruikt voor alle tekst. Alleen het losse woord Tess wordt automatisch in Caveat gezet, ook in inhoud uit het Panel. De lokale WOFF2-bestanden en fontlicenties staan in `assets/fonts`; er worden geen externe fontverzoeken gedaan door bezoekers.

De header is wit, de footer en zachte achtergrondvlakken zijn #FFD9DE. Het aangeleverde logo heeft witte letters; er staat tijdelijk een fijne donkere schaduw om de letters leesbaar te houden op lichte vlakken. Voor de definitieve versie is een transparante variant met donkere letters wenselijk.

## Donatievoortgang en rekeningnummer

De voortgangsbalk blijft zichtbaar zonder bevestigde cijfers, met XX.XXX, 14% en voorbeeldmijlpalen die expliciet als voorlopig worden aangeduid. Vul opgehaald bedrag en doelbedrag in bij Site-instellingen om de echte voortgang te tonen. Mijlpalen zijn afzonderlijk bewerkbaar. Het rekeningnummer BE97 7340 5272 3549 staat bij Help Tess en in de footer en is via Site-instellingen aanpasbaar.

## Tailwind ontwikkelen

De styling gebruikt Tailwind CSS met utility-klassen in `site/templates` en `site/snippets`. De kleuren, lettertypes en responsive breekpunten staan in `assets/css/tailwind.css`. Alleen rijke Kirby-tekst en native pseudo-elementen gebruiken daar beperkte componentregels met `@apply`.

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

Alle websiteteksten staan in de contentbestanden en hebben velden in het Panel. De homepage gebruikt `content/home/home.txt`; de andere pagina’s gebruiken hun eigen contentbestand. Header, footer en de gedeelde oproep tot steun gebruiken `content/site.txt` (Panel: Gedeelde websiteteksten). Updateknoppen en de melding zonder updates staan in `content/updates/updates.txt`, ook voor de homepage en losse updates. Fototeksten, formulierlabels en JavaScript-meldingen zijn eveneens bewerkbaar. PHP bevat de opbouw en logica, zonder vaste websiteteksten.
