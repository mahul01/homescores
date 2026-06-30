<?php

require_once __DIR__ . '/Physics.php';
require_once __DIR__ . '/Diagnostics.php';

class WeatherSimulatorEngine {
private $width;
private $height;
private $dt;
private $grid;
private $time;

public function __construct($width, $height, $dt, array $params) {
$this->width = $width;
$this->height = $height;
$this->dt = $dt;
$this->time = 0;
$this->grid = $this->initializeGrid($params);
}

private function initializeGrid(array $params) {
$grid = array();
$directionRad = deg2rad($params['wind_direction']);
$u = $params['wind_speed'] * cos($directionRad);
$v = $params['wind_speed'] * sin($directionRad);

for ($y = 0; $y < $this->height; $y++) {
$grid[$y] = array();
$heightFactor = ($this->height - 1 - $y);
for ($x = 0; $x < $this->width; $x++) {
$temp = $params['temperature'] - ($params['temperature_gradient'] * $heightFactor);
$pressure = max(700.0, $params['pressure'] - ($heightFactor * 14.0));
$qs = WeatherSimulatorPhysics::saturationMixingRatio($temp, $pressure);
$vapor = $qs * max(0.05, min(1.2, $params['humidity']));

$grid[$y][$x] = array(
'temperature' => $temp,
'pressure' => $pressure,
'vapor' => $vapor,
'cloud' => 0.0,
'rain' => 0.0,
'rainfall_out' => 0.0,
'u' => $u,
'v' => $v,
);
}
}

return $grid;
}

public function step() {
$advected = $this->advectState($this->grid);
for ($y = 0; $y < $this->height; $y++) {
for ($x = 0; $x < $this->width; $x++) {
$advected[$y][$x] = WeatherSimulatorPhysics::microphysicsStep($advected[$y][$x], $this->dt);
}
}

$this->grid = $advected;
$this->time += $this->dt;

return array(
'time' => $this->time,
'diagnostics' => WeatherSimulatorDiagnostics::summarize($this->grid),
'grid' => $this->grid,
);
}

private function advectState(array $grid) {
$newGrid = $grid;
$mix = 0.09 * $this->dt;

for ($y = 0; $y < $this->height; $y++) {
for ($x = 0; $x < $this->width; $x++) {
$cell = $grid[$y][$x];
$sourceX = $x - (($cell['u'] >= 0) ? 1 : -1);
$sourceY = $y - (($cell['v'] >= 0) ? 1 : -1);
$sourceX = max(0, min($this->width - 1, $sourceX));
$sourceY = max(0, min($this->height - 1, $sourceY));
$src = $grid[$sourceY][$sourceX];

$newGrid[$y][$x]['temperature'] = (1.0 - $mix) * $cell['temperature'] + ($mix * $src['temperature']);
$newGrid[$y][$x]['vapor'] = max(0.0, (1.0 - $mix) * $cell['vapor'] + ($mix * $src['vapor']));
$newGrid[$y][$x]['cloud'] = max(0.0, (1.0 - $mix) * $cell['cloud'] + ($mix * $src['cloud']));
$newGrid[$y][$x]['rain'] = max(0.0, (1.0 - $mix) * $cell['rain'] + ($mix * $src['rain']));
$newGrid[$y][$x]['rainfall_out'] = 0.0;
}
}

return $newGrid;
}
}
