# Clown Web (core-only)

Tahle větev je úmyslně odstrojená na **jen `clown/core`** — žádné content addony, žádná
infrastruktura navíc. Je to výchozí stav pro stavbu webu znovu od nuly, bez balastu z
plné reference (ta je na `main`).

Podrobná architektura Clown CMS (extension points, co je vždy site-specific, provozní
detaily) je v [`docs/CMS_CORE_ARCHITECTURE.md`](docs/CMS_CORE_ARCHITECTURE.md).

Co je nainstalované:

| Balíček | Namespace | Co dává |
|---|---|---|
| `clown/core` | `Clown\Core\` | Admin chrome, routing core, registry rozšiřitelnosti (`AdminMenuRegistry`/`RouteRegistry`/`ContentProviderRegistry`/`TaggableRegistry`/`AdminEntityLinkRegistry`), `TemplateOverrideResolver`, auth extension point (DB login s brute-force ochranou), core entity `User`/`Page`/`Menu`/`MenuItem`/`MediaLibrary`. |

Obsah webu:

- veřejné stránky **O nás** (`/o-nas/`) a **O projektu** (`/o-projektu/`), front vzhled přes
  `app/templates/override/Front/`,
- hlavní menu s odkazy na obě stránky,
- administrace (`/admin/`): Dashboard, Stránky, Menu, Uživatelé, Profil — vše z core, bez
  jediného addonu,
- `Admin:Packages` (`/admin/packages/`) — read-only přehled nainstalovaných `clown/*`
  balíčků z `composer.lock` (teď bude jen `clown/core`).

## Spuštění

Potřebujete Docker a GitHub token s přístupem k privátnímu repozitáři `libormatejka/clown-core`.

```bash
cp config/local.neon.dist config/local.neon
make up
COMPOSER_AUTH='{"github-oauth":{"github.com":"<token>"}}' make install
make migrate
make seed        # vypíše přihlašovací údaje admina
make build
```

Web běží na <http://localhost:8081>, administrace na <http://localhost:8081/admin/>.

`make seed` přijímá volby `--email` a `--password` (bez hesla se vygeneruje náhodné).

## Test

```bash
make smoke EMAIL=admin@clown-web.local PASSWORD=<heslo>
```

Smoke test projde veřejné stránky, přihlásí se a načte admin stránky. Kontroluje i obsah odpovědi, protože
Nette při chybě v šabloně často vrací HTTP 200 s Tracy obrazovkou.

## Co patří do webu a co do jádra

Presentery (admin Login/Logout/Page/Menu/MenuItem/User/Dashboard/Profil, front Page/Náhled/Chyby),
jejich šablony, layouty, autentikátor a menu dodává `clown/core`. Web obsahuje jen:

- konfiguraci (`config/`), včetně `clownCore.passwordAuthenticator: true`,
- router (`app/Router/RouterFactory.php`) — `CoreRoutes::admin()`/`front()`, žádné addon routy,
- `AssetManifestResolver` (čte manifest webpacku),
- `PackageCatalogService`/`PackagesAdminMenuProvider` — diagnostická stránka v adminu,
- seed příkaz a webpack/Tailwind sestavení assetů.

Vzhled lze přepsat šablonami v `app/templates/override/` (např. `Admin/@layout.latte`, `Front/@layout.latte`,
`Admin/Page/update.latte`) nebo vlastním presenterem se stejným názvem (např. `App\Module\Front\Homepage\HomepagePresenter`).

## Jak přidat addon zpátky

1. `composer require clown/<addon>` + `repositories` blok ve `composer.json`.
2. `config/common.neon` — `extensions:` + konfigurační sekce (pokud addon nějaké parametry potřebuje).
3. `config/doctrine.neon` — nový `mapping:` blok (`vendor/clown/<addon>/src/Entity`, namespace `Clown\<Addon>\Entity`).
4. Pro content addon navíc `config/common.neon` `services:` — `GenericContentProvider('<Type>', '<Label>',
   Entity::class, TagEntity::class, '<previewRoutePrefix>', <hasGallery: bool>)` s tagy
   `[clown.contentProvider, clown.taggable]`.
5. Pokud addon má presenter s raw (neautowirovatelným) konstruktorovým parametrem (vzor `AiPresenter`),
   zaregistrovat forwarding stub jako explicitní službu v `services:` se jmenným argumentem.
6. `rm -rf temp/cache` + `make migrate` (nová entita = nová migrace) + `orm:validate-schema --skip-sync`.
7. `make smoke` pro kontrolu regrese.

Plný katalog všech balíčků (co dávají, na čem závisí) je na větvi `main` v README.
