# Clown Web

Referenční web postavený na [Clown CMS](https://github.com/libormatejka/clown-core)
(`clown/core` + addon balíčky). Slouží jako živý příklad, jak se Clown CMS
sestavuje, a jako základ pro stavbu dalších webů — klonovat tento repozitář
a škálovat (přidat/odebrat balíčky, přepsat šablony) je rychlejší než
začínat od nuly.

Podrobná architektura Clown CMS (extension points, co je vždy
site-specific, provozní detaily) je v [`docs/CMS_CORE_ARCHITECTURE.md`](docs/CMS_CORE_ARCHITECTURE.md)
přímo v tomhle repu (kopie z `collectorboy`, kde celá extrakce probíhala) —
tenhle README je jen "jak spustit a rozšiřovat tenhle konkrétní web".

Obsah webu:

- veřejné stránky **O nás** (`/o-nas/`) a **O projektu** (`/o-projektu/`),
- hlavní menu s odkazy na obě stránky,
- administrace (`/admin/`) se všemi 19 balíčky zapojenými (core + 18
  addonů, viz katalog níže).

## Katalog balíčků

Všechny `clown/*` repozitáře jsou pod `github.com/libormatejka/clown-*`
(privátní). Instalují se jako composer `vcs` repozitáře (viz
`composer.json`) nebo jako `github:` npm závislosti (asset balíčky).

### Core

| Balíček | Namespace | Co dává |
|---|---|---|
| `clown/core` | `Clown\Core\` | Admin chrome, routing core, registry rozšiřitelnosti (`AdminMenuRegistry`/`RouteRegistry`/`ContentProviderRegistry`/`TaggableRegistry`/`AdminEntityLinkRegistry`), `TemplateOverrideResolver`, auth extension point, core entity `User`/`MediaLibrary`. |

### Infrastrukturní addony (bez content entity)

| Balíček | Namespace | Závisí na | Co dává |
|---|---|---|---|
| `clown/cache` | `Clown\Cache\` | — | `FrontCacheService`, `CacheKeys`. |
| `clown/revisions` | `Clown\Revisions\` | core | Revizní mechanismus (`RevisionInterface`, `AbstractRevisionSubscriber`, admin komponenta `Revisions`). |
| `clown/front-extras` | `Clown\FrontExtras\` | core | GTM, StructuredData, Shortcode engine, `SearchBox`. |
| `clown/api` | `Clown\Api\` | core | `BaseApiPresenter`, `ApiKey`/`ApiLog`, admin log listing. Per-content API presentery dodává web, viz níže. |
| `clown/webmcp` | `Clown\WebMcp\` | core | `BaseWebMcpPresenter`, `WebMcpLog` — MCP přes HTTP. |
| `clown/google-auth` | `Clown\GoogleAuth\` | core | Google OAuth přihlášení (`GoogleOAuthLoginProvider`) — vyžaduje reálné credentials, viz "Google OAuth" níže. |
| `clown/ai` | `Clown\Ai\` | core | AI asistent v adminu (`AiPresenter`, `GeminiApiService`, přes Gemini API). |

### Obsahové addony

| Balíček | Namespace | Závisí na | Entity |
|---|---|---|---|
| `clown/taxonomy` | `Clown\Taxonomy\` | core | `Tag`, `Category`. |
| `clown/gallery` | `Clown\Gallery\` | core, taxonomy | `Gallery`, `GalleryTag`, `GalleryMediaLibrary`. |
| `clown/redirect` | `Clown\Redirect\` | core | `Redirect`. |
| `clown/blocks` | `Clown\Blocks\` | core | `Block`. |
| `clown/log` | `Clown\Log\` | core | `Log`, `LogType`, `EntityLogSubscriber`. |
| `clown/articles` | `Clown\Articles\` | core, taxonomy, gallery | `Article`, `ArticleTag`. |
| `clown/portfolio` | `Clown\Portfolio\` | core, taxonomy | `Portfolio`, `PortfolioTag`. |
| `clown/podcast` | `Clown\Podcast\` | core, taxonomy, gallery | `Podcast`, `PodcastTag`. |
| `clown/blog` | `Clown\Blog\` | core, taxonomy, gallery | `Blog`, `BlogTag`. |
| `clown/post` | `Clown\Post\` | core, taxonomy, gallery, revisions | `Post`, `PostTag`, `PostRevision`. |
| `clown/reviews` | `Clown\Reviews\` | core, taxonomy, gallery, revisions | `Review`, `ReviewTag`, `ReviewRevision`. |

### Frontend asset balíčky (npm)

| Balíček | Co dává |
|---|---|
| `@libormatejka/clown-core-assets` | Tailwind theme/compat, sdílené admin JS (`MediaModal`, `TinymceEditor` vč. shortcode tlačítek, `DataGridColChooser`, ...). |
| `@libormatejka/clown-gallery-assets` | `GalleryManager` (výběr médií do galerie). |
| `@libormatejka/clown-ai-assets` | `AIChat.js` — AI chat asistent v adminu. |
| `@libormatejka/clown-reviews-assets` | Review box hodnocení na detailu recenze. |

## Spuštění

Potřebujete Docker a GitHub token s přístupem k privátním repozitářům `libormatejka/clown-*`.

```bash
cp config/local.neon.dist config/local.neon
make up
COMPOSER_AUTH='{"github-oauth":{"github.com":"<token>"}}' make install
make migrate
make seed        # vypíše přihlašovací údaje admina
```

Npm balíčky `@libormatejka/clown-*-assets` jsou privátní git závislosti. V Node kontejneru je před `npm install`
potřeba dočasně nastavit přepis git URL na HTTPS s tokenem (nic se netrvale neukládá):

```bash
docker-compose --project-name clownweb -f .docker/docker-compose.yml exec -T -e GH_TOKEN=<token> node sh -c '
  for from in "https://github.com/" "ssh://git@github.com/" "git@github.com:"; do
    git config --global --add url."https://x-access-token:$GH_TOKEN@github.com/".insteadOf "$from"
  done'
docker-compose --project-name clownweb -f .docker/docker-compose.yml exec -T node npm install
make build
```

Web běží na <http://localhost:8081>, administrace na <http://localhost:8081/admin/>.

`make seed` přijímá volby `--email` a `--password` (bez hesla se vygeneruje náhodné).

## Google OAuth (volitelné)

`config/common.neon` má `clownGoogleAuth.*` jako `%google.*%` parametry s
placeholdery. Pro reálné přihlášení přes Google:

1. Google Cloud Console → vytvořit projekt, OAuth consent screen (stačí
   "Testing" status pro pár testovacích účtů).
2. Credentials → Create OAuth client ID, typ "Web application",
   Authorized redirect URI přesně podle `redirectUri` níže.
3. Do `config/local.neon` (gitignored) doplnit reálné hodnoty:

```neon
parameters:
	google:
		clientId: <client-id-z-google>
		clientSecret: <client-secret-z-google>
		redirectUri: http://localhost:8081/admin/login/google-callback
```

Uživatel musí mít v DB nastavené `isGoogleLoginEnabled()` a odpovídající
e-mail, jinak `GoogleOAuthLoginProvider` přihlášení odmítne.

## Test

```bash
make smoke EMAIL=admin@clown-web.local PASSWORD=<heslo>
```

Smoke test projde veřejné stránky, přihlásí se a načte admin stránky. Kontroluje i obsah odpovědi, protože
Nette při chybě v šabloně často vrací HTTP 200 s Tracy obrazovkou.

## Co patří do webu a co do jádra/addonu

Presentery (admin Login/Logout/Page/Dashboard/Profil, front Page/Náhled/Chyby), jejich šablony, layouty,
autentikátor a menu dodává `clown/core`; content CRUD (Article/Post/Review/...) dodávají příslušné addony.
Web obsahuje jen:

- konfiguraci (`config/`), včetně `clownCore.passwordAuthenticator: true`,
- router (`app/Router`) složený z `CoreRoutes::admin()`/`front()` + addon routy (`clown.route`) + vlastní site-specific routy,
- `AssetManifestResolver` (čte manifest webpacku),
- **per-content API presentery** (`app/Module/Api/{Article,Post,Review,Gallery}`) — žádný balíček neví o content
  addonech, takže endpointy pro TinyMCE shortcode tlačítka (`/api/v1/articles/` apod.) musí žít tady; přímé
  Doctrine dotazy, bez GraphQL/generátorové vrstvy,
- seed příkaz a webpack/Tailwind sestavení assetů.

Vzhled lze přepsat šablonami v `app/templates/override/` (např. `Admin/@layout.latte`, `Front/@layout.latte`,
`Admin/Page/update.latte`) nebo vlastním presenterem se stejným názvem (např. `App\Module\Front\Homepage\HomepagePresenter`).

## Jak přidat další content addon

1. `composer require clown/<addon>` + `repositories` blok ve `composer.json` (podle vzoru existujících).
2. `config/common.neon` — `extensions:` + konfigurační sekce (pokud addon nějaké parametry potřebuje).
3. `config/doctrine.neon` — nový `mapping:` blok (`vendor/clown/<addon>/src/Entity`, namespace `Clown\<Addon>\Entity`).
4. `config/common.neon` `services:` — `GenericContentProvider('<Type>', '<Label>', Entity::class, TagEntity::class,
   '<previewRoutePrefix>', <hasGallery: bool>)` s tagy `[clown.contentProvider, clown.taggable]`, jinak `Front:Preview`
   pro ten typ vrátí "nejsou na tomto webu nainstalované" a Taxonomy admin ho nevidí v počtech.
5. Pokud addon má presenter s raw (neautowirovatelným) konstruktorovým parametrem (vzor `AiPresenter`/`GalleryPresenter`),
   zaregistrovat forwarding stub jako explicitní službu v `services:` se jmenným argumentem.
6. `rm -rf temp/cache` + `make migrate` (nová entita = nová migrace) + `orm:validate-schema --skip-sync`.
7. `make smoke` pro kontrolu regrese.

Nejmenší fungující příklad per-content API presenteru (bod "co patří do webu" výše) je
`app/Module/Api/Article/ArticlePresenter.php` — zkopírovat a upravit pro další typ.
