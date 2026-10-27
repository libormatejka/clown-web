# Clown Web

Minimální ukázkový web postavený na jádře [Clown CMS](https://github.com/libormatejka/clown-core) (`clown/core`).
Slouží jako test přenositelnosti jádra (Fáze 7 Krok B v plánu migrace v repozitáři `collectorboy`).

Obsah webu:

- veřejné stránky **O nás** (`/o-nas/`) a **O projektu** (`/o-projektu/`),
- hlavní menu s odkazy na obě stránky,
- administrace (`/admin/`) s jedním uživatelem; správa stránek je z `clown/core`.

## Spuštění

Potřebujete Docker a GitHub token s přístupem k privátním repozitářům `libormatejka/clown-*`.

```bash
cp config/local.neon.dist config/local.neon
make up
COMPOSER_AUTH='{"github-oauth":{"github.com":"<token>"}}' make install
make migrate
make seed        # vypíše přihlašovací údaje admina
```

Npm balíček `@libormatejka/clown-core-assets` je privátní git závislost. V Node kontejneru je před `npm install`
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

## Test

```bash
make smoke EMAIL=admin@clown-web.local PASSWORD=<heslo>
```

Smoke test projde veřejné stránky, přihlásí se a načte admin stránky. Kontroluje i obsah odpovědi, protože
Nette při chybě v šabloně často vrací HTTP 200 s Tracy obrazovkou.

## Co patří do webu a co do jádra

Do tohoto repozitáře patří jen „lepidlo“: router, autentikátor, forwarding stub presentery, layouty, seed.
Všechno, co bylo potřeba doplnit navíc proti `clown/core`, je zapsané v `docs/CMS_CORE_MIGRATION_PLAN.md`
v repozitáři `collectorboy` (sekce Fáze 7 Krok B).
