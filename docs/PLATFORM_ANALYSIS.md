# Analýza: sestavování webů z balíčků Clown přes `clown/platform`

> **Stav: návrh (analýza), nic z popsaného cílového stavu zatím neexistuje.**
> Dokument popisuje, jak sestavovat více webů (`clown-web`, `url-shortener`, …)
> nad sdílenou vrstvou, aby se v každém webu řešil jen frontend a administrace,
> Docker a build zůstaly na jednom místě. Navazuje na
> [`CMS_CORE_ARCHITECTURE.md`](CMS_CORE_ARCHITECTURE.md), který popisuje stav
> dnes.

## 1. Výchozí stav (zjištěno v kódu)

- **Druhý web je kopie prvního.** `url-shortener` vznikl z `clown-web`. Z 51
  souborů sledovaných v `clown-web` je 45 v `url-shortener` shodných
  byte-for-byte. Shodné jsou admin presentery (Homepage, Log, Packages),
  `RouterFactory`, `SeedCommand`, `AssetManifestResolver`,
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
  Web proto drží `assets/admin.js`, TinyMCE pravidlo ve `webpack.config.js`,
  ruční `mapping:` v `doctrine.neon` pro každý addon, `extensions:` v
  `common.neon` a content providery.
- **Composer ignoruje `repositories` z balíčků**, bere je jen z root
  `composer.json`. Blok `repositories` (VCS URL) se proto opakuje v každém webu
  a nelze ho „zdědit“. Totéž platí o GitHub tokenu.
- **Addon už nese vlastní admin.** Např. `clown-affiliate-shorturl` obsahuje
  presenter, formuláře, šablony, `AdminMenuProvider` (tag
  `clown.adminMenuItem`) a CRUD služby. Menu se po registraci extension
  propíše samo. Zbývá automatizovat napojení (viz níže).

## 2. Cílový stav

### 2.1 Tři vrstvy

```
clown/core + addony (articles, gallery, taxonomy, …)   doména, beze změny
        ▲
clown/platform                                          NOVÉ: „distribuce“
  ├─ admin stránky, které nepatří žádné entitě
  │    (Homepage dashboard, Log aktivit, Balíčky)
  ├─ SeedCommand, RouterFactory, AssetManifestResolver
  ├─ discovery addonů (čte extra.clown z composeru)
  ├─ prebuilt admin bundle (JS + CSS)
  ├─ webpack preset pro front
  ├─ docker/ (php image, nginx šablona)
  └─ bin/clown (init, migrate, seed, build)
        ▲
web (clown-web, url-shortener, …)
  ├─ composer.json                výběr addonů + clown/platform
  ├─ config/site.neon             název, DB, branding, zapnuté addony
  ├─ assets/front                 front styly a skripty
  └─ app/templates/override/Front front šablony
```

**Pravidlo:** co by bylo v dalším webu stejné, patří do platformy. Web smí
obsahovat jen to, co se mezi weby liší.

### 2.2 Kde co žije

| Věc | Kde | Poznámka |
|---|---|---|
| Entita, služby, migrace | balíček addonu | Addon nese i vlastní migrace. |
| Admin presenter, formuláře, šablony entity | balíček addonu | Už dnes tak funguje. |
| Admin chrome, sdílené komponenty (CRUD, DataGrid, form renderer) | `clown/core` | Jediná páka na jednotný vzhled adminu. |
| Admin stránky bez entity (Homepage, Log, Packages) | `clown/platform` | Dnes jsou v každém webu zvlášť. |
| Discovery a registrace addonů | `clown/platform` | Nově, viz 2.3. |
| Admin JS a CSS | `clown/platform` (prebuilt bundle) | Web admin nebuilduje. |
| Front presentery a šablony, front assety | web | Design je právě to, co se liší. |
| Docker image, nginx šablona | `clown/platform` (`docker/`) | |
| Sdílená dev infrastruktura (proxy, DB, phpMyAdmin) | samostatný `clown-dev` stack | Běží jednou pro všechny weby. |
| Branding, název, DB, volba addonů | web (`config/site.neon`) | |

### 2.3 Discovery addonů

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

Platforma při kompilaci kontejneru přečte `vendor/composer/installed.json`
(`PackageCatalogService` už dnes čte `composer.lock`) a:

1. seřadí addony podle grafu závislostí z composeru,
2. zaregistruje DI extension,
3. přidá doctrine mapping a složku migrací,
4. zaregistruje runtime hooky (dnes je `AffiliateUrlStartup::register()` ručně
   v `www/index.php`),
5. vynechá addony vypnuté v konfiguraci webu.

Instalace není totéž co zapnutí: addon se po `composer require` zapne
automaticky, ale web ho může vypnout.

```neon
# config/site.neon
platform:
	web:
		name: 'BatBoy.cz'
	addons:
		taxonomy: off        # nainstalovaný tranzitivně, web ho nechce v adminu
	admin:
		menuPositions:
			AffiliateUrl: 20 # přepis pevné pozice z addonu
```

### 2.4 Zamčení adminu

`OverridableTemplates` / `TemplateOverrideResolver` dostane allowlist, např.
`clownCore.overridable: [Front/*]`. Pokus o override pod `Admin/` v debug režimu
spadne, v produkci se ignoruje. Jediný kontrolovaný únik je branding přes
konfiguraci (logo, akcentní barva přes CSS proměnné, název).

Potřebuje-li web vlastní admin sekci, vyrobí se z ní addon, ne kód ve webu.

### 2.5 Docker

Sdílený dev stack `clown-dev` (reverzní proxy Traefik/Caddy, MariaDB,
phpMyAdmin) běží jednou. Každý web má jen vlastní `php` kontejner v sdílené
síti a je dostupný jako `clown-web.localhost`, `url-shortener.localhost`. Nový
nástroj (Mailpit, Redis…) se přidá do stacku jednou. Image a nginx šablona se
berou z `clown/platform`.

Docker kolem webu nelze dát jen do composer balíčku: `composer install` už musí
běžet v kontejneru. Proto sdílený stack mimo weby a `bin/clown` jako tenký
bootstrap.

### 2.6 Distribuce a verzování

- Semver tagy (`^1.0`) místo `dev-main` u všech balíčků.
- Jeden privátní composer zdroj (Private Packagist, Satis nebo GitHub Packages)
  místo opakovaných VCS bloků a tokenů ve webech.
- Release platformy = `composer update clown/platform` ve webu. Platform
  závislosti zafixuje, takže se aktualizuje i core a admin bundle.
- Lokální vývoj přes `path` repozitáře patří do konfigurace vývojáře, ne do
  `composer.json` balíčků (`clown-affiliate-shorturl` má dnes natvrdo
  `/clown-core`).

### 2.7 Nový web

`composer create-project clown/web-skeleton`: `composer.json`,
`config/site.neon`, `app/Bootstrap.php`, front šablony, front assety. Skeleton
musí zůstat tenký, protože se po vytvoření s platformou nesynchronizuje.

## 3. Plusy

- Jedno místo pro admin, build a Docker: oprava se dělá jednou a dostane se do
  všech webů přes `composer update`.
- Admin se nemůže rozejít: bundle je hotový a override je zamčený.
- Nový web je `composer create-project` plus front; nový addon je
  `composer require`.
- Weby mohou běžet vedle sebe, nástroje v Dockeru se přidávají jednou.
- `clown/core` zůstává nezávislé jádro, které nezná Docker, build ani
  sestavení aplikace.
- Navazuje na to, co už existuje (registry v core, tagy, `PackageCatalogService`,
  admin v addonech), nic se nepřepisuje od nuly.

## 4. Minusy a rizika

1. **Schéma DB.** Po `composer require` se entita okamžitě namapuje, ale
   tabulka neexistuje. Bez migrací v addonu nebo s `schema-tool:update` hrozí
   500 nebo ztráta dat. Web dnes používá `schema-tool:create` a jednu složku
   migrací, což pro automatiku nestačí. *Mitigace:* migrace v addonech,
   registrace složek, `migrate` při deployi.
2. **Instalováno ≠ zapnuto.** Addony se tahají tranzitivně (`gallery` →
   `taxonomy`) a v adminu se objeví sekce, kterou web nechtěl, i nový prostor
   pro útok. *Mitigace:* opt-out v konfiguraci. Role a oprávnění per modul
   nejsou ověřené a budou potřeba.
3. **Frontend assety jsou nejtěžší část.** PHP se registruje automaticky, ale
   admin šablony addonů používají Tailwind třídy a addony mají vlastní JS
   (`gallery-assets`, `ai-assets`). CSS a JS se musí přebuildit pro každou
   sadu addonů. Prebuilt bundle musí skenovat šablony všech addonů, takže
   obsahuje i třídy addonů, které web nepoužívá. *Mitigace:* build matice
   nebo lazy chunky per addon.
4. **Pořadí a závislosti extensions.** Registry se skládají v `beforeCompile()`
   a addony na sobě závisí. Discovery musí řadit podle grafu, jinak vznikají
   těžko diagnostikovatelné chyby. Neviditelná registrace se hůř ladí.
   *Mitigace:* `bin/clown addons:list`, `composer update` musí invalidovat
   `temp/cache` (dnes ruční krok).
5. **Jednotný admin se nezajistí sám od sebe.** Každý addon má vlastní `.latte`.
   Bez sdílených komponent v core se admin rozejde uvnitř balíčků. *Mitigace:*
   generický CRUD, form renderer a DataGrid šablona v core jako povinná cesta.
6. **Menší kontrola webu.** Pevné `position` v menu addonů se srážejí. *Mitigace:*
   přepis pozic v konfiguraci (viz 2.3).
7. **Verzování a kompatibilita.** S 19 repozitáři se změna API core dotkne všech
   addonů. *Mitigace:* stabilní kontrakt core (`^1`), CI s testy addonů proti
   core, release procesy.
8. **Skeleton zastarává.** Po vytvoření webu se s ním nesynchronizuje.
   *Mitigace:* držet ho tenký, logiku mít v platformě.
9. **Přepis a údržba.** Vzniká nový balíček s vlastním CI, release procesem a
   dokumentací. Jedna chyba v platformě se projeví ve všech webech najednou.
10. **Docker mimo web.** Sdílený dev stack je mimo repozitář webu, takže nový
    vývojář musí nainstalovat navíc `clown-dev`. Produkční nasazení tím není
    pokryté (viz otevřené otázky).

## 5. Zvažované alternativy

| Alternativa | Proč ne |
|---|---|
| Dál kopírovat web jako šablonu / fork | To je současný stav a důvod této analýzy: 45 z 51 souborů je kopie. |
| Všechno do `clown/core` | Porušilo by nezávislost core, které by začalo znát Docker a build. |
| Monorepo se všemi weby | Balíčky už jsou samostatné repozitáře a weby se nasazují odděleně. Přepis by stál víc než přínos. |
| Admin jen v platformě, ne v addonech | Addon s entitou by pak bez zásahu do platformy nešel použít. Admin entity patří k entitě. |
| Auto-discovery bez opt-outu | Tranzitivní addony by bez vědomí webu přidávaly sekce do adminu. |

## 6. Postup migrace (od nejlevnějšího)

1. **Sdílený dev stack** (`clown-dev`: proxy, MariaDB, phpMyAdmin) mimo weby.
   Vyřeší kolizi portů a phpMyAdmin. Nic se nerozbije.
2. **`clown/platform`, část 1:** přesunout shodné PHP soubory (admin presentery
   Homepage/Log/Packages, `RouterFactory`, `SeedCommand`,
   `AssetManifestResolver`) z webů do platformy. Ověřit, jestli
   `Clown\Core\Application\PresenterFactory` s fallbacky nahradí forwarding
   stuby (dokumentace je pravděpodobně zastaralá).
3. **Migrace v addonech** a jejich registrace. Podmínka všeho dalšího.
4. **Discovery** přes `extra.clown`, nejdřív na `clown-affiliate-shorturl`:
   extension, doctrine mapping, migrace, runtime hook místo úpravy
   `www/index.php`. Opt-out v konfiguraci a přepis pozic menu.
5. **Admin bundle:** build admin JS a CSS v CI platformy, odstranit
   `assets/admin.js` a admin část webpacku z webů.
6. **Allowlist override:** zamknout `Admin/`. Web přestane přebíjet
   `Admin/@layout.latte`, jeho unikátní části přejdou do brandingu v konfiguraci.
7. **Semver a privátní composer zdroj.**
8. **Skeleton** až nakonec, když bude vidět, co zbylo ve webu.

## 7. Otevřené otázky

- **Jak a kde weby běží v produkci?** V `url-shortener` je `deployment.log`, ale
  není jasné, zda jde o shared hosting, VPS nebo Docker. Určuje to, jak se
  prebuilt admin bundle dostane do `www/`.
- **Má mít `clown/platform` povinně všechny addony, nebo jen core?** Návrh: core
  plus infrastrukturní addony (cache, revisions, front-extras), content addony
  si web vybírá.
- **Privátní repozitáře, nebo veřejná Packagist distribuce?** Rozhoduje o
  privátním composer zdroji.
- **Role a oprávnění per modul.** Nutné ověřit, zda je `User` entita podporuje.
