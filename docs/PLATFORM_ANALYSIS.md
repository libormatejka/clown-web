# Analýza: sestavování webů z balíčků Clown přes `clown/platform`

> **Stav: návrh (analýza), nic z popsaného cílového stavu zatím neexistuje.**
> Dokument popisuje, jak sestavovat více webů (`clown-web`, `url-shortener`, …)
> nad sdílenou vrstvou, aby se v každém webu řešil jen frontend a administrace,
> Docker a build zůstaly na jednom místě. Navazuje na
> [`CMS_CORE_ARCHITECTURE.md`](CMS_CORE_ARCHITECTURE.md), který popisuje stav
> dnes.
>
> Revize 2026-11-26: první verze byla po kontrole proti kódu opravena
> (viz kapitola 10). Tvrzení o současném stavu jsou ověřená proti
> repozitářům `clown-web`, `url-shortener`, `clown-core`,
> `clown-affiliate-shorturl` a `clown-core-assets`.

## 1. Výchozí stav (zjištěno v kódu)

- **Druhý web je kopie prvního.** `url-shortener` vznikl z `clown-web`. Z 51
  souborů sledovaných v `clown-web`, které existují i v `url-shortener`, je 46
  shodných byte-for-byte. Liší se jen `.gitignore`, `config/common.neon`
  (název a popis webu), front layout, front homepage a `style.css`.
  Shodné jsou admin presentery (Homepage, Log, Packages), `RouterFactory`,
  `SeedCommand`, `AssetManifestResolver`, `PackageCatalogService`,
  `webpack.config.js`, `assets/admin.js`, Dockerfiles, Makefile a
  `docker-compose.yml`. Každá oprava adminu se dělá dvakrát a weby se rozcházejí.
- **Weby nemohou běžet současně.** Mají stejný Makefile s
  `--project-name clownweb`, stejnou DB `clownweb_coreonly` a stejné porty (80,
  3307, 10000). Nový nástroj v Dockeru (např. phpMyAdmin) se proto přidává do
  každého webu zvlášť.
- **Admin je otevřený úpravám.** `Clown\Core\Template\OverridableTemplates`
  hledá šablonu nejdřív v `app/templates/override/` webu, a to i pro modul
  `Admin`. Web dnes přebíjí `Admin/@layout.latte` (fonty, výčet JS/CSS).
- **Wiring adminu zůstává na webu.** Balíček „sám JS nikam nenapojuje“.
  Web proto drží `assets/admin.js` (import modulů z
  `@libormatejka/clown-core-assets` plus inicializace), TinyMCE pravidlo ve
  `webpack.config.js`, ruční `mapping:` v `doctrine.neon` pro každý addon,
  `extensions:` v `common.neon` a content providery.
- **Dashboard je přebitý.** Core má vlastní
  `Clown\Core\Admin\Homepage\HomepagePresenter`; web ho přebíjí verzí
  převzatou z collectorboy (karty Page/MediaLibrary/User/Log).
- **Stránky Log a Packages jsou ve webu**, i když entity `Log`/`LogType` jsou v
  core. `PackageCatalogService` (čte `composer.lock`) a obě menu providery
  (`LogAdminMenuProvider`, `PackagesAdminMenuProvider`) jsou také ve webu.
- **Forwarding stuby už nejsou potřeba.** `ClownCoreExtension::registerPresenterFallbacks()`
  registruje `PresenterFactory` s fallbacky pro presentery z core a web
  žádné stuby nemá. `CMS_CORE_ARCHITECTURE.md` o nich ještě píše, je zastaralý.
- **Core ani addony nemají migrace.** Všechny tři migrace (`media_library`,
  `menu`, `menu_item`, `page`, `user`, `login_attempt`, `log_type`, `log`) leží v
  `migrations/` webu, i když jde o tabulky z core. Každý nový web by je
  generoval znovu.
- **Neexistují role ani oprávnění.** Entita `User` nemá role a core nemá
  autorizátor (`IAuthorizator`, `isAllowed`). Každý přihlášený uživatel vidí
  všechny admin sekce.
- **Composer ignoruje `repositories` z balíčků**, bere je jen z root
  `composer.json`. Blok `repositories` (VCS URL) se proto opakuje v každém webu
  a nelze ho „zdědit“. Totéž platí o GitHub tokenu. Všechny balíčky jsou
  navíc na `dev-main`, takže `composer update` zvedne všechno na aktuální `main`.
- **Addon už nese vlastní admin.** Např. `clown-affiliate-shorturl` obsahuje
  presenter, formuláře, šablony, `AdminMenuProvider` (tag
  `clown.adminMenuItem`) a CRUD služby. Menu se po registraci extension
  propíše samo. Runtime hook (`AffiliateUrlStartup::register()`) má podle
  komentáře v extension volat web v `www/index.php`; `url-shortener` ale addon
  zatím nevyžaduje a `index.php` je shodný s `clown-web`, takže jde o plánovaný
  stav. Pozor i na `repositories` s cestou `/clown-core` natvrdo v
  `composer.json` addonu.

## 2. Předpoklady

- **Stávající weby (`clown-web`, `url-shortener`) lze smazat a postavit znovu.**
  Neobsahují data ani obsah, které by se musely migrovat. Z toho plyne:
  nemusí se řešit přechodné stavy ani zpětná kompatibilita a migrace balíčků
  mohou začít načisto (jedna počáteční migrace na balíček). Před smazáním je
  třeba vytáhnout, co je unikátní: front šablony a styly, branding z
  `common.neon`, dashboard z `HomepagePresenter` (pro core) a
  `PackageCatalogService` (pro platformu).
- **Produkční nasazení je mimo rozsah.** Řeší se samostatně. Platforma musí jen
  umět vydat hotový admin bundle a spustit `migrate`; způsob nasazení na ní
  nezávisí.

## 3. Cílový stav

### 3.1 Tři vrstvy

```
clown/core + addony (articles, gallery, taxonomy, …)   doména
  core: admin chrome, dashboard, Log stránka, migrace core, autorizace
  addon: entita, admin entity, migrace, extra.clown
        ▲
clown/platform                                          NOVÉ: „distribuce“
  ├─ discovery addonů (čte extra.clown z composeru)
  ├─ PackageCatalogService + stránka Balíčky
  ├─ RouterFactory, obecný SeedCommand, AssetManifestResolver
  ├─ docker/ (php image, nginx šablona)
  └─ bin/clown (init, migrate, seed, build)
        ▲
web (clown-web, url-shortener, …)
  ├─ composer.json                výběr addonů + clown/platform
  ├─ config/site.neon             název, DB, branding, zapnuté addony
  ├─ assets/front                 front styly a skripty
  └─ app/templates/override/Front front šablony
```

**Pravidlo:** co by bylo v dalším webu stejné, patří do platformy nebo core.
Web smí obsahovat jen to, co se mezi weby liší. Admin konkrétní entity zůstává
v balíčku té entity.

### 3.2 Kde co žije

| Věc | Kde | Poznámka |
|---|---|---|
| Entita, služby, migrace | balíček addonu | Addon nese i vlastní migrace. |
| Admin presenter, formuláře, šablony entity | balíček addonu | Už dnes tak funguje. |
| Admin chrome, sdílené komponenty (CRUD, DataGrid, form renderer) | `clown/core` | Jediná páka na jednotný vzhled adminu. |
| Dashboard | `clown/core` | Nahradí současný minimální dashboard verzí z webu. Karty přidávají addony přes nový tag (např. `clown.dashboardWidget`). |
| Stránka Log aktivit | `clown/core` | Entity `Log`/`LogType` a subscriber už v core jsou. |
| Migrace entit core | `clown/core` | Dnes leží ve webu. |
| Autorizace (role, oprávnění per modul) | `clown/core` | Neexistuje, viz 3.5. |
| Discovery a registrace addonů | `clown/platform` | Nově, viz 3.3. |
| `PackageCatalogService` a stránka Balíčky | `clown/platform` | Je to introspekce nainstalovaných balíčků, tedy téma discovery. |
| `RouterFactory`, obecný `SeedCommand`, `AssetManifestResolver` | `clown/platform` | Dnes shodné v obou webech. |
| Zdroje admin JS/CSS a webpack preset | `clown-core-assets` | Viz 3.6. |
| Docker image, nginx šablona | `clown/platform` (`docker/`) | |
| Sdílená dev infrastruktura (proxy, DB, phpMyAdmin) | samostatný `clown-dev` stack | Běží jednou pro všechny weby. |
| Front presentery, šablony a assety | web | Design je právě to, co se liší. |
| Branding, název, DB, volba addonů | web (`config/site.neon`) | |

### 3.3 Discovery addonů

Addon deklaruje v `composer.json`:

```json
{
  "extra": {
    "clown": {
      "extension": "Clown\\AffiliateShortUrl\\DI\\ClownAffiliateShortUrlExtension",
      "entities": "src/Entity",
      "migrations": "migrations",
      "requires": ["clown/core"]
    }
  }
}
```

Platforma při kompilaci kontejneru přečte `vendor/composer/installed.json` a:

1. seřadí addony podle grafu závislostí z composeru,
2. zaregistruje DI extension,
3. přidá doctrine mapping a složku migrací,
4. zaregistruje runtime hooky (místo ruční úpravy `www/index.php`),
5. vynechá addony vypnuté v konfiguraci webu.

Instalace není totéž co zapnutí: addon se po `composer require` zapne
automaticky, ale web ho může vypnout. Citlivé addony (`api`, `webmcp`, `ai`)
by měly být do zavedení autorizace (3.5) ve výchozím stavu **vypnuté** a web
je musí zapnout výslovně.

```neon
# config/site.neon
platform:
	web:
		name: 'BatBoy.cz'
	addons:
		taxonomy: off        # nainstalovaný tranzitivně, web ho nechce v adminu
		api: on              # citlivý addon, výchozí je off
	admin:
		menuPositions:
			AffiliateUrl: 20 # přepis pevné pozice z addonu
```

**Invalidace cache kontejneru.** Discovery znamená, že každá změna balíčků musí
zneplatnit zkompilovaný kontejner (`temp/cache`). Composer skripty z
nainstalované závislosti se u root projektu nespouštějí (běží jen skripty
root `composer.json` a pluginů), takže `post-autoload-dump` v platformě
nestačí. Spolehlivější je předat hash `installed.json` do `Configurator` jako
statický parametr, který je součástí cache klíče kontejneru. Při implementaci
ověřit.

### 3.4 Zamčení adminu

`OverridableTemplates` / `TemplateOverrideResolver` dostane allowlist, např.
`clownCore.overridable: [Front/*]`. Pokus o override pod `Admin/` v debug režimu
spadne, v produkci se ignoruje. Protože weby vznikají znovu (kapitola 2), může
allowlist platit od první verze platformy. Jediný kontrolovaný únik je branding
přes konfiguraci (logo, akcentní barva přes CSS proměnné, název).

Potřebuje-li web vlastní admin sekci, vyrobí se z ní addon, ne kód ve webu.

### 3.5 Autorizace (nová práce v core)

Dnes nejsou role ani oprávnění. Automatické zapnutí addonů tak znamená, že
každý přihlášený uživatel uvidí všechny sekce všech zapnutých addonů. Je to
samostatný kus práce v core, ne jen konfigurace:

- role na entitě `User` a `IAuthorizator` v core,
- oprávnění deklarovaná modulem (např. ve stejném místě jako admin menu
  provider), aby addon uměl říct, kdo smí jeho sekci vidět,
- menu a presentery se řídí oprávněním.

Do jejího vzniku platí výchozí „vypnuto“ pro citlivé addony (3.3).

### 3.6 Admin frontend: kde se sestavuje

Tailwind musí při buildu skenovat šablony **všech nainstalovaných addonů**.
`clown-core-assets` je záměrně „bez site-specific `@source` cest“ a nezná sadu
addonů, takže hotový admin bundle sestavit samo nemůže. Varianty:

| Varianta | Jak | Plus | Minus |
|---|---|---|---|
| **A. Preset v `clown-core-assets`, build ve webu** | Admin entry (`admin.js`) a webpack konfigurace se přesunou do `clown-core-assets` jako hotový preset. Web dělá `npm run build` s presetem a svojí sadou addonů. | Nejlevnější. Web přestane psát wiring, Tailwind skenuje jen jeho addony. Žádný nový build systém. | Web dál potřebuje Node a build admina. Admin CSS se může mezi weby lišit podle sady addonů. |
| **B. Jeden hotový bundle z CI** | Samostatný build (CI platformy nebo build repozitář) sestaví admin proti všem addonům a vydá výstup jako `dist/` v composer balíčku nebo release artefakt. | Web admin vůbec nebuilduje a nepotřebuje Node pro admin. Admin je v každém webu stejný. | Větší CSS (třídy addonů, které web nepoužívá). Potřeba cesta, jak se bundle dostane do `www/`. Další artefakt k verzování. |
| **C. Hybrid** | Preset z A, ale build centrálně jako v B. | Zdroje zůstávají v `clown-core-assets`, výstup je jednotný. | Součet složitosti obou. |

**Doporučení:** začít variantou A, která okamžitě odstraní duplicitní wiring.
O přechodu na B/C rozhodnout po krátkém spiku (velikost CSS, způsob doručení
do `www/`). `clown/platform` je PHP balíček a nemá obsahovat Node toolchain.

### 3.7 Migrace

- Core i každý addon dostane vlastní `migrations/` s jednou počáteční
  migrací vygenerovanou ze svých entit (bez historie ze stávajících webů).
- Platforma registruje složky migrací všech zapnutých balíčků v pořadí podle
  závislostí. Doctrine řadí migrace podle názvu verze napříč složkami, takže
  časové značky počátečních migrací musí odpovídat grafu závislostí (core před
  taxonomy před gallery apod.).
- Web při deployi spouští `migrate`. `schema-tool:create` a `schema-tool:update`
  v Makefile odpadají.
- Počáteční migrace jsou po vzniku prvního produkčního webu **zmrazené**. Dál
  se jen přidávají nové.

### 3.8 Docker

Sdílený dev stack `clown-dev` (reverzní proxy Traefik/Caddy, MariaDB,
phpMyAdmin) běží jednou. Každý web má jen vlastní `php` kontejner v sdílené
síti a je dostupný jako `clown-web.localhost`, `url-shortener.localhost`. Nový
nástroj (Mailpit, Redis…) se přidá do stacku jednou. Image a nginx šablona se
berou z `clown/platform`.

Docker kolem webu nelze dát jen do composer balíčku: `composer install` už musí
běžet v kontejneru. Proto sdílený stack mimo weby a `bin/clown` jako tenký
bootstrap.

### 3.9 Distribuce a verzování

- **Semver tagy (`^1.0`) jsou podmínka**, ne závěrečný krok. Bez nich je
  „release platformy = `composer update clown/platform`“ nepředvídatelný,
  protože se zvedne vše na aktuální `main`. Musí být hotové před discovery.
- Jeden privátní composer zdroj (Private Packagist, Satis nebo GitHub Packages)
  místo opakovaných VCS bloků a tokenů ve webech.
- Lokální vývoj přes `path` repozitáře patří do konfigurace vývojáře, ne do
  `composer.json` balíčků (`clown-affiliate-shorturl` má dnes natvrdo
  `/clown-core`).

### 3.10 Nový web a skeleton

`composer create-project clown/web-skeleton`: `composer.json`,
`config/site.neon`, `app/Bootstrap.php`, front šablony, front assety. Skeleton
musí zůstat tenký, protože se po vytvoření s platformou nesynchronizuje.

Protože weby vznikají znovu (kapitola 2), není skeleton poslední krok, ale
**akceptační test platformy**: co v něm po sestavení zůstane navíc, patří do
platformy nebo core. `clown-web` a `url-shortener` se z něj postaví znovu a
staré repozitáře se smažou.

## 4. Plusy

- Jedno místo pro admin, build a Docker: oprava se dělá jednou a dostane se do
  všech webů přes `composer update`.
- Admin se nemůže rozejít: override je zamčený a wiring je sdílený.
- Nový web je `composer create-project` plus front; nový addon je
  `composer require`.
- Weby mohou běžet vedle sebe, nástroje v Dockeru se přidávají jednou.
- `clown/core` zůstává nezávislé jádro, které nezná Docker, build ani
  sestavení aplikace.
- Navazuje na existující základ (registry a tagy v core, admin v addonech,
  fallback `PresenterFactory`), nepřepisuje se od nuly.
- Protože se weby staví znovu, odpadá kompatibilita a přechodné stavy.

## 5. Minusy a rizika

1. **Schéma DB.** Po `composer require` se entita okamžitě namapuje, ale
   tabulka neexistuje. Bez migrací v balíčku nebo s `schema-tool:update` hrozí
   500 nebo ztráta dat. Riziko se netýká jen addonů: ani core dnes migrace
   nemá. *Mitigace:* 3.7.
2. **Oprávnění neexistují.** Každý přihlášený uživatel vidí všechny sekce všech
   zapnutých addonů, nový addon znamená nový prostor pro útok. *Mitigace:*
   výchozí „vypnuto“ pro citlivé addony, autorizace v core (3.5), která je
   samostatná a nezanedbatelná práce.
3. **Frontend assety jsou nejtěžší část.** PHP se registruje automaticky, ale
   admin šablony addonů používají Tailwind třídy a addony mají vlastní JS
   (`gallery-assets`, `ai-assets`). CSS a JS se musí přebuildit pro každou
   sadu addonů. *Mitigace:* varianty v 3.6.
4. **Pořadí a závislosti extensions.** Registry se skládají v `beforeCompile()`
   a addony na sobě závisí. Discovery musí řadit podle grafu, jinak vznikají
   těžko diagnostikovatelné chyby. Neviditelná registrace se hůř ladí.
   *Mitigace:* `bin/clown addons:list`, invalidace cache podle `installed.json`
   (3.3).
5. **Jednotný admin se nezajistí sám od sebe.** Každý addon má vlastní `.latte`.
   Bez sdílených komponent v core se admin rozejde uvnitř balíčků. *Mitigace:*
   generický CRUD, form renderer a DataGrid šablona v core jako povinná cesta.
6. **Menší kontrola webu.** Pevné `position` v menu addonů se srážejí.
   *Mitigace:* přepis pozic v konfiguraci (3.3).
7. **Verzování a kompatibilita.** S 19 repozitáři se změna API core dotkne všech
   addonů. *Mitigace:* semver s kontraktem core (`^1`), CI s testy addonů proti
   core.
8. **Skeleton zastarává.** Po vytvoření webu se s ním nesynchronizuje.
   *Mitigace:* držet ho tenký, logiku mít v platformě.
9. **Údržba.** Vzniká nový balíček s vlastním CI, release procesem a
   dokumentací. Jedna chyba v platformě se projeví ve všech webech najednou.
10. **Docker mimo web.** Sdílený dev stack je mimo repozitář webu, takže nový
    vývojář musí nainstalovat navíc `clown-dev`.
11. **Zmrazené počáteční migrace.** Špatně navržená počáteční migrace se po
    prvním produkčním webu dá opravit jen další migrací. *Mitigace:* počáteční
    migrace pečlivě zrevidovat před prvním produkčním nasazením.

## 6. Zvažované alternativy

| Alternativa | Proč ne |
|---|---|
| Dál kopírovat web jako šablonu / fork | To je současný stav a důvod této analýzy: 46 z 51 souborů je kopie. |
| Všechno do `clown/core` | Porušilo by nezávislost core, které by začalo znát Docker a build. |
| Monorepo se všemi weby | Balíčky už jsou samostatné repozitáře a weby se nasazují odděleně. Přepis by stál víc než přínos. |
| Admin jen v platformě, ne v addonech | Addon s entitou by pak bez zásahu do platformy nešel použít. Admin entity patří k entitě. |
| Auto-discovery bez opt-outu | Tranzitivní addony by bez vědomí webu přidávaly sekce do adminu. |

## 7. Postup (od nejmenšího rizika)

Weby se staví znovu (kapitola 2), takže není co migrovat. Kroky 1–3 jsou
podmínkou discovery; krok 8 ověřuje celek.

1. **Semver tagy** u všech balíčků a **privátní composer zdroj**.
2. **Migrace v core a addonech** (3.7), včetně počátečních migrací.
3. **Core:** dashboard a stránka Log přesunuté z webu, tag pro dashboard widgety,
   fundament autorizace (3.5).
4. **`clown/platform`:** discovery přes `extra.clown`, opt-out a „výchozí
   vypnuto“ pro citlivé addony, přepis pozic menu, invalidace cache, zamčený
   `Admin/`, `PackageCatalogService` a stránka Balíčky, `RouterFactory`,
   obecný `SeedCommand`, `AssetManifestResolver`. Nejdřív na
   `clown-affiliate-shorturl`.
5. **Admin frontend:** varianta A (preset v `clown-core-assets`, 3.6), potom
   spike a rozhodnutí o B/C.
6. **Sdílený dev stack `clown-dev`** a Docker z platformy.
7. **Skeleton** `clown/web-skeleton`.
8. **Znovu postavit `clown-web` a `url-shortener`** ze skeletonu a staré
   repozitáře smazat. Co v nich po sestavení zbude navíc, patří do platformy
   nebo core.

Krok 6 (dev stack) je možné udělat nezávisle a kdykoliv dřív: je to
nejlevnější krok, který okamžitě vyřeší kolizi portů a phpMyAdmin.

## 8. Otevřené otázky

- **Má mít `clown/platform` povinně všechny addony, nebo jen core?** Návrh: core
  plus infrastrukturní addony (cache, revisions, front-extras), content addony
  si web vybírá.
- **Privátní repozitáře, nebo veřejná Packagist distribuce?** Rozhoduje o
  privátním composer zdroji.
- **Rozsah autorizace.** Stačí dvě role (admin, editor) a oprávnění per modul,
  nebo je potřeba jemnější model?
- **Varianta admin bundlu (3.6).** Rozhodnout po spiku.

## 9. Co se musí ověřit při implementaci

- Že hash `installed.json` předaný jako statický parametr `Configurator`
  zneplatní cache kontejneru (3.3).
- Řazení migrací napříč složkami balíčků podle verze (3.7).
- Že fallback `PresenterFactory` pokryje presentery addonů stejně jako core,
  nebo je potřeba další registrace fallbacků přes discovery.

## 10. Záznam revize

První verze byla zkontrolována proti kódu a opravena takto:

- Počet shodných souborů: 46 z 51, ne 45.
- `AffiliateUrlStartup::register()` není dnes v `www/index.php`; jde o plánovaný
  stav (addon `url-shortener` zatím nevyžaduje).
- Forwarding stuby už neexistují (fallbacky v `PresenterFactory`).
- Dashboard nepatří do platformy, ale do core (core má vlastní, web ho
  přebíjí). Stránka Log patří do core, stránka Balíčky do platformy.
- `PackageCatalogService` leží ve webu, ne v core.
- Role a oprávnění neexistují (původně otevřená otázka). Přidána kapitola 3.5
  a výchozí „vypnuto“ pro citlivé addony.
- Migrace core leží ve webu; core i addony potřebují vlastní (3.7).
- Admin bundle: místo „v platformě“ porovnány varianty s ohledem na Tailwind
  skenování šablon addonů (3.6).
- Invalidace cache: původní návrh „composer skript `post-autoload-dump`“ nestačí,
  protože composer nespouští skripty závislostí; navržen hash `installed.json`
  v cache klíči kontejneru (3.3).
- Semver přesunut na začátek postupu (původně předposlední krok).
- Přidán předpoklad, že stávající weby lze smazat a postavit znovu, a
  z toho plynoucí změny postupu (skeleton jako akceptační test).
- Produkční nasazení označeno jako mimo rozsah.
