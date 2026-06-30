<?php
/**
 * Simplified atmospheric physics and microphysics utilities.
 */

class WeatherSimulatorPhysics {
const EPSILON = 0.622;

/**
 * Tetens formula for saturation vapor pressure in hPa.
 */
public static function saturationVaporPressure($temperatureC) {
return 6.112 * exp((17.67 * $temperatureC) / ($temperatureC + 243.5));
}

/**
 * Saturation mixing ratio (kg/kg).
 */
public static function saturationMixingRatio($temperatureC, $pressureHpa) {
$es = self::saturationVaporPressure($temperatureC);
$denominator = max($pressureHpa - $es, 0.1);
return self::EPSILON * $es / $denominator;
}

/**
 * Relative humidity [0, 1+] from vapor mixing ratio (kg/kg).
 */
public static function relativeHumidity($temperatureC, $pressureHpa, $vaporMixingRatio) {
$qs = self::saturationMixingRatio($temperatureC, $pressureHpa);
if ($qs <= 0.0) {
return 0.0;
}

return $vaporMixingRatio / $qs;
}

/**
 * One-cell microphysics step with simplified latent heating.
 */
public static function microphysicsStep(array $cell, $dt) {
$qs = self::saturationMixingRatio($cell['temperature'], $cell['pressure']);
$latentHeatFactor = 2200.0;
$condensationRate = 1.0;
$evaporationRate = 0.6;

if ($cell['vapor'] > $qs) {
$excess = $cell['vapor'] - $qs;
$condensed = min($excess, $excess * $condensationRate * $dt);
$cell['vapor'] -= $condensed;
$cell['cloud'] += $condensed;
$cell['temperature'] += $latentHeatFactor * $condensed;
} elseif ($cell['vapor'] < $qs && ($cell['cloud'] > 0.0 || $cell['rain'] > 0.0)) {
$deficit = $qs - $cell['vapor'];
$evapTarget = min($deficit, ($cell['cloud'] + $cell['rain']) * $evaporationRate * $dt);
$evapCloud = min($cell['cloud'], $evapTarget);
$remaining = $evapTarget - $evapCloud;
$evapRain = min($cell['rain'], $remaining);
$evaporated = $evapCloud + $evapRain;

$cell['cloud'] -= $evapCloud;
$cell['rain'] -= $evapRain;
$cell['vapor'] += $evaporated;
$cell['temperature'] -= $latentHeatFactor * $evaporated;
}

$autoconversionThreshold = 0.0012;
$autoconversionRate = 0.35;
if ($cell['cloud'] > $autoconversionThreshold) {
$convert = ($cell['cloud'] - $autoconversionThreshold) * $autoconversionRate * $dt;
$convert = min($convert, $cell['cloud']);
$cell['cloud'] -= $convert;
$cell['rain'] += $convert;
}

$fallRate = 0.25;
$rainfallOut = $cell['rain'] * $fallRate * $dt;
$cell['rain'] = max(0.0, $cell['rain'] - $rainfallOut);
$cell['rainfall_out'] = $rainfallOut;

return $cell;
}
}
