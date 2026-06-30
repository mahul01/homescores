<?php

require_once __DIR__ . '/../wp-content/themes/humescores/inc/weather-simulator/Physics.php';
require_once __DIR__ . '/../wp-content/themes/humescores/inc/weather-simulator/Diagnostics.php';

function fail($message) {
fwrite(STDERR, "FAIL: {$message}\n");
exit(1);
}

function assert_close($actual, $expected, $tolerance, $message) {
if (abs($actual - $expected) > $tolerance) {
fail($message . " (expected {$expected}, got {$actual})");
}
}

function assert_true($condition, $message) {
if (!$condition) {
fail($message);
}
}

$temperature = 20.0;
$pressure = 1000.0;
$qs = WeatherSimulatorPhysics::saturationMixingRatio($temperature, $pressure);
$rh = WeatherSimulatorPhysics::relativeHumidity($temperature, $pressure, $qs);
assert_close($rh, 1.0, 0.000001, 'RH should be near 100% at saturation');

$cell = array(
'temperature' => 20.0,
'pressure' => 1000.0,
'vapor' => $qs * 1.1,
'cloud' => 0.0,
'rain' => 0.0,
'rainfall_out' => 0.0,
'u' => 0.0,
'v' => 0.0,
);
$next = WeatherSimulatorPhysics::microphysicsStep($cell, 1.0);
assert_true($next['cloud'] > 0.0, 'Condensation should increase cloud water when supersaturated');
assert_true($next['temperature'] > $cell['temperature'], 'Condensation should warm the cell via latent heat');

$dryCell = array(
'temperature' => 20.0,
'pressure' => 1000.0,
'vapor' => $qs * 0.70,
'cloud' => 0.003,
'rain' => 0.0,
'rainfall_out' => 0.0,
'u' => 0.0,
'v' => 0.0,
);
$evap = WeatherSimulatorPhysics::microphysicsStep($dryCell, 1.0);
assert_true($evap['vapor'] > $dryCell['vapor'], 'Evaporation should increase vapor in undersaturated air');
assert_true($evap['cloud'] < $dryCell['cloud'], 'Evaporation should reduce cloud water when undersaturated');

assert_true(WeatherSimulatorDiagnostics::classify(0.10, 0.00001, 0.20) === 'clear', 'Low cloud/rain/fog should be clear');
assert_true(WeatherSimulatorDiagnostics::classify(0.50, 0.00001, 0.20) === 'cloudy', 'High cloud should classify as cloudy');
assert_true(WeatherSimulatorDiagnostics::classify(0.20, 0.00060, 0.20) === 'rain', 'Rain intensity should classify as rain');
assert_true(WeatherSimulatorDiagnostics::classify(0.20, 0.00001, 0.75) === 'fog', 'High fog index should classify as fog');

echo "Weather simulator deterministic tests passed.\n";
