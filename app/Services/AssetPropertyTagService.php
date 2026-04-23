<?php

namespace App\Services;

use Illuminate\Support\Collection;

class AssetPropertyTagService
{
    private const COMPANY_NAME = 'STRONGLINK SERVICES';

    public static function generate(Collection $assets): string
    {
        $tags = $assets->map(fn ($asset) => [
            'property_tag' => $asset->property_tag ?? '—',
            'name'         => $asset->name ?? '',
            'category'     => $asset->category?->name ?? '',
            'location'     => $asset->location ?? '',
        ])->all();

        return self::wrapDocument($tags);
    }

    private static function renderTag(array $tag): string
    {
        $propertyTag = e($tag['property_tag']);
        $name        = e($tag['name']);
        $category    = e($tag['category']) ?: '—';
        $location    = e($tag['location']);
        $cn          = e(self::COMPANY_NAME);

        return <<<HTML
        <div class="tag">
            <div class="tag-header">
                <div class="tag-prop-of">PROPERTY OF</div>
                <div class="tag-brand-row">
                    <span class="tag-company">{$cn}</span>
                    <span class="tag-cat-pill">{$category}</span>
                </div>
            </div>
            <div class="tag-blue-bar"></div>
            <div class="tag-body">
                <div class="tag-number">{$propertyTag}</div>
            </div>
            <div class="tag-footer">
                <span class="tag-name">{$name}</span>
                <span class="tag-loc">{$location}</span>
            </div>
        </div>
        HTML;
    }

    public static function wrapDocument(array $tags): string
    {
        $count    = count($tags);
        $genTime  = now()->timezone('Asia/Manila')->format('M d, Y h:i A');
        $tagsHtml = collect($tags)->map(fn ($tag) => self::renderTag($tag))->join('');

        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Property Tags — {$count} item(s)</title>
            <style>
                *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

                body {
                    font-family: 'Segoe UI', Arial, sans-serif;
                    background: #dde3ea;
                    color: #111827;
                }

                /* ── Toolbar ── */
                .toolbar {
                    position: fixed;
                    top: 0; left: 0; right: 0;
                    z-index: 9999;
                    background: #0f1f3d;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 10px 24px;
                    box-shadow: 0 2px 12px rgba(0,0,0,.4);
                }
                .tbar-l { display: flex; align-items: center; gap: 14px; }
                .tbar-title { font-size: 14px; font-weight: 800; color: #fff; letter-spacing: .02em; }
                .tbar-sub { font-size: 10px; color: #64748b; margin-top: 1px; }
                .tbar-badge {
                    background: #1d4ed8; color: #fff;
                    font-size: 11px; font-weight: 700;
                    padding: 3px 12px; border-radius: 999px;
                }
                .tbar-r { display: flex; gap: 8px; }
                .btn {
                    display: inline-flex; align-items: center; gap: 6px;
                    padding: 8px 18px; border-radius: 6px;
                    font-size: 12px; font-weight: 700;
                    cursor: pointer; border: none; transition: opacity .15s;
                }
                .btn:hover { opacity: .85; }
                .btn-blue { background: #1d4ed8; color: #fff; }
                .btn-ghost {
                    background: rgba(255,255,255,.08);
                    color: #e2e8f0;
                    border: 1px solid rgba(255,255,255,.15);
                }

                /* ── Page wrapper ── */
                .pages {
                    padding: 72px 24px 40px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 24px;
                }

                /* ── A4 sheet ── */
                .a4 {
                    width: 210mm;
                    min-height: 297mm;
                    background: #fff;
                    box-shadow: 0 6px 24px rgba(0,0,0,.18);
                    border-radius: 3px;
                    padding: 5mm 5mm;
                    display: flex;
                    flex-wrap: wrap;
                    gap: 2mm;
                    align-content: flex-start;
                }

                /* ── Tag: 1.5in × 0.5in ── */
                .tag {
                    width: 1.5in;
                    height: 0.5in;
                    border-radius: 2px;
                    border: 1px solid #0f1f3d;
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                    background: #0f1f3d;
                    page-break-inside: avoid;
                    break-inside: avoid;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                /* Dark navy header */
                .tag-header {
                    background: #0f1f3d;
                    padding: 2px 4px 1px 4px;
                    display: flex;
                    flex-direction: column;
                    gap: 0px;
                    flex-shrink: 0;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .tag-prop-of {
                    font-size: 3.5pt;
                    font-weight: 700;
                    color: #94a3b8;
                    text-transform: uppercase;
                    letter-spacing: .1em;
                    line-height: 1;
                }

                .tag-brand-row {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 3px;
                }

                .tag-company {
                    font-size: 4.5pt;
                    font-weight: 900;
                    color: #ffffff;
                    text-transform: uppercase;
                    letter-spacing: .04em;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    line-height: 1;
                }

                .tag-cat-pill {
                    font-size: 2.5pt;
                    font-weight: 800;
                    color: #0f1f3d;
                    background: #cbd5e1;
                    padding: 1px 3px;
                    border-radius: 1px;
                    text-transform: uppercase;
                    letter-spacing: .04em;
                    white-space: nowrap;
                    flex-shrink: 0;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                /* Blue accent bar */
                .tag-blue-bar {
                    height: 2px;
                    background: #1d4ed8;
                    flex-shrink: 0;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                /* White body — tag number */
                .tag-body {
                    background: #ffffff;
                    flex: 1;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 0 4px;
                    min-height: 0;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .tag-number {
                    font-size: 11pt;
                    font-weight: 900;
                    color: #0f1f3d;
                    letter-spacing: .03em;
                    line-height: 1;
                    text-align: center;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    width: 100%;
                }

                /* Light footer */
                .tag-footer {
                    background: #f1f5f9;
                    border-top: 1px solid #e2e8f0;
                    padding: 1px 4px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    flex-shrink: 0;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .tag-name {
                    font-size: 3.5pt;
                    font-weight: 700;
                    color: #334155;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    max-width: 62%;
                    text-transform: uppercase;
                    letter-spacing: .03em;
                }

                .tag-loc {
                    font-size: 3pt;
                    color: #94a3b8;
                    white-space: nowrap;
                    text-align: right;
                    max-width: 36%;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }

                /* ── Print ── */
                @media print {
                    @page {
                        size: A4 portrait;
                        margin: 5mm 6mm;
                    }

                    body { background: #fff !important; }
                    .toolbar { display: none !important; }

                    .pages {
                        padding: 0;
                        gap: 0;
                        background: #fff;
                    }

                    .a4 {
                        width: 210mm;
                        min-height: 297mm;
                        box-shadow: none;
                        border-radius: 0;
                        padding: 5mm 6mm;
                        page-break-after: always;
                        break-after: page;
                    }

                    .tag {
                        border: 1px solid #0f1f3d;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                }
            </style>
        </head>
        <body>
            <div class="toolbar">
                <div class="tbar-l">
                    <div>
                        <div class="tbar-title">Property Tags</div>
                        <div class="tbar-sub">Generated {$genTime}</div>
                    </div>
                    <div class="tbar-badge">{$count} tag(s)</div>
                </div>
                <div class="tbar-r">
                    <button class="btn btn-blue" onclick="window.print()">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <polyline points="6 9 6 2 18 2 18 9"/>
                            <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                            <rect x="6" y="14" width="12" height="8"/>
                        </svg>
                        Print All ({$count} tags)
                    </button>
                    <button class="btn btn-ghost" onclick="window.close()">&#10005; Close</button>
                </div>
            </div>
            <div class="pages">
                <div class="a4">
                    {$tagsHtml}
                </div>
            </div>

            <script>
                document.querySelectorAll('.tag-number').forEach(function (el) {
                    const body = el.closest('.tag-body');
                    const maxW = body ? body.offsetWidth - 8 : 120;
                    let size   = 11;

                    el.style.fontSize      = size + 'pt';
                    el.style.letterSpacing = '.03em';

                    while (el.scrollWidth > maxW && size > 5) {
                        size -= 0.5;
                        el.style.fontSize = size + 'pt';
                        if (size < 8) el.style.letterSpacing = '.01em';
                        if (size < 6) el.style.letterSpacing = '0';
                    }
                });
            </script>
        </body>
        </html>
        HTML;
    }
}