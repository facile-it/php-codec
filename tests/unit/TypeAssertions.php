<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec;

use Facile\PhpCodec\Decoder;

class TypeAssertions
{
    /**
     * @param Decoder<mixed, string> $d
     */
    public static function decoderMixedString(Decoder $d): void {}

    /**
     * @param Decoder<mixed, int> $d
     */
    public static function decoderMixedInt(Decoder $d): void {}

    /**
     * @param Decoder<mixed, float> $d
     */
    public static function decoderMixedFloat(Decoder $d): void {}

    /**
     * @param Decoder<mixed, bool> $d
     */
    public static function decoderMixedBool(Decoder $d): void {}
}
