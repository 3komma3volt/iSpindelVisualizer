<?php
include('header.php');
include('navbar.php');
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container mt-5">

  <div class="text-center">
    <h1 class="d-inline">iSpindel: <?= $spindle_alias ?></h1>
    <a class="bi bi-pencil-square d-inline" href="#" data-bs-toggle="modal" data-bs-target="#nameChangeDialog"></a>
    <p>ID: <?=$spindle_id ?></p>
    <?=$error_message ?>
    <form method="POST">
      <label class="mr-sm-2" for="timespanSelect">View</label>
      <select class="mr-sm-2 mb-2" id="timespanSelect" name="timespanSelect" onchange="this.form.submit()">
        <option value="3" <?= $select3 ?>>3 days</option>
        <option value="7" <?= $select7 ?>>7 days</option>
        <option value="14" <?= $select14 ?>>14 days</option>
        <option value="21" <?= $select21 ?>>21 days</option>
      </select>
    </form>
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
          <?= $timestamps ?>,
        ],
        datasets: [{
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

<div class="modal fade" id="nameChangeDialog" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Rename iSpindel</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label for="formSpindleName">New Name:</label>
            <input type="text" class="form-control" name="formSpindleName" id="formSpindleName" value="<?= $spindle_alias ?>">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include("footer.php") ?>