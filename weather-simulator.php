#!/usr/bin/env php
<?php

require_once __DIR__ . '/wp-content/themes/humescores/inc/weather-simulator/Presets.php';
require_once __DIR__ . '/wp-content/themes/humescores/inc/weather-simulator/SimulatorEngine.php';

function parse_args($argv) {
$options = getopt('', array(
'preset::',
'steps::',
'width::',
'height::',
'dt::',
'temperature::',
'temperature-gradient::',
'humidity::',
'pressure::',
'wind-speed::',
'wind-direction::',
'interactive::',
));

$presetName = isset($options['preset']) ? $options['preset'] : 'clear';
$params = WeatherSimulatorPresets::get($presetName);

$map = array(
'temperature' => 'temperature',
'temperature-gradient' => 'temperature_gradient',
'humidity' => 'humidity',
'pressure' => 'pressure',
'wind-speed' => 'wind_speed',
'wind-direction' => 'wind_direction',
);

foreach ($map as $opt => $paramKey) {
if (isset($options[$opt])) {
$params[$paramKey] = (float) $options[$opt];
}
}

if (isset($options['interactive'])) {
$params = prompt_parameters($params);
}

return array(
'preset_name' => $presetName,
'steps' => isset($options['steps']) ? max(1, (int) $options['steps']) : 24,
'width' => isset($options['width']) ? max(4, (int) $options['width']) : 12,
'height' => isset($options['height']) ? max(3, (int) $options['height']) : 8,
'dt' => isset($options['dt']) ? max(0.1, (float) $options['dt']) : 1.0,
'params' => $params,
);
}

function prompt_parameters(array $params) {
echo "Interactive mode: press Enter to keep defaults.\n";
foreach ($params as $key => $default) {
$label = str_replace('_', ' ', $key);
echo ucfirst($label) . ' [' . $default . ']: ';
$line = trim(fgets(STDIN));
if ($line !== '') {
$params[$key] = (float) $line;
}
}

return $params;
}

function state_icon($classification) {
$icons = array(
'clear' => '☀️',
'cloudy' => '☁️',
'rain' => '🌧️',
'fog' => '🌫️',
);

return isset($icons[$classification]) ? $icons[$classification] : '•';
}

function print_dashboard($step, array $diag) {
printf(
"t=%02d  %s %-6s RH=%5.1f%% cloud=%5.1f%% rain=%0.5f fog=%0.2f vis=%0.2fkm\n",
$step,
state_icon($diag['classification']),
strtoupper($diag['classification']),
$diag['mean_rh'] * 100.0,
$diag['cloud_coverage'] * 100.0,
$diag['rain_intensity'],
$diag['fog_index'],
$diag['visibility_km']
);
}

$run = parse_args($argv);
$engine = new WeatherSimulatorEngine($run['width'], $run['height'], $run['dt'], $run['params']);

printf("Weather simulator preset: %s (%s)\n", $run['preset_name'], $run['params']['label']);
printf("Grid: %dx%d  steps: %d  dt: %.2f\n", $run['width'], $run['height'], $run['steps'], $run['dt']);
printf("Controls: T=%.1fC grad=%.2fC/layer RH=%.0f%% P=%.1fhPa wind=%.1fm/s dir=%.0f°\n\n",
$run['params']['temperature'],
$run['params']['temperature_gradient'],
$run['params']['humidity'] * 100.0,
$run['params']['pressure'],
$run['params']['wind_speed'],
$run['params']['wind_direction']
);

$history = array();
for ($i = 1; $i <= $run['steps']; $i++) {
$result = $engine->step();
$diag = $result['diagnostics'];
$history[] = $diag['classification'];
print_dashboard($i, $diag);
}

echo "\nFinal state counts: " . implode(', ', array_map(function ($state) use ($history) {
$count = 0;
foreach ($history as $s) {
if ($s === $state) {
$count++;
}
}
return strtoupper($state) . ':' . $count;
}, array('clear', 'cloudy', 'rain', 'fog'))) . "\n";
