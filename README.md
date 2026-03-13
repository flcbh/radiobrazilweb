# Radio Brazil Web — Site Oficial

Site estático de rádio gospel hospedado em [radiobrazilweb.com](https://www.radiobrazilweb.com).

---

## Estrutura do Repositório

```
radiobrazilweb/
├── index.html      # Site completo (HTML + CSS + JS tudo em um arquivo)
├── nowplaying.php  # Proxy server-side para metadados do ShoutCast
├── contact.php     # Handler de formulário de contato (PHP mail())
└── README.md       # Este arquivo
```

---

## Hospedagem & Infraestrutura

| Item | Detalhe |
|---|---|
| **Hosting** | cPanel shared hosting — `oxley.hostns.io` |
| **cPanel user** | `radiobra` |
| **cPanel URL** | `https://oxley.hostns.io:2083` |
| **Document root** | `/home/radiobra/public_html/` |
| **Domínio** | `radiobrazilweb.com` / `www.radiobrazilweb.com` |
| **Email** | `info@radiobrazilweb.com` |

---

## Stream de Áudio

| Item | Detalhe |
|---|---|
| **URL do stream** | `https://stream.radiobrazilweb.com:8757/stream` |
| **Servidor** | ShoutCast 2 |
| **Bitrate** | 256 kbps |
| **Protocolo metadados** | `/7.html` (CSV), `/currentsong`, `/status-json.xsl` |

---

## Deploy

### Método principal (via cPanel UAPI)

Abrir o tab do cPanel File Manager (`oxley.hostns.io:2083`) e executar no console do browser:

```javascript
(async () => {
  const raw = await fetch('https://raw.githubusercontent.com/flcbh/radiobrazilweb/main/index.html', {cache:'no-store'}).then(r=>r.text());
  const res = await fetch('/cpsessXXXXXXXXXX/execute/Fileman/save_file_content', {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'cpanel_jsonapi_version=2&dir=%2Fhome%2Fradiobra%2Fpublic_html&file=index.html&content='+encodeURIComponent(raw)
  }).then(r=>r.json());
  console.log(res.status); // 1 = sucesso
})();
```

> **Nota:** Substituir `cpsessXXXXXXXXXX` pelo token de sessão atual da URL do cPanel.
> O token expira com a sessão — fazer login no cPanel antes.

### Git

```bash
# Branch principal
git remote    # origin → https://github.com/flcbh/radiobrazilweb.git
git branch    # main (não master)

# Fluxo de trabalho
git add index.html
git commit -m "descrição da mudança"
git push origin main
```

---

## Arquivos PHP

### `nowplaying.php`
Proxy server-side para buscar o nome da música atual do ShoutCast.
Evita erro de CORS ao chamar o stream diretamente do browser.

- **Endpoint chamado pelo JS:** `GET /nowplaying.php`
- **Resposta:** `{"song": "Artista - Título"}` ou `{"song": ""}` se o stream estiver offline
- **Fallbacks:** `/7.html` → `/currentsong` → `/status-json.xsl` → HTTP fallback
- **Timeouts cURL:** connect 4s / transfer 6s

### `contact.php`
Handler do formulário "Fale Conosco".

- **Método:** `POST /contact.php`
- **Campos:** `name`, `email`, `subject`, `message`
- **Destino do email:** `info@radiobrazilweb.com`
- **Resposta JSON:** `{"ok": true}` ou `{"ok": false, "error": "..."}`
- **Usa:** PHP `mail()` — requer que o servidor suporte envio de email

---

## Seções do Site

| Seção | ID / Âncora | Descrição |
|---|---|---|
| Hero | `#hero` | Capa com player e CTA |
| Player | `#player` | Player ao vivo embutido |
| Versículo | `.verse-section` | Versículo bíblico do dia |
| Apps | `#listen-on` | Links para ouvir em todos os apps |
| Oração | `#prayer-section` | Seção devocional |
| Programação | `#schedule` | Grade de programação |
| DJs | `#djs` | Perfis da equipe |
| Request | `#request` | Pedido de música |
| Artigos | `#articles` | Blog / artigos de fé |
| Contato | `#contact-section` | Formulário de contato por email |
| Footer | `#contact` | Rodapé com links e redes sociais |

---

## Logo

Os `<img data-logo="1" src="">` aguardam um arquivo de logo.
Para adicionar: enviar o arquivo (ex: `logo.png`) para `/home/radiobra/public_html/` e atualizar os `src` no `index.html`.

---

## Dependências externas (CDN)

- **Fontes:** Google Fonts — Playfair Display, Inter
- **Fotos:** Unsplash CDN (`images.unsplash.com`)
- **Stream:** `stream.radiobrazilweb.com:8757`

---

## Última atualização
Março 2026
