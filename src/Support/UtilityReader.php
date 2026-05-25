<?php

namespace Pino\Support;

use Pino\Numera;

final class UtilityReader
{
    public static function toIp(Numera $numera, string $ip): string
    {
        $ip = DigitNormalizer::toAsciiDigits(trim($ip));

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            throw new \InvalidArgumentException('Invalid IPv4 address: ' . $ip);
        }

        $dot = $numera->translate('dot', 'dot', camelCase: false);
        $octets = explode('.', $ip);
        $parts = array_map(fn(string $octet) => self::octetToWords($numera, (int)$octet), $octets);

        return $numera->applyCamelCase(implode(' ' . $dot . ' ', $parts));
    }

    public static function toVersion(Numera $numera, string $version): string
    {
        $version = DigitNormalizer::toAsciiDigits(trim($version));
        $point = $numera->translate('point', 'point', camelCase: false);
        $dash = $numera->translate('dash', 'dash', camelCase: false);

        $segments = preg_split('/([.\-])/', $version, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if ($segments === false || $segments === []) {
            return $numera->applyCamelCase($numera->n2w($version));
        }

        $parts = [];
        foreach ($segments as $segment) {
            if ($segment === '.') {
                $parts[] = $point;
                continue;
            }
            if ($segment === '-') {
                $parts[] = $dash;
                continue;
            }
            if (preg_match('/^\d+$/', $segment)) {
                $parts[] = $numera->n2w((int)$segment);
            } else {
                $parts[] = $segment;
            }
        }

        return $numera->applyCamelCase(implode(' ', $parts));
    }

    private static function octetToWords(Numera $numera, int $octet): string
    {
        if ($octet < 0 || $octet > 255) {
            throw new \InvalidArgumentException('IPv4 octet must be between 0 and 255.');
        }

        if ($octet < 100) {
            return $numera->n2w($octet);
        }

        if ($octet < 200) {
            return trim($numera->n2w(1) . ' ' . $numera->n2w($octet % 100));
        }

        return trim($numera->n2w(2) . ' ' . $numera->n2w($octet % 100));
    }
}
