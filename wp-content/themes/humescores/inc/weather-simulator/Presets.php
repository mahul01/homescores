<?php

class WeatherSimulatorPresets {
public static function all() {
return array(
'clear' => array(
'label' => 'Clear Sky',
'temperature' => 22.0,
'temperature_gradient' => 1.2,
'humidity' => 0.35,
'pressure' => 1018.0,
'wind_speed' => 4.0,
'wind_direction' => 45.0,
),
'stratiform' => array(
'label' => 'Humid Stratiform Cloud',
'temperature' => 16.0,
'temperature_gradient' => 0.4,
'humidity' => 1.06,
'pressure' => 1008.0,
'wind_speed' => 2.0,
'wind_direction' => 90.0,
),
'convective' => array(
'label' => 'Convective Rain-Prone',
'temperature' => 30.0,
'temperature_gradient' => 2.5,
'humidity' => 1.20,
'pressure' => 1002.0,
'wind_speed' => 7.0,
'wind_direction' => 210.0,
),
'fog' => array(
'label' => 'Nighttime Fog-Prone',
'temperature' => 9.0,
'temperature_gradient' => 0.2,
'humidity' => 0.98,
'pressure' => 1015.0,
'wind_speed' => 0.8,
'wind_direction' => 20.0,
),
);
}

public static function get($name) {
$all = self::all();
if (isset($all[$name])) {
return $all[$name];
}

return $all['clear'];
}
}
