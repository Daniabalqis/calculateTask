<?php
session_start();
$show = false;

if(isset($_POST['reset'])){
    unset($calchistory);
    session_destroy();
}
if (isset($_POST['submit'])) {
    $vol = floatval($_POST['vol']);
    $cur = floatval($_POST['cur']);
    $rate = floatval($_POST['rate']);

    $power = $vol * $cur;
    $energy_per_hour = $power / 1000;
    $energy_per_day = $energy_per_hour * 24;

    function calculate_cost($energy) {
        if ($energy <= 200) {
            $rate = 21.80;
            return $energy * $rate;
        } elseif ($energy <= 300) {

            return (200 * 21.80) + (($energy - 200) * 33.40);
        } elseif ($energy <= 600) {
            return (200 * 21.80) + (100 * 33.40) + (($energy - 300) * 51.60);
        } elseif ($energy <= 900) {
            return (200 * 21.80) + (100 * 33.40) + (300 * 51.60) + (($energy - 600) * 54.60);
        } else {
            return (200 * 21.80) + (100 * 33.40) + (300 * 51.60) + (300 * 54.60) + (($energy - 900) * 57.10);
        }
    }

    $cost_per_hour = calculate_cost($energy_per_hour) / 100;
    $cost_per_hour *= ($rate / 100);
    if ($cost_per_hour < 3 / 24) { 
        $cost_per_hour = 3 / 24;
    }

    $cost_per_day = calculate_cost($energy_per_day) / 100;
    $cost_per_day *= ($rate / 100);
    if ($cost_per_day < 3) {
        $cost_per_day = 3;
    }

    $calchistory = [
        'vol' => $vol,
        'cur' => $cur,
        'rate' => $rate,
        'power' => $power,
        'energy_per_hour' => $energy_per_hour,
        'energy_per_day' => $energy_per_day,
        'cost_per_hour' => $cost_per_hour,
        'cost_per_day' => $cost_per_day
    ];

    if (isset($_SESSION['history'])) {
        $_SESSION['history'][] = $calchistory;
        $show = true;
    } else {
        $_SESSION['history'] = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Power Energy Calculator</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="mt-5">Calculator</h1>
        <form method="post" class="mt-3">
            <div class="form-group">
                <label>Voltage (V)</label>
                <input type="number" step="0.01" class="form-control" id="vol" name="vol" required>
            </div>
            <div class="form-group">
                <label>Current (A)</label>
                <input type="number" step="0.01" class="form-control" id="cur" name="cur" required>
            </div>
            <div class="form-group">
                <label>Current Rate (%)</label>
                <input type="number" step="0.01" class="form-control" id="rate" name="rate" required>
            </div>
            <input type="submit" name="submit" class="btn btn-primary" value="Calculate">
           
        </form>
        <br>
        <form action="" method="POST">
        <input type="submit" name="reset" class="btn btn-primary" value="Reset">
        </form>

    <?php

if(isset($_POST['submit'])){
            echo "<div class='mt-4 alert alert-success'>Cost per Hour: RM" . number_format($cost_per_hour, 2) . "<br>
            Cost per Day: RM" . number_format($cost_per_day, 2) . "<br>
            Power : " . number_format($power, 2) . " W<br>
            Energy per Hour : " . number_format($energy_per_hour, 2) . " kWh<br>
            Energy per Day : " . number_format($energy_per_day, 2) . " kWh</div>";
        }
        ?>

        <?php
        

        if ($show == true){ ?>
            <h2 class="mt-5">Calculation History</h2>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Voltage (V)</th>
                        <th scope="col">Current (A)</th>
                        <th scope="col">Current Rate (%)</th>
                        <th scope="col">Power (W)</th>
                        <th scope="col">Energy per Hour (kWh)</th>
                        <th scope="col">Energy per Day (kWh)</th>
                        <th scope="col">Cost per Hour (RM)</th>
                        <th scope="col">Cost per Day (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($_SESSION['history'] as $history) : ?>
                        <tr>
                            <td><?= htmlspecialchars($i) ?></td>
                            <td><?= htmlspecialchars($history['vol']) ?></td>
                            <td><?= htmlspecialchars($history['cur']) ?></td>
                            <td><?= htmlspecialchars($history['rate']) ?></td>
                            <td><?= htmlspecialchars(number_format($history['power'], 2)) ?></td>
                            <td><?= htmlspecialchars(number_format($history['energy_per_hour'], 2)) ?></td>
                            <td><?= htmlspecialchars(number_format($history['energy_per_day'], 2)) ?></td>
                            <td><?= htmlspecialchars(number_format($history['cost_per_hour'], 2)) ?></td>
                            <td><?= htmlspecialchars(number_format($history['cost_per_day'], 2)) ?></td>
                        </tr>
                    <?php
                        $i = $i+1;
                    endforeach; ?>
                </tbody>
            </table>
        <?php }; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>