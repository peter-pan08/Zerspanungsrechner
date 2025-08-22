<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CalculationTest extends TestCase
{
    public function testBasicCalculationMatchesExample(): void
    {
        $vc = 100.0; // m/min
        $d = 100.0; // mm
        $f = 0.2; // mm/U
        $ap = 0.5; // mm
        $kc = 1500.0; // N/mm^2

        $n = (1000 * $vc) / (M_PI * $d);
        $vc_berechnet = (M_PI * $d * $n) / 1000;
        $this->assertEqualsWithDelta($vc, $vc_berechnet, 0.01);

        $q_mm3 = $ap * $f * $n * M_PI * $d / 1000;
        $this->assertEqualsWithDelta(10.0, $q_mm3, 0.01);

        $Fc = $kc * $ap * $f;
        $leistung = ($Fc * $vc_berechnet) / 60000; // kW
        $this->assertEqualsWithDelta(0.25, $leistung, 0.001);
    }
}
