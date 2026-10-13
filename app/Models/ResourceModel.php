<?php

namespace App\Models;

use CodeIgniter\Model;

class ResourceModel extends Model
{
    protected $table            = 'resources';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'title',
        'author',
        'genre',
        'description',
        'url',
        'content_text',
        'resource_type_id',
        'department_id',
        'thumbnail_url',
        'external_id',
        'provider',
        'embed_url',
        'metadata',
        'status',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes= true;

    /**
     * Validador estrito de URL contra protocolos perigosos (apenas http:// e https:// permitidos)
     */
    public static function isValidHttpUrl(?string $url): bool
    {
        if (empty($url)) {
            return false;
        }
        $trimmed = trim($url);
        if (!preg_match('/^https?:\/\//i', $trimmed)) {
            return false;
        }
        return (filter_var($trimmed, FILTER_VALIDATE_URL) !== false);
    }

    /**
     * Faz requisição HTTP rápida com cURL para capturar metadados remotos
     */
    public static function fetchHttpContent(string $url, int $timeout = 6): ?string
    {
        if (!function_exists('curl_init')) {
            $ctx = stream_context_create([
                'http' => [
                    'timeout' => $timeout,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                    'header' => "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\nAccept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7\r\n"
                ]
            ]);
            return @file_get_contents($url, false, $ctx) ?: null;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 4,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER     => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
                'Sec-Ch-Ua: "Chromium";v="124", "Google Chrome";v="124", "Not-A.Brand";v="99"',
                'Sec-Ch-Ua-Mobile: ?0',
                'Sec-Ch-Ua-Platform: "Windows"',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: none',
                'Sec-Fetch-User: ?1',
                'Upgrade-Insecure-Requests: 1'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 400 && $response) {
            return $response;
        }

        return null;
    }

    /**
     * Faz requisição e retorna array JSON
     */
    public static function fetchJson(string $url, int $timeout = 4): ?array
    {
        $raw = self::fetchHttpContent($url, $timeout);
        if (!$raw) return null;
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Converte slug de URL em título formatado com capitalização natural
     */
    public static function slugToTitle(string $slug): string
    {
        $slug = urldecode($slug);
        $slug = str_replace(['-', '_'], ' ', $slug);
        $words = explode(' ', $slug);
        $preps = ['de', 'da', 'do', 'das', 'dos', 'em', 'para', 'por', 'com', 'e', 'a', 'o', 'as', 'os', 'ao', 'aos', 'no', 'na', 'nos', 'nas', 'feat', 'ft'];
        $c = [];
        foreach ($words as $i => $w) {
            $lw = mb_strtolower($w, 'UTF-8');
            if ($i > 0 && in_array($lw, $preps)) {
                $c[] = $lw;
            } else {
                $c[] = mb_convert_case($w, MB_CASE_TITLE, 'UTF-8');
            }
        }
        return trim(implode(' ', $c));
    }

    /**
     * Tenta identificar o gênero musical com base em palavras-chave no texto/título/tags.
     * Caso não consiga identificar com certeza, retorna null (deixa em branco).
     */
    public static function detectGenre(?string ...$textSources): ?string
    {
        $combined = '';
        foreach ($textSources as $txt) {
            if (!empty($txt)) {
                $combined .= ' ' . $txt;
            }
        }
        if (trim($combined) === '') {
            return null;
        }

        $lower = mb_strtolower($combined, 'UTF-8');

        // 1. Gospel / Louvor / Worship
        if (preg_match('/\b(worship)\b/iu', $lower)) {
            return 'Worship';
        }
        if (preg_match('/\b(gospel|louvor|adoração|adoracao|hino|harpa cristã|evangélico|evangelico|cristã|cristao|advec|igreja|ministerio|ministério)\b/ui', $lower)) {
            return 'Gospel / Louvor';
        }

        // 2. Outros gêneros comuns se identificáveis
        if (preg_match('/\b(sertanejo|modão|modao)\b/ui', $lower)) {
            return 'Sertanejo';
        }
        if (preg_match('/\b(pop rock|rock)\b/ui', $lower)) {
            return 'Rock';
        }
        if (preg_match('/\b(pop)\b/ui', $lower)) {
            return 'Pop';
        }
        if (preg_match('/\b(mpb|bossa nova)\b/ui', $lower)) {
            return 'MPB';
        }
        if (preg_match('/\b(eletrônica|eletronica|electronic|remix|edm)\b/ui', $lower)) {
            return 'Eletrônica';
        }
        if (preg_match('/\b(rap|hip hop|trap)\b/ui', $lower)) {
            return 'Rap / Hip-Hop';
        }
        if (preg_match('/\b(reggae)\b/ui', $lower)) {
            return 'Reggae';
        }
        if (preg_match('/\b(forró|forro)\b/ui', $lower)) {
            return 'Forró';
        }
        if (preg_match('/\b(coral|instrumental|orquestra|acústico|acustico)\b/ui', $lower)) {
            return 'Instrumental / Coral';
        }

        // Caso não consiga identificar, retorna null (em branco)
        return null;
    }

    /**
     * Tenta extrair o autor/artista a partir do título quando no padrão "Artista - Título"
     */
    public static function extractAuthorFromTitle(?string $title): ?string
    {
        if (empty($title)) return null;
        if (preg_match('/^([^\-–—\|]{2,70})\s*[\-–—]\s*(.+)$/u', $title, $matches)) {
            $candidate = trim($matches[1]);
            if (!preg_match('/^(clipe|video|vídeo|audio|áudio|oficial|official|letra|cifra|aula|ouça|assista)\b/ui', $candidate)) {
                return $candidate;
            }
        }
        return null;
    }

    /**
     * Auto-Parser de URLs para identificação de Provedores (YouTube, Spotify, Cifra Club, Drive, PDF)
     * e extração de Títulos, Autores, Gêneros Musicais, Cifras/Letras, Thumbnails e URLs de Embed seguras
     */
    public static function parseUrl(string $url, int $resource_type_id = 1): array
    {
        $url = trim($url);
        $result = [
            'url'               => $url,
            'title'             => null,
            'author'            => null,
            'genre'             => null,
            'content_text'      => null,
            'suggested_type_id' => null,
            'provider'          => 'generic',
            'external_id'       => null,
            'thumbnail_url'     => null,
            'embed_url'         => null,
            'metadata'          => []
        ];

        if (empty($url) || !self::isValidHttpUrl($url)) {
            return $result;
        }

        // 1. YouTube Parser
        // Suporta: youtube.com/watch?v=ID, youtu.be/ID, youtube.com/embed/ID, youtube.com/shorts/ID, youtube.com/live/ID
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts|live)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $url, $matches)) {
            $youtubeId = $matches[1];
            $result['provider']          = 'youtube';
            $result['external_id']       = $youtubeId;
            $result['thumbnail_url']     = "https://img.youtube.com/vi/{$youtubeId}/hqdefault.jpg";
            $result['embed_url']         = "https://www.youtube.com/embed/{$youtubeId}?autoplay=0&rel=0";
            $result['suggested_type_id'] = 1; // Vídeo
            $result['metadata']          = [
                'platform' => 'YouTube',
                'video_id' => $youtubeId,
                'watch_url'=> "https://www.youtube.com/watch?v={$youtubeId}"
            ];

            // Tenta obter título oficial e canal via oEmbed público do YouTube
            $oembedUrl = "https://www.youtube.com/oembed?url=" . urlencode($url) . "&format=json";
            $oembedData = self::fetchJson($oembedUrl);
            if (!empty($oembedData['title'])) {
                $result['title'] = html_entity_decode($oembedData['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
            if (!empty($oembedData['author_name'])) {
                $channelAuthor = html_entity_decode($oembedData['author_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $channelAuthor = trim(preg_replace('/(?:\s*-\s*Topic|\s*Oficial|\s*Official|\s*VEVO)\s*$/i', '', $channelAuthor));
                $result['author'] = $channelAuthor;
                $result['metadata']['author'] = $channelAuthor;
            }

            // Se o título tiver o padrão "Artista - Nome da Música", refina o autor
            $titleAuthor = self::extractAuthorFromTitle($result['title']);
            if (!empty($titleAuthor)) {
                if (empty($result['author']) || mb_stripos($result['title'], $result['author']) !== false) {
                    $result['author'] = $titleAuthor;
                    $result['metadata']['author'] = $titleAuthor;
                }
            }

            // Tenta obter keywords da página do YouTube para identificar o gênero musical com precisão
            $youtubeKeywords = null;
            $html = self::fetchHttpContent($url, 3);
            if ($html && preg_match('/<meta name="keywords" content="([^"]+)"/i', $html, $km)) {
                $youtubeKeywords = html_entity_decode($km[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            // Gênero Musical (extrai automaticamente ou deixa null em branco)
            $result['genre'] = self::detectGenre($result['title'], $result['author'] ?? '', $youtubeKeywords ?? '');
            if (!empty($result['genre'])) {
                $result['metadata']['genre'] = $result['genre'];
            }

            return $result;
        }

        // 2. Spotify Parser
        // Suporta: open.spotify.com/track/ID, open.spotify.com/playlist/ID, open.spotify.com/album/ID, open.spotify.com/episode/ID
        if (preg_match('/open\.spotify\.com\/(track|playlist|album|episode|show)\/([a-zA-Z0-9]+)/i', $url, $matches)) {
            $spotifyType = strtolower($matches[1]);
            $spotifyId   = $matches[2];
            $result['provider']          = 'spotify';
            $result['external_id']       = $spotifyId;
            $result['thumbnail_url']     = function_exists('base_url') ? base_url('assets/img/spotify_badge.png') : '/assets/img/spotify_badge.png';
            $result['embed_url']         = "https://open.spotify.com/embed/{$spotifyType}/{$spotifyId}?utm_source=generator";
            $result['suggested_type_id'] = 2; // Áudio / Música
            $result['metadata']          = [
                'platform'     => 'Spotify',
                'spotify_type' => $spotifyType,
                'spotify_id'   => $spotifyId
            ];

            // Tenta obter título oficial via oEmbed público do Spotify
            $oembedUrl = "https://open.spotify.com/oembed?url=" . urlencode($url);
            $oembedData = self::fetchJson($oembedUrl);
            if (!empty($oembedData['title'])) {
                $cleanTitle = html_entity_decode($oembedData['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $result['title'] = $cleanTitle;
                if (!empty($oembedData['thumbnail_url'])) {
                    $result['thumbnail_url'] = $oembedData['thumbnail_url'];
                }

                if (preg_match('/(?:song\s+(?:and\s+lyrics\s+)?by|by)\s+([^\|]+?)(?:\s*\|\s*Spotify)?$/i', $cleanTitle, $am)) {
                    $result['author'] = trim($am[1]);
                    $songOnly = trim(preg_replace('/\s*-\s*(?:song\s+(?:and\s+lyrics\s+)?by|by)\s+.*$/i', '', $cleanTitle));
                    if (!empty($songOnly)) {
                        $result['title'] = $songOnly;
                    }
                }
            }

            // Se ainda não temos o autor, tenta via meta tags do HTML da página
            if (empty($result['author'])) {
                $html = self::fetchHttpContent($url);
                if ($html) {
                    if (preg_match('/<meta property="og:description" content="([^"]+)"/i', $html, $ogm)) {
                        $desc = html_entity_decode($ogm[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        $parts = explode('·', $desc);
                        if (count($parts) >= 2) {
                            $result['author'] = trim($parts[0]);
                        }
                    }
                }
            }

            if (!empty($result['author'])) {
                $result['metadata']['author'] = $result['author'];
            }

            $result['genre'] = self::detectGenre($result['title'], $result['author'] ?? '');
            if (!empty($result['genre'])) {
                $result['metadata']['genre'] = $result['genre'];
            }

            return $result;
        }

        // 3. Cifra Club Parser (Captura Título, Artista, Letra e Cifra da Música)
        if (preg_match('/cifraclub\.com\.br\/([^\/\?#]+)(?:\/([^\/\?#]+))?/i', $url, $matches)) {
            $result['provider']          = 'cifraclub';
            $result['suggested_type_id'] = 5; // Texto / Cifra / Letra
            $artistSlug = $matches[1] ?? '';
            $songSlug   = $matches[2] ?? '';

            // Tenta obter o HTML da página do Cifra Club
            $html = self::fetchHttpContent($url);
            $extractedTitle = null;
            $extractedCifra = null;

            if ($html) {
                // Título via <title>
                if (preg_match('/<title>(.*?)<\/title>/i', $html, $tm)) {
                    $rawTitle = html_entity_decode($tm[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    if (stripos($rawTitle, 'Access Denied') === false) {
                        $extractedTitle = trim(preg_replace('/(?:\s*-\s*|\s*\|\s*)Cifra\s*Club.*$/i', '', $rawTitle));
                    }
                }

                // Cifra via <pre>
                if (preg_match('/<pre[^>]*>(.*?)<\/pre>/is', $html, $pm)) {
                    $cifraRaw = $pm[1];
                    $cifraRaw = preg_replace('/<span class="tablatura">.*?<\/span>/is', '', $cifraRaw);
                    $cifraRaw = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $cifraRaw);
                    $cifraClean = strip_tags($cifraRaw);
                    $cifraClean = html_entity_decode($cifraClean, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $extractedCifra = trim($cifraClean);
                }

                // Gênero no estilo da página
                if (preg_match('/\/estilo\/([^\/"]+)\//i', $html, $gm)) {
                    $result['genre'] = self::slugToTitle($gm[1]);
                }
            }

            // Fallback inteligente pelo slug da URL se a requisição remota foi bloqueada
            $artistName = !empty($artistSlug) ? self::slugToTitle($artistSlug) : null;
            if (empty($extractedTitle) && !empty($artistName)) {
                if (!empty($songSlug)) {
                    $songName = self::slugToTitle($songSlug);
                    $extractedTitle = "{$songName} - {$artistName}";
                } else {
                    $extractedTitle = "Cifras de {$artistName}";
                }
            }

            $result['title']        = $extractedTitle;
            $result['author']       = $artistName;
            $result['content_text'] = $extractedCifra;

            if (empty($result['genre'])) {
                $result['genre'] = self::detectGenre($extractedTitle, $artistName ?? '');
            }

            $result['metadata']     = [
                'platform' => 'Cifra Club',
                'artist'   => $artistName,
                'genre'    => $result['genre'],
                'song'     => $songSlug
            ];

            return $result;
        }

        // 4. Google Drive / Docs Parser
        if (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/i', $url, $matches)) {
            $driveId = $matches[1];
            $result['provider']          = 'drive';
            $result['external_id']       = $driveId;
            $result['embed_url']         = "https://drive.google.com/file/d/{$driveId}/preview";
            $result['thumbnail_url']     = "https://drive-thirdparty.googleusercontent.com/16/type/video/mp4";
            $result['suggested_type_id'] = 4; // Link
            $result['metadata']          = ['platform' => 'Google Drive', 'file_id' => $driveId];

            $html = self::fetchHttpContent($url);
            if ($html && preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $ogm)) {
                $result['title'] = html_entity_decode($ogm[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            } elseif ($html && preg_match('/<title>(.*?)<\/title>/i', $html, $tm)) {
                $rawTitle = html_entity_decode($tm[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $result['title'] = trim(str_replace('- Google Drive', '', $rawTitle));
            }

            $result['author'] = self::extractAuthorFromTitle($result['title']);
            $result['genre']  = self::detectGenre($result['title']);
            return $result;
        }

        // 5. PDF Direct Link
        if (preg_match('/\.pdf($|\?)/i', $url)) {
            $result['provider']          = 'pdf';
            $result['embed_url']         = $url;
            $result['suggested_type_id'] = 3; // PDF
            $filename = basename(parse_url($url, PHP_URL_PATH));
            $filenameClean = urldecode(preg_replace('/\.pdf$/i', '', $filename));
            $result['title']             = self::slugToTitle($filenameClean);
            $result['author']            = self::extractAuthorFromTitle($result['title']);
            $result['genre']             = self::detectGenre($result['title']);
            $result['metadata']          = ['platform' => 'PDF Document', 'format' => 'pdf'];
            return $result;
        }

        // 6. Link Genérico (Tenta capturar <title> ou og:title)
        $result['provider']  = 'generic';
        $result['embed_url'] = null; // Links genéricos abrem em nova aba
        $result['metadata']  = ['platform' => 'External Web'];

        $html = self::fetchHttpContent($url);
        if ($html) {
            if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $ogm)) {
                $result['title'] = trim(html_entity_decode($ogm[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            } elseif (preg_match('/<title>(.*?)<\/title>/i', $html, $tm)) {
                $rawTitle = trim(html_entity_decode($tm[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                $result['title'] = preg_replace('/\s+/', ' ', $rawTitle);
            }

            if (preg_match('/<meta (?:name|property)="(?:author|article:author)" content="([^"]+)"/i', $html, $am)) {
                $result['author'] = trim(html_entity_decode($am[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            }
        }

        if (empty($result['author']) && !empty($result['title'])) {
            $result['author'] = self::extractAuthorFromTitle($result['title']);
        }

        $result['genre'] = self::detectGenre($result['title'], $result['author'] ?? '');

        return $result;
    }

    /**
     * Lista recursos com filtros por departamento, tipo, busca de texto e paginação
     */
    public function listarRecursos(array $filtros = []): array
    {
        $db = db_connect();
        $builder = $db->table('resources as r');
        $builder->select('
            r.*,
            rt.name as type_name,
            rt.code as type_code,
            rt.icon as type_icon,
            rt.color as type_color,
            d.nome as department_name,
            d.cor_identificacao as department_color
        ');
        $builder->join('resource_types as rt', 'rt.id = r.resource_type_id', 'inner');
        $builder->join('tb_departamento as d', 'd.id_departamento = r.department_id', 'left');
        $builder->where('r.deleted_at', null);

        if (isset($filtros['status']) && $filtros['status'] !== '') {
            $builder->where('r.status', (int)$filtros['status']);
        }

        if (!empty($filtros['resource_type_id'])) {
            $builder->where('r.resource_type_id', (int)$filtros['resource_type_id']);
        }

        if (isset($filtros['department_or_global']) && $filtros['department_or_global'] !== null) {
            $depId = (int)$filtros['department_or_global'];
            $builder->groupStart()
                ->where('r.department_id', null)
                ->orWhere('r.department_id', $depId)
                ->groupEnd();
        } elseif (isset($filtros['department_id']) && $filtros['department_id'] !== null && $filtros['department_id'] !== '') {
            if ($filtros['department_id'] === 'global' || $filtros['department_id'] === '0') {
                $builder->where('r.department_id', null);
            } else {
                $builder->where('r.department_id', (int)$filtros['department_id']);
            }
        } elseif (isset($filtros['departamentos_permitidos'])) {
            $allowedIds = $filtros['departamentos_permitidos'];
            $builder->groupStart();
            $builder->where('r.department_id', null);
            if (!empty($allowedIds)) {
                $builder->orWhereIn('r.department_id', $allowedIds);
            }
            $builder->groupEnd();
        }

        if (!empty($filtros['busca'])) {
            $termo = trim($filtros['busca']);
            $builder->groupStart()
                ->like('r.title', $termo)
                ->orLike('r.author', $termo)
                ->orLike('r.genre', $termo)
                ->orLike('r.description', $termo)
                ->orLike('r.url', $termo)
                ->orLike('r.content_text', $termo)
                ->groupEnd();
        }

        $builder->orderBy('r.id', 'DESC');

        if (!empty($filtros['limit'])) {
            $offset = !empty($filtros['offset']) ? (int)$filtros['offset'] : 0;
            $builder->limit((int)$filtros['limit'], $offset);
        }

        return $builder->get()->getResult('object');
    }

    /**
     * Busca um recurso completo por ID
     */
    public function buscarPorId(int $id)
    {
        $db = db_connect();
        $builder = $db->table('resources as r');
        $builder->select('
            r.*,
            rt.name as type_name,
            rt.code as type_code,
            rt.icon as type_icon,
            rt.color as type_color,
            d.nome as department_name,
            d.cor_identificacao as department_color
        ');
        $builder->join('resource_types as rt', 'rt.id = r.resource_type_id', 'inner');
        $builder->join('tb_departamento as d', 'd.id_departamento = r.department_id', 'left');
        $builder->where('r.id', $id);
        $builder->where('r.deleted_at', null);

        return $builder->get()->getFirstRow('object');
    }
}
