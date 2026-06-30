<?php

require_once __DIR__ . '/Physics.php';

class WeatherSimulatorDiagnostics {
public static function summarize(array $grid) {
$totalCells = 0;
$rhSum = 0.0;
$cloudCells = 0;
$rainSum = 0.0;
$surfaceFogSignal = 0.0;
$surfaceCount = 0;
$windSurface = 0.0;

$maxY = count($grid) - 1;
foreach ($grid as $y => $row) {
foreach ($row as $cell) {
$totalCells++;
$rh = WeatherSimulatorPhysics::relativeHumidity($cell['temperature'], $cell['pressure'], $cell['vapor']);
$rhSum += min($rh, 1.5);

if ($cell['cloud'] > 0.00005) {
$cloudCells++;
}
$rainSum += $cell['rain'] + $cell['rainfall_out'];

if ($y === $maxY) {
$surfaceCount++;
$surfaceFogSignal += min($rh, 1.1) * (1.0 + min($cell['cloud'] * 300.0, 0.5));
$windSurface += sqrt(($cell['u'] * $cell['u']) + ($cell['v'] * $cell['v']));
}
}
}

$meanRh = $totalCells ? $rhSum / $totalCells : 0.0;
$cloudCoverage = $totalCells ? $cloudCells / $totalCells : 0.0;
$rainIntensity = $totalCells ? $rainSum / $totalCells : 0.0;
$surfaceFogSignal = $surfaceCount ? $surfaceFogSignal / $surfaceCount : 0.0;
$meanSurfaceWind = $surfaceCount ? $windSurface / $surfaceCount : 0.0;
$fogIndex = min(1.0, $surfaceFogSignal * max(0.0, 1.0 - min($meanSurfaceWind / 4.5, 1.0)));
$visibilityKm = max(0.2, 10.0 * (1.0 - min($fogIndex, 0.95)));

return array(
'mean_rh' => $meanRh,
'cloud_coverage' => $cloudCoverage,
'rain_intensity' => $rainIntensity,
'fog_index' => $fogIndex,
'visibility_km' => $visibilityKm,
'classification' => self::classify($cloudCoverage, $rainIntensity, $fogIndex),
);
}

public static function classify($cloudCoverage, $rainIntensity, $fogIndex) {
if ($rainIntensity >= 0.00008) {
return 'rain';
}
if ($fogIndex >= 0.70) {
return 'fog';
}
if ($cloudCoverage >= 0.20) {
return 'cloudy';
}

return 'clear';
}
}
