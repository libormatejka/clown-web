#!/usr/bin/env python3
"""
Smoke test clown-webu: veřejné stránky, přihlášení do administrace a načtení admin stránek.

Použití: python3 tools/smoke-test.py <email> <heslo> [base-url]

Nette při chybě v šabloně často vrací HTTP 200 se zkrácenou odpovědí a Tracy obrazovkou,
proto se kromě stavového kódu kontroluje i obsah odpovědi.
"""
import http.cookiejar
import re
import sys
import urllib.error
import urllib.parse
import urllib.request

email, password = sys.argv[1], sys.argv[2]
base = sys.argv[3] if len(sys.argv) > 3 else 'http://localhost:8081'

opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
failures = []


def request(path, data=None):
    try:
        response = opener.open(urllib.request.Request(base + path, data=data))
        return response.status, response.geturl(), response.read().decode('utf-8', 'ignore')
    except urllib.error.HTTPError as error:
        return error.code, path, error.read().decode('utf-8', 'ignore')


def visible_text(html):
    html = re.sub(r'<(script|style).*?</\1>', '', html, flags=re.S)
    return re.sub(r'\s+', ' ', re.sub(r'<[^>]+>', ' ', html)).strip()


def check(path, expected_status=200, expected_text=None, expect_layout=False):
    status, url, html = request(path)
    problems = []
    if status != expected_status:
        problems.append('stav %d (čekáno %d)' % (status, expected_status))
    if expected_status == 200 and 'id="tracy-bs"' in html:
        problems.append('v odpovědi je Tracy chyba')
    if expected_text and expected_text not in visible_text(html):
        problems.append('chybí text "%s"' % expected_text)
    if expect_layout and expected_status == 200 and '<html' not in html:
        # Nette umí vrátit HTTP 200 jen s obsahovým fragmentem bez <html>/layoutu
        # (bez výjimky, beze stopy) — viz AdminPresenter::formatLayoutTemplateFiles
        # fallback v clown-core. Kontrola textu samotná tohle neodhalí.
        problems.append('chybí <html> (stránka se vyrenderovala bez layoutu)')
    print('%-4s %s -> %d %s' % ('FAIL' if problems else 'ok', path, status, '; '.join(problems)))
    if problems:
        failures.append(path)
    return url, html


check('/', 200, 'Clown Web')
check('/o-nas/', 200, 'O nás')
check('/o-projektu/', 200, 'O projektu')
check('/neexistuje/', 404)
check('/admin/', 200, 'Vítejte v administraci', expect_layout=True)

url, html = request('/admin/login/')[1:]
form = re.search(r'<form[^>]*>.*?</form>', html, re.S).group(0)
fields = {}
for tag in re.findall(r'<input[^>]*>', form):
    name = re.search(r'name="([^"]*)"', tag)
    value = re.search(r'value="([^"]*)"', tag)
    if name:
        fields[name.group(1)] = value.group(1) if value else ''
fields['email'], fields['password'] = email, password
parsed = urllib.parse.urlparse(url)
request(parsed.path + ('?' + parsed.query if parsed.query else ''), urllib.parse.urlencode(fields).encode())

check('/admin/', 200, 'Počet stránek', expect_layout=True)
check('/admin/page/', 200, 'Seznam stránek', expect_layout=True)
check('/admin/page/create', 200, 'Přidat stránku', expect_layout=True)
check('/admin/page/update/1', 200, 'Upravit stránku', expect_layout=True)
check('/admin/profile/', 200, email, expect_layout=True)
check('/preview/stranka/o-nas/', 200, 'Náhled stránky')
check('/admin/redirect/', 200, 'Přesměrování', expect_layout=True)
check('/admin/redirect/create', 200, 'Přidat přesměrování', expect_layout=True)
check('/admin/block/', 200, 'Seznam blocků', expect_layout=True)
check('/admin/block/create', 200, 'Přidat block', expect_layout=True)
check('/admin/tag/', 200, 'Seznam štítků', expect_layout=True)
check('/admin/tag/create', 200, 'Vytvořit štítek', expect_layout=True)
check('/admin/category/', 200, 'Seznam kategorií', expect_layout=True)
check('/admin/category/create', 200, 'Vytvořit kategorii', expect_layout=True)
check('/admin/gallery/', 200, 'Všechny galerie', expect_layout=True)
check('/admin/gallery/create', 200, 'Vytvořit galerii', expect_layout=True)
check('/admin/article/', 200, 'Seznam článků', expect_layout=True)
check('/admin/article/create', 200, 'Napsat článek', expect_layout=True)
check('/admin/portfolio/', 200, 'Výpis portfolií', expect_layout=True)
check('/admin/portfolio/create', 200, 'Vytvořit portfolio', expect_layout=True)

sys.exit(1 if failures else 0)
