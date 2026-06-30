# HomeScores Weather Simulator MVP

This repository now includes an initial playable atmospheric simulator milestone.

## Run the simulator

From repository root:

```bash
php weather-simulator.php --preset=clear --steps=20
```

Interactive prompt mode:

```bash
php weather-simulator.php --preset=fog --interactive=1
```

## Controls and variables

The simulator evolves a 2D grid in time with state variables per cell:

- `temperature` (°C)
- `pressure` (hPa, with vertical altitude proxy)
- `vapor` (water vapor mixing ratio)
- `cloud` (cloud water mixing ratio)
- `rain` (rain water mixing ratio)
- `u`, `v` wind components (m/s)

CLI controls:

- `--temperature`
- `--temperature-gradient`
- `--humidity` (0..1+)
- `--pressure`
- `--wind-speed`
- `--wind-direction` (degrees)
- `--width`, `--height`, `--steps`, `--dt`

## Scenario presets

- `clear` (clear sky)
- `stratiform` (humid stratiform cloud case)
- `convective` (convective rain-prone case)
- `fog` (nighttime fog-prone case)

## Physics and diagnostics in this MVP

Implemented simplified processes:

- relative humidity from saturation mixing ratio
- condensation under supersaturation
- evaporation in undersaturated air
- cloud-to-rain autoconversion threshold
- rain fall tendency/sedimentation loss
- latent heat coupling to temperature

Per-step diagnostics:

- `clear` / `cloudy` / `rain` / `fog` classification
- mean RH
- cloud coverage proxy
- rain intensity proxy
- fog index and near-surface visibility proxy

## Example output

```text
t=01  ☀️ CLEAR  RH= 35.0% cloud=  0.0% rain=0.00000 fog=0.05 vis=9.45km
...
t=20  🌫️ FOG    RH= 98.0% cloud= 21.9% rain=0.00001 fog=0.81 vis=1.90km
```

## Deterministic tests

Run:

```bash
php tests/weather_simulator_test.php
```

Covered logic:

- RH consistency at saturation
- condensation transition and latent warming
- evaporation transition in undersaturated air
- classification thresholds for clear/cloudy/rain/fog

## Known simplifications

This is a lightweight MVP, not a full atmospheric solver. Simplifications include:

- 2D coarse grid instead of full 3D atmosphere
- heuristic advection and microphysics rates
- no explicit radiation transfer or terrain coupling
- no ice-phase microphysics (snow/hail/graupel)
- no data assimilation or observational calibration

## Roadmap for higher realism

1. Move to 3D staggered-grid dynamics and stricter numerical stability control.
2. Add diurnal radiation and land-surface flux coupling.
3. Add mixed-phase microphysics and improved precipitation processes.
4. Integrate terrain/orographic forcing and boundary-layer turbulence closure.
5. Add validation workflow against observed/reanalysis weather cases.
