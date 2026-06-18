<?php

namespace Tests\Browser\Concerns;

/**
 * Small helpers for the browser tests to read computed styles out of the real
 * page (this is what lets e2e verify "styles are actually applied", not just
 * that markup exists). Parses CSS rgb()/rgba() strings into [r, g, b].
 */
trait ReadsComputedStyle
{
    /**
     * @return array{0:int,1:int,2:int}
     */
    protected function rgb(?string $css): array
    {
        if (! $css) {
            return [0, 0, 0];
        }

        preg_match_all('/\d+/', $css, $m);

        return [
            (int) ($m[0][0] ?? 0),
            (int) ($m[0][1] ?? 0),
            (int) ($m[0][2] ?? 0),
        ];
    }

    protected function isReddish(?string $css): bool
    {
        [$r, $g, $b] = $this->rgb($css);

        return $r > 120 && $r > $g + 40 && $r > $b + 40;
    }

    protected function brightness(?string $css): int
    {
        return array_sum($this->rgb($css));
    }
}
