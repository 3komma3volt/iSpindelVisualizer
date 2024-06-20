<?php
include('header.php');
include('navbar.php');
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container mt-5">

  <div id="logo" class="text-center">
    <h1 class="d-inline">iSpindel: <?= $spindle_alias ?></h1>    <a class="bi bi-pencil-square d-inline" href="#"></a>
    <p>ID: <?= $spindle_id ?></p>

  </div>

  <div class='chart-container' style='position: relative; height:40vh;'>
    <canvas id='spindledata'></canvas>
  </div>

  <script>
    const ctxChart = document.getElementById('spindledata');

    new Chart(ctxChart, {
      type: 'line',
      data: {
        labels: [
          <?=$timestamps ?>,
        ],
        datasets: [
          {
            label: 'Gravity',
            data: [<?= $gravity_list ?>],
            borderWidth: 2,
            yAxisID: 'y3',
          },
          {
            label: 'Temperature',
            data: [<?= $temperature_list ?>],
            borderWidth: 1,
            yAxisID: 'y1',
          },
          {
            label: 'Battery',
            data: [<?= $battery_list ?>],
            borderWidth: 1,
            yAxisID: 'y2',
            hidden: true,
          },
          {          
            label: 'Angle',
            data: [<?= $angle_list ?>],
            borderWidth: 1,
            yAxisID: 'y',
            hidden: true,
          },
        ]
      },
      options: {
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'top'
          }
        },
        scales: {
          y: {
            title: {
              display: true,
              text: 'Angle/°'
            },
            beginAtZero: false,
            position: 'right',

          },
          y1: {
            title: {
              display: true,
              text: 'Temperature/°'
            },
            beginAtZero: false

          },
          y2: {
            title: {
              display: true,
              text: 'Battery/V'
            },
            beginAtZero: false,
            position: 'right',

          },
          y3: {
            title: {
              display: true,
              text: 'Gravity'
            },
            beginAtZero: false

          }
        }
      }
    });
  </script>
</div>


<?php include("footer.php") ?>