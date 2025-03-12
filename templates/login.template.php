<?php include('header.template.php') ?>
<style>
  .div-center {
    width: 400px;
    height: 400px;
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    margin: auto;
  }
</style>
<div class="container mt-5">

  <div id="logo" class="text-center">
    <h1>Access iSpindel</h1>
    <p><?= $error_message ?></p>

  </div>
  <form method="post" id="main">
  <div class="div-center">
    <div class="form-group">
      <label for="spindle_id">Spindle ID (decimal):</label>
      <input type="text" class="form-control" id="spindle_id" name="spindle_id" placeholder="123456">
    </div>
    <div class="form-group">
      <label for="spindle_key">Spindle Key:</label>
      <input type="password" class="form-control" id="spindle_key" name="spindle_key" placeholder="...">
    </div>
    <div class="row d-flex mt-3 ms-2 me-2"> <button type="submit" class="btn btn-primary mt-1">Access</button>
    </div>
    </form>
  </div>

</div>

<?php include('footer.template.php') ?>