> **Kopie pro samostatnou práci v `clown-web`.** Tenhle dokument primárně
> žije v `collectorboy/docs/CMS_CORE_ARCHITECTURE.md` (tam, kde celá
> extrakce Clown CMS probíhala) a je sem zkopírovaný, aby relace pracující
> jen v tomhle repozitáři měla kompletní kontext bez nutnosti mít otevřený
> `collectorboy`. Při změně architektury aktualizovat primárně v
> `collectorboy` a sem dotáhnout kopii (žádný automatický sync mezi
> soubory neexistuje). Plný den-po-dni historický log celé extrakce je
> jen v `collectorboy/docs/archive/CMS_CORE_MIGRATION_PLAN.md`.

# Clown CMS — architektura a návod na stavbu nového webu

**Živý referenční web je tenhle repozitář, `clown-web`**
(`github.com/libormatejka/clown-web`) — minimální, ale plně funkční
sestavení všech 19 balíčků. `README.md` v rootu je návod "jak spustit a
rozšiřovat tenhle konkrétní web"; tenhle dokument je návod "jak postavit
*libovolný* nový web na stejném základu" (nebo jak dál rozšiřovat tenhle).

## Co je Clown CMS

Clown CMS je sada Nette/Doctrine balíčků (`libormatejka/clown-core` +
addony), vzniklá extrakcí z `collectorboy.cz`. Princip:

- **`clown/core`** = jádro (admin chrome, routing, registry rozšiřitelnosti,
  core entity User/MediaLibrary). Nezávisí na žádném jiném `clown/*`
  balíčku.
- **Addon** = samostatný balíček, který core rozšiřuje přes registry/tagy,
  nikdy ne přes hardcoded reference. Web si vybírá, které addony chce, jen
  zápisem do `composer.json` + `config/common.neon`.
- **Web (site repo)** = konkrétní instalace — `composer.json` s výběrem
  addonů, `config/` (DI, mapping, parametry), tenký `app/` (router, seed
  příkaz, případně vlastní presentery/šablony, které core/addon
  nepokrývají), `assets/` (frontend sestavení).

Žádná GraphQL knihovna nikde není — "resolver" je vlastní vzor (rozhraní +
implementace injektovaná do presenteru), ne GraphQL schema. `clown/generator`
(generátor request/filter DTO) je **site-specific nástroj, ne součást
Clown CMS** — `clown-web` ho vůbec nemá, per-content API/CRUD se tam dělá
přímými Doctrine dotazy (viz "Co zůstává vždy site-specific" níže).

## Katalog balíčků

Všechny repozitáře: `github.com/libormatejka/<balíček>`. PHP balíčky se
instalují přes composer (`composer.json` → `require` + `repositories` typu
`vcs`), npm asset balíčky jako `github:` git-based závislosti. Stejný
katalog, stručněji, je i v tomto repu v `README.md`.

### Core

| Balíček | Namespace | Závisí na | Co dává |
|---|---|---|---|
| `clown/core` | `Clown\Core\` | — | Admin chrome (presentery, komponenty, layouty), `AdminMenuRegistry`/`RouteRegistry`/`ContentProviderRegistry`/`TaggableRegistry`/`AdminEntityLinkRegistry`, `TemplateOverrideResolver`, auth extension point (`AuthenticatorPipeline`, `IOAuthLoginProvider`), core entity `User`/`MediaLibrary`, `Previewable`/`Loggable` marker rozhraní, `CoreRoutes::admin()`/`front()`. |

### Infrastrukturní addony (bez content entity)

| Balíček | Namespace | Závisí na | Co dává |
|---|---|---|---|
| `clown/cache` | `Clown\Cache\` | — | `FrontCacheService`/`IFrontCacheService`, `CacheKeys` — generická front cache infrastruktura, bez znalosti konkrétního obsahu. |
| `clown/revisions` | `Clown\Revisions\` | core | `RevisionInterface`, `AbstractRevisionSubscriber`, `RevisionDiffService`, admin komponenta `Revisions` — revizní mechanismus pro libovolnou entitu. |
| `clown/front-extras` | `Clown\FrontExtras\` | core | GTM (`GtmEntityProviderRegistry`), StructuredData (`SchemaProviderRegistry`), Shortcode engine (`ShortcodeHandlerRegistry` + 5 obecných handlerů), `SearchBox`. |
| `clown/api` | `Clown\Api\` | core | `BaseApiPresenter`, `ApiKey`/`ApiLog` entity, `ApiKeyAuthenticator`, admin log listing. Per-content API presentery (Article/Post/Review/...) **nejsou součástí** — viz níže. |
| `clown/webmcp` | `Clown\WebMcp\` | core | `BaseWebMcpPresenter`, `WebMcpLog` — MCP přes HTTP pro AI nástroje. Per-content presentery opět zůstávají na webu. |
| `clown/google-auth` | `Clown\GoogleAuth\` | core | `GoogleOAuthLoginProvider` (`IOAuthLoginProvider`) — Google OAuth přihlášení do administrace, vyžaduje reálné credentials z Google Cloud Console (viz `README.md`). |
| `clown/ai` | `Clown\Ai\` | core | `AiPresenter`, `GeminiApiService` — AI asistent v adminu (meta/perex, gramatika, popisky obrázků). |

### Obsahové addony

| Balíček | Namespace | Závisí na | Entity |
|---|---|---|---|
| `clown/taxonomy` | `Clown\Taxonomy\` | core | `Tag`, `Category` + generický admin CRUD. |
| `clown/gallery` | `Clown\Gallery\` | core, taxonomy | `Gallery`, `GalleryTag`, `GalleryMediaLibrary`. |
| `clown/redirect` | `Clown\Redirect\` | core | `Redirect` (301/302). |
| `clown/blocks` | `Clown\Blocks\` | core | `Block` (vlastní HTML/JS vkládaný přes URL/shortcode). |
| `clown/log` | `Clown\Log\` | core | `Log`, `LogType`, `EntityLogSubscriber`, `ManualLogService` — generický audit log (entity se připojují přes `Loggable`). |
| `clown/articles` | `Clown\Articles\` | core, taxonomy, gallery | `Article`, `ArticleTag`. |
| `clown/portfolio` | `Clown\Portfolio\` | core, taxonomy | `Portfolio`, `PortfolioTag`. |
| `clown/podcast` | `Clown\Podcast\` | core, taxonomy, gallery | `Podcast`, `PodcastTag`. |
| `clown/blog` | `Clown\Blog\` | core, taxonomy, gallery | `Blog`, `BlogTag`. |
| `clown/post` | `Clown\Post\` | core, taxonomy, gallery, revisions | `Post`, `PostTag`, `PostRevision`. |
| `clown/reviews` | `Clown\Reviews\` | core, taxonomy, gallery, revisions | `Review`, `ReviewTag`, `ReviewRevision`. |

### Frontend asset balíčky (npm, `github:` git-based závislost)

| Balíček | Co dává |
|---|---|
| `@libormatejka/clown-core-assets` | Tailwind theme/compat (bez site-specific `@source` cest), sdílené admin JS moduly (`AlertCloseButton`, `DataGridColChooser`, `GenerateSlug`, `MediaModal`, `SidebarMenuButton`, `StatusButtonLabelUpdater`, `TableList`, `UserMenuButton`, `TinymceEditor` — vč. všech shortcode toolbar tlačítek). |
| `@libormatejka/clown-gallery-assets` | `GalleryManager` (multi-select výběr médií do galerie, Create/Update Gallery formuláře). |
| `@libormatejka/clown-ai-assets` | `AIChat.js` — AI chat asistent v adminu. |
| `@libormatejka/clown-reviews-assets` | Review box hodnocení na detailu recenze (front). |

Balíčky bez vlastního npm assets (Redirect, Blocks, Taxonomy, Log, Articles,
Portfolio, Podcast, Blog, Post, Api, WebMcp, GoogleAuth) nemají front/admin
JS nad rámec toho, co dává `clown-core-assets` + generická admin CRUD
šablona.

## Extension points (jak addon rozšiřuje core/jiný addon)

Všechny jsou tag-based registry sestavované v `beforeCompile()` dané
`CompilerExtension` (nikdy v `loadConfiguration()` — v tu chvíli ještě
nejsou zaregistrované tagy ostatních extensions).

| Tag (konstanta v `ClownCoreExtension`) | Registry | K čemu |
|---|---|---|
| `clown.adminMenuItem` (`TAG_ADMIN_MENU_ITEM`) | `AdminMenuRegistry` | Položky do admin sidebaru. |
| `clown.route` (`TAG_ROUTE`) | `RouteRegistry` | Vlastní routy (`IRouteFactory::createRoute()`), skládají se před `CoreRoutes::front()` (ta má catch-all `<slug>/`). |
| `clown.contentProvider` (`TAG_CONTENT_PROVIDER`) | `ContentProviderRegistry` | Generický přístup k libovolné content entitě (`GenericContentProvider`) — používá `Front:Preview` pro dispatch podle typu. |
| `clown.taggable` (`TAG_TAGGABLE`) | `TaggableRegistry` | Stejné instance jako content provider, navíc pro počty použití štítku v `Admin:Tag`. |
| `clown.adminEntityLink` (`TAG_ADMIN_ENTITY_LINK`) | `AdminEntityLinkRegistry` | Mapování entity → admin route (`Admin:Log`'s "zobrazit" odkazy). |
| `clown.authenticator` (`TAG_AUTHENTICATOR`) | `AuthenticatorPipeline` | Vlastní autentikační metody (heslo, API klíč...). |
| `clown.oauthLoginProvider` (`TAG_OAUTH_LOGIN_PROVIDER`) | `OAuthLoginProviderRegistry` | OAuth providery (`clown/google-auth` se sem registruje). |

Addon-specific registry (žijí v tom addonu, ne v core, protože řeší
problém specifický jen pro ten addon):

| Tag (v daného addonu `*Extension`) | Registry | Balíček |
|---|---|---|
| `clown.galleryUsageChecker` | `GalleryUsageCheckerRegistry` | `clown/gallery` — "používá tuto galerii nějaký content typ?" před smazáním. |
| `clown.gtmEntityProvider` | `GtmEntityProviderRegistry` | `clown/front-extras` — GTM data podle presenteru. |
| `clown.schemaProvider` | `SchemaProviderRegistry` | `clown/front-extras` — StructuredData (JSON-LD) podle typu stránky. |
| `clown.shortcodeHandler` | `ShortcodeHandlerRegistry` | `clown/front-extras` — dispatch shortcode tagů v obsahu. |

Jednodušší vzor — **nullable autowired rozhraní bez registru**, pro
"nejvýš jedna implementace na webu": `IArticleCacheInvalidator`/
`IPostCacheInvalidator`/`IReviewCacheInvalidator` (v `clown/articles`,
`clown/post`, `clown/reviews`) — addon presenter si je injektuje jako
`?IXCacheInvalidator $x = null`, web implementuje svůj `XCacheProvider`
třídou `implements IXCacheInvalidator` a Nette ho autowire-uje bez další
DI registrace (je jediná implementace).

**Typovaný value object místo raw parametru** — když balíček potřebuje
skalární site-specific hodnotu (web title, media library dir, API pepper),
dostane tenký typovaný wrapper registrovaný v core/addonu (`WebTitle`,
`MediaLibraryDir`, `ApiPepper`, `ApiDebugMode`) místo `string`/`Settings`
objektu — autowirable odkudkoli bez per-konzument registrace.

## Jak postavit nový web na Clown CMS (nebo rozšířit tenhle)

1. **`composer.json`** — `require` vybraných `clown/*` balíčků + `repositories`
   blok typu `vcs` pro každý (viz `composer.json` v tomhle repu). Instalace
   vyžaduje GitHub token s přístupem k `libormatejka/clown-*` (privátní
   repa) — zapsat do `/root/.composer/auth.json` v PHP kontejneru (neukládá
   se trvale, po recreate kontejneru znovu).
2. **`config/common.neon`** — `extensions:` blok s DI extension třídou
   každého balíčku (`Clown\X\DI\ClownXExtension`), pak jejich konfigurační
   sekce. `clownCore.passwordAuthenticator: true` zapíná core's vlastní
   autentikátor (jinak musí web zaregistrovat svůj přes `clown.authenticator`).
3. **`config/doctrine.neon`** — pro každý balíček s entitou (ne všechny mají)
   jeden `mapping:` blok ukazující na `vendor/clown/<balíček>/src/Entity`
   (nettrine/orm neumí mapping cesty sbírat z DI extension sám, musí se
   psát ručně per balíček).
4. **Content providery** — pro každý content addon jeden záznam v
   `services:` (`GenericContentProvider(...)`, tagy `clown.contentProvider`
   + `clown.taggable`) — bez něj `Front:Preview` pro ten typ vrátí "nejsou
   na tomto webu nainstalované" a taxonomy admin ho nevidí.
5. **Router** — `App\Router\RouterFactory::createRouter()` skládá
   `CoreRoutes::admin()` + `RouteRegistry::createRouteList()` (addon routy)
   + `CoreRoutes::front()` (catch-all musí být poslední).
6. **Per-content API/WebMcp/GraphQL presentery** (pokud web potřebuje
   `/api/v1/articles/` apod.) — vlastní, přímo v `app/Module/Api/...`, viz
   sekce níže. `clown/api`/`clown/webmcp` je neposkytují.
7. **Frontend** — `@libormatejka/clown-core-assets` jako npm `github:`
   závislost (git token stejně jako composer, ale jako git `insteadOf`
   rewrite v node kontejneru, ne `auth.json`), webpack entry importuje
   sdílené JS moduly + `TinymceEditor.render()` + `MediaModal`/`GalleryManager`
   inicializaci podmíněně podle přítomnosti elementu v DOM (viz
   `assets/admin.js`).
8. **Seed** — vlastní `App\Model\Command\SeedCommand` (core nemá, protože
   neví, jaká site-specific data má web chtít).
9. **Smoke test** — `tools/smoke-test.py` — jednoduchý HTTP klient, loguje
   se a projde admin stránky, kontroluje i obsah odpovědi (Nette umí
   vrátit 200 s Tracy screenem při chybě v šabloně).

### Co zůstává vždy site-specific (nikdy v core/addonu)

- **Per-content GraphQL/API/WebMcp presentery** (Article/Post/Review/...) —
  `clown/api`/`clown/webmcp` neví nic o content addonech (to by byla
  obrácená závislost). Jde o přímé `EntityManagerInterface` dotazy (viz
  `app/Module/Api/{Article,Post,Review,Gallery}/*Presenter.php` v tomhle
  repu — nejmenší fungující příklad k okopírování pro další content typ).
- **Forwarding stub** pro každý addon presenter, protože Nette mapuje
  presenter → třídu per modul ("Admin"/"Front"/"Api"), ne per presenter:
  `final class XPresenter extends Clown\Y\Admin\XPresenter {}` v
  `app/Module/Admin/X/XPresenter.php`. Pokud presenter má raw
  (neautowirovatelný) konstruktorový parametr, web ho musí explicitně
  zaregistrovat jako službu se jménem argumentem — addon to za web udělat
  nemůže (viz `AiPresenter`/`GalleryPresenter` v `config/common.neon`).
- **Cache providery** (`ArticleCacheProvider` apod.) — addon jen definuje
  rozhraní, implementaci (pokud web cachuje) dodává web.
- **Branding** (StructuredData domain/social links/organization,
  `WEB.name`, GTM ID) — vždy DI parametr webu, nikdy baked v balíčku.

## Důležité provozní detaily (ne kódové, ale nutné znát)

- **GitHub token se nikam trvale neukládá** — composer (`/root/.composer/auth.json`
  v PHP kontejneru) i npm (`git config --global --add url.*.insteadOf` v
  node kontejneru) ho ztrácí po recreate kontejneru nebo nové relaci.
  Token pro composer lze v nouzi přečíst z PHP kontejneru a znovu použít
  pro node (stejný token, jiný mechanismus zápisu).
- **`temp/cache` musí být smazaná** po jakékoli změně DI konfigurace nebo
  přesunu entity mezi namespacy — Nette/Doctrine jinak tiše použije starou
  zkompilovanou metadata/kontejner (typicky zmatečná chyba, ne jasná shoda
  s příčinou).
- **`chmod -R a+rwX temp log`** po `rm -rf temp/cache` jako root uvnitř
  kontejneru — php-fpm běží jako `www-data`, bez toho 500 "permission
  denied" na zápis cache.
- **phpcs vyžaduje `--tab-width=4`** explicitně, jinak false-positive
  "line indented incorrectly" na každém tab-indented souboru.
- Balíčky žijí jako samostatné git repozitáře pod `github.com/libormatejka/
  clown-*`; lokální vývoj typicky přes composer/npm `path`/`vcs` repozitáře
  mountnuté do kontejnerů (viz `.docker/docker-compose.yml`).

## Kde hledat víc

- `README.md` (root tohoto repa) — přesné příkazy pro spuštění tohoto
  konkrétního webu (Docker, composer, npm, seed, smoke test, Google OAuth).
- `collectorboy/docs/archive/CMS_CORE_MIGRATION_PLAN.md` — plný historický
  log extrakce: proč je co kde (rozhodnutí, zvažované alternativy,
  objevené bugy, čísla testů u každého kroku). Jen v `collectorboy`
  repozitáři, konzultovat při nejasnosti "proč to je takhle".
