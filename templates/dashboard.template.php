<?php
include('header.template.php');
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

  const newLegendClickHandler = function (e, legendItem, legend) {
    const index = legendItem.datasetIndex;
    const ci = legend.chart;
    if (ci.isDatasetVisible(index)) {
      ci.hide(index);
      legendItem.hidden = true;
    } else {
      ci.show(index);
      legendItem.hidden = false;
    }
    let viewValue = legendItem.hidden === false ? 1 : 0;

    $.ajax({
      url: 'view_configuration.php',
      type: 'POST',
      data: { 'changed_view': legendItem.text, 'changed_value': viewValue },
      dataType: 'json',
      success: function (response) {
        if (!response.success) {
          console.error('Error:', response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error('AJAX Error:', status, error);
      }
    });
  }
</script>

<div class="container mt-1">

  <div class="text-center">
    <h1 class="d-inline">iSpindel:
      <?= $spindle_alias ?>
    </h1>

    <?= $message ?>
    <div class="row justify-content-md-center mt-2 mb-3">
      <div class="col-auto mx-auto">
        <form method="POST">
          <div class="input-group input-group-sm mt-2">
            <span class="input-group-text" id="spindleIdName">ID:
              <?= $spindle_id ?>
            </span>
            <span class="input-group-text" id="timespanSelectDesc">View</span>
            <select class="form-select" aria-describedby="timespanSelectDesc" id="timespanSelect" name="timespanSelect"
              onchange="this.form.submit()">
              <option value="3" <?=$select3 ?>>3 days</option>
              <option value="7" <?=$select7 ?>>7 days</option>
              <option value="14" <?=$select14 ?>>14 days</option>
              <option value="21" <?=$select21 ?>>21 days</option>
            </select>

            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
              aria-expanded="false"><i class="bi bi-list"></i></button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#nameChangeDialog"><i
                    class="bi btn-sm bi-pencil-square me-2"></i>Rename</a></li>
              <li> <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#clearDataModal"><i
                    class="bi bi-eraser me-2"></i>Clear Data</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a href="logout.php" class="dropdown-item"><i class="bi bi-door-open me-2"></i>Logout</a></li>
            </ul>
          </div>
        </form>

      </div>
    </div>
    <div class='chart-container' style='position: relative; height:80vh;'>
      <canvas id='spindledata'></canvas>
    </div>
  </div>
  <script>
    const ctxChart = document.getElementById('spindledata');

    let gravity_hidden = <?= $gravity_visible ?> == 1 ? false : true;
    let temperature_hidden = <?= $temperature_visible ?> == 1 ? false : true;
    let battery_hidden = <?= $battery_visible ?> == 1 ? false : true;
    let angle_hidden = <?= $angle_visible ?> == 1 ? false : true;

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
          hidden: gravity_hidden
        },
        {
          label: 'Temperature',
          data: [<?= $temperature_list ?>],
          borderWidth: 1,
          yAxisID: 'y1',
          hidden: temperature_hidden
        },
        {
          label: 'Battery',
          data: [<?= $battery_list ?>],
          borderWidth: 1,
          yAxisID: 'y2',
          hidden: battery_hidden,
        },
        {
          label: 'Angle',
          data: [<?= $angle_list ?>],
          borderWidth: 1,
          yAxisID: 'y',
          hidden: angle_hidden,
        },
        ]
      },
      options: {
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'top',
            onClick: newLegendClickHandler
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
            min: 0,
            max: 90

          },
          y1: {
            title: {
              display: true,
              text: 'Temperature/°'
            },
            beginAtZero: false,
            min: 0,
            max: 30

          },
          y2: {
            title: {
              display: true,
              text: 'Battery/V'
            },
            beginAtZero: false,
            position: 'right',
            min: 2.5,
            max: 5.0

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
            <input type="text" class="form-control" name="formSpindleName" id="formSpindleName"
              value="<?= $spindle_alias ?>">
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


<div class="modal fade" id="clearDataModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Clear data</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="chkClearData" id="chkClearData">
            <label class="form-check-label" for="chkClearData">
              Confirm deletion of all mesaurement data (iSpindel key will not be deleted)
            </label>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" id="btnClearData" class="btn btn-danger" disabled>Clear</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const check = document.getElementById('chkClearData');
  const button = document.getElementById('btnClearData');
  check.addEventListener('change', () => {
    button.disabled = !check.checked;
  });

</script>

<?php include("footer.template.php") ?>