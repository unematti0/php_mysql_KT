<?php include("config.php"); ?>
<!doctype html>
<html lang="et">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Autoremondi broneering</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <h1>Broneeri autoremondi aeg</h1>

  <?php
  if (isset($_POST["submit"])) {
      $eesnimi = $_POST["eesnimi"];
      $perekonnanimi = $_POST["perekonnanimi"];
      $isikukood = $_POST["isikukood"];
      $email = $_POST["email"];
      $teenus_id = $_POST["teenus"];
      $töökoht_id = $_POST["töökoht"];
      $kuupäev = $_POST["kuupäev"];
      $kellaaeg = $_POST["kellaaeg"];

      // Kontroll kas töökoht on sel ajal vaba
      $kontroll = "SELECT * FROM broneering WHERE töökoht_id=$töökoht_id AND kuupäev='$kuupäev' AND algus_kellaaeg='$kellaaeg'";
      $kontroll_tulemus = mysqli_query($yhendus, $kontroll);

      if (mysqli_num_rows($kontroll_tulemus) > 0) {
          echo '<div class="alert alert-danger">Valitud töökoht on sellel ajal juba broneeritud.</div>';
      } else {
          // Otsime või lisame kliendi
          $klient_q = "SELECT id FROM klient WHERE isikukood='$isikukood'";
          $klient_r = mysqli_query($yhendus, $klient_q);

          if ($rida = mysqli_fetch_assoc($klient_r)) {
              $klient_id = $rida["id"];
          } else {
              $insert_klient = "INSERT INTO klient (eesnimi, perekonnanimi, isikukood, email)
                                VALUES ('$eesnimi', '$perekonnanimi', '$isikukood', '$email')";
              mysqli_query($yhendus, $insert_klient);
              $klient_id = mysqli_insert_id($yhendus);
          }

          $insert_bron = "INSERT INTO broneering (klient_id, teenus_id, töökoht_id, kuupäev, algus_kellaaeg)
                          VALUES ($klient_id, $teenus_id, $töökoht_id, '$kuupäev', '$kellaaeg')";
          mysqli_query($yhendus, $insert_bron);

          echo '<div class="alert alert-success">Broneering edukalt salvestatud!</div>';
      }
  }
  ?>

  <form method="POST" class="row g-3">
    <h4>Kliendi andmed</h4>
    <div class="col-md-6">
      <label class="form-label">Eesnimi</label>
      <input type="text" name="eesnimi" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Perekonnanimi</label>
      <input type="text" name="perekonnanimi" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Isikukood</label>
      <input type="text" name="isikukood" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">E-mail</label>
      <input type="email" name="email" class="form-control" required>
    </div>

    <h4 class="pt-3">Broneering</h4>
    <div class="col-md-6">
      <label class="form-label">Teenuse tüüp</label>
      <select name="teenus" class="form-select" required>
        <option value="">Vali teenus...</option>
        <?php
        $teenused = mysqli_query($yhendus, "SELECT * FROM teenus");
        while ($r = mysqli_fetch_assoc($teenused)) {
            echo "<option value='".$r['id']."'>".$r['nimi']." (".$r['hind']."€)</option>";
        }
        ?>
      </select>
    </div>

    <div class="col-md-6">
      <label class="form-label">Töökoht</label>
      <select name="töökoht" class="form-select" required>
        <option value="">Vali töökoht...</option>
        <?php
        $tk = mysqli_query($yhendus, "SELECT * FROM töökoht");
        while ($r = mysqli_fetch_assoc($tk)) {
            echo "<option value='".$r['id']."'>".$r['nimi']."</option>";
        }
        ?>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label">Kuupäev</label>
      <input type="date" name="kuupäev" class="form-control" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Algusaeg</label>
      <input type="time" name="kellaaeg" class="form-control" required>
    </div>

    <div class="col-12">
      <button type="submit" name="submit" class="btn btn-primary">Broneeri aeg</button>
    </div>
  </form>

</div>
</body>
</html>
