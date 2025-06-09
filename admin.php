<?php
// ===================== PHP LOGIKA =====================
// 1. Sisselogimise kontroll ja ühendus
session_start();
include("config.php");
if (!isset($_SESSION['tuvastamine'])) {
  header('Location: logout.php');
  exit();
}

// 2. Teenuse lisamine
// Kui admin lisab uue teenuse, salvestatakse see andmebaasi
if (isset($_POST["teenuse_lisamine"])) {
    $nimi = $_POST["nimi"];
    $kirjeldus = $_POST["kirjeldus"];
    $kestus = $_POST["kestus"];
    $hind = $_POST["hind"];
    $stmt = $yhendus->prepare("INSERT INTO teenus (nimi, kirjeldus, kestus, hind) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssid", $nimi, $kirjeldus, $kestus, $hind);
    $stmt->execute();
}

// 3. Teenuse kustutamine
if (isset($_GET["kustuta_teenus"])) {
    $id = intval($_GET["kustuta_teenus"]);
    $yhendus->query("DELETE FROM teenus WHERE id=$id");
}

// 4. Teenuse muutmine
if (isset($_POST["muuda_teenus"])) {
    $id = intval($_POST["teenus_id"]);
    $nimi = mysqli_real_escape_string($yhendus, $_POST["muuda_nimi"]);
    $kirjeldus = mysqli_real_escape_string($yhendus, $_POST["muuda_kirjeldus"]);
    $kestus = intval($_POST["muuda_kestus"]);
    $hind = floatval($_POST["muuda_hind"]);
    $yhendus->query("UPDATE teenus SET nimi='$nimi', kirjeldus='$kirjeldus', kestus=$kestus, hind=$hind WHERE id=$id");
    $teenus_success = "Teenust uuendati!";
}

// 5. Broneeringu kustutamine
if (isset($_GET["kustuta_broneering"])) {
    $id = intval($_GET["kustuta_broneering"]);
    $yhendus->query("DELETE FROM broneering WHERE id=$id");
}

// 6. Broneeringu muutmine
if (isset($_POST["muuda_broneering"])) {
    $id = intval($_POST["broneering_id"]);
    $kuupaev = mysqli_real_escape_string($yhendus, $_POST["muuda_kuupaev"]);
    $kellaaeg = mysqli_real_escape_string($yhendus, $_POST["muuda_kellaaeg"]);
    $tookoht_id = intval($_POST["muuda_tookoht"]);

    // Kontroll, et uus aeg poleks juba hõivatud
    $kontroll = $yhendus->query("SELECT id FROM broneering WHERE tookoht_id=$tookoht_id AND kuupaev='$kuupaev' AND algus_kellaaeg='$kellaaeg' AND id!=$id");
    if ($kontroll->num_rows > 0) {
        $muuda_error = "Sellel ajal on töökoht juba hõivatud!";
    } else {
        $yhendus->query("UPDATE broneering SET kuupaev='$kuupaev', algus_kellaaeg='$kellaaeg', tookoht_id=$tookoht_id WHERE id=$id");
        $muuda_success = "Broneeringut uuendati!";
    }
}
?>
<!doctype html>
<html lang="et">
<head>
  <meta charset="utf-8">
  <title>Admin - Autotöökoda</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <h1 class="mb-4">Administraatori leht</h1>
  <form action="logout.php" method="post" class="mb-4">
    <input type="submit" value="Logi välja" name="logout" class="btn btn-outline-danger">
  </form>

  <!-- TEENUSE LISAMINE -->
  <h2>Lisa uus teenus</h2>
  <form method="post" class="row g-3 mb-5">
    <div class="col-md-4">
      <input type="text" name="nimi" class="form-control" placeholder="Teenuse nimi" required>
    </div>
    <div class="col-md-4">
      <input type="text" name="kirjeldus" class="form-control" placeholder="Kirjeldus">
    </div>
    <div class="col-md-2">
      <input type="number" name="kestus" class="form-control" placeholder="Kestus (min)" required>
    </div>
    <div class="col-md-2">
      <input type="number" step="0.01" name="hind" class="form-control" placeholder="Hind (€)" required>
    </div>
    <div class="col-12">
      <button type="submit" name="teenuse_lisamine" class="btn btn-success">Lisa teenus</button>
    </div>
  </form>

  <!-- TEENUSTE TABEL -->
  <h2>Teenused</h2>
  <?php if (!empty($teenus_success)) echo "<div class='alert alert-success'>$teenus_success</div>"; ?>
  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Nimi</th>
        <th>Kirjeldus</th>
        <th>Kestus (min)</th>
        <th>Hind (€)</th>
        <th>Tegevus</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Teenuste tabeli ridade kuvamine ja muutmise vorm
      $muuda_teenus_id = isset($_GET['muuda_teenus']) ? intval($_GET['muuda_teenus']) : 0;
      $tulemus = $yhendus->query("SELECT * FROM teenus ORDER BY id DESC");
      while ($rida = $tulemus->fetch_assoc()) {
        if ($muuda_teenus_id === intval($rida['id'])) {
          // Teenuse muutmise vorm
          echo "<tr>
            <form method='post'>
              <td>{$rida['id']}<input type='hidden' name='teenus_id' value='{$rida['id']}'></td>
              <td><input type='text' name='muuda_nimi' value=\"".htmlspecialchars($rida['nimi'])."\" class='form-control form-control-sm' required></td>
              <td><input type='text' name='muuda_kirjeldus' value=\"".htmlspecialchars($rida['kirjeldus'])."\" class='form-control form-control-sm'></td>
              <td><input type='number' name='muuda_kestus' value=\"{$rida['kestus']}\" class='form-control form-control-sm' required></td>
              <td><input type='number' step='0.01' name='muuda_hind' value=\"{$rida['hind']}\" class='form-control form-control-sm' required></td>
              <td>
                <button type='submit' name='muuda_teenus' class='btn btn-success btn-sm'>Salvesta</button>
                <a href='admin.php' class='btn btn-secondary btn-sm'>Tühista</a>
              </td>
            </form>
          </tr>";
        } else {
          echo "<tr>
                <td>{$rida['id']}</td>
                <td>{$rida['nimi']}</td>
                <td>{$rida['kirjeldus']}</td>
                <td>{$rida['kestus']}</td>
                <td>{$rida['hind']}</td>
                <td>
                  <a href='?muuda_teenus={$rida['id']}' class='btn btn-warning btn-sm'>Muuda</a>
                  <a href='?kustuta_teenus={$rida['id']}' class='btn btn-danger btn-sm'>Kustuta</a>
                </td>
              </tr>";
        }
      }
      ?>
    </tbody>
  </table>

  <!-- BRONEERINGUD -->
  <h2 class="mt-5">Broneeringud</h2>
  <?php if (!empty($muuda_error)) echo "<div class='alert alert-danger'>$muuda_error</div>"; ?>
  <?php if (!empty($muuda_success)) echo "<div class='alert alert-success'>$muuda_success</div>"; ?>
  <table class="table table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Klient</th>
        <th>Teenus</th>
        <th>Töökoh</th>
        <th>Kuupäev</th>
        <th>Kellaaeg</th>
        <th>Tegevus</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Broneeringute tabeli ridade kuvamine ja muutmise vorm
      $muuda_id = isset($_GET['muuda_broneering']) ? intval($_GET['muuda_broneering']) : 0;
      $broneeringud = $yhendus->query("
        SELECT b.id, CONCAT(k.eesnimi, ' ', k.perekonnanimi) AS klient, 
               t.nimi AS teenus, tk.nimi AS tookoht, b.kuupaev, b.algus_kellaaeg, b.tookoht_id
        FROM broneering b
        JOIN klient k ON b.klient_id = k.id
        JOIN teenus t ON b.teenus_id = t.id
        JOIN tookoht tk ON b.tookoht_id = tk.id
        ORDER BY b.kuupaev DESC, b.algus_kellaaeg DESC
      ");
      while ($rida = $broneeringud->fetch_assoc()) {
        if ($muuda_id === intval($rida['id'])) {
          // Broneeringu muutmise vorm
          echo "<tr>
            <form method='post'>
              <td>{$rida['id']}<input type='hidden' name='broneering_id' value='{$rida['id']}'></td>
              <td>{$rida['klient']}</td>
              <td>{$rida['teenus']}</td>
              <td>
                <select name='muuda_tookoht' class='form-select form-select-sm'>";
                  $tk = $yhendus->query("SELECT * FROM tookoht");
                  while ($t = $tk->fetch_assoc()) {
                    $selected = ($t['nimi'] == $rida['tookoht']) ? "selected" : "";
                    echo "<option value='{$t['id']}' $selected>{$t['nimi']}</option>";
                  }
          echo "  </select>
              </td>
              <td><input type='date' name='muuda_kuupaev' value='{$rida['kuupaev']}' class='form-control form-control-sm' required></td>
              <td><input type='time' name='muuda_kellaaeg' value='{$rida['algus_kellaaeg']}' class='form-control form-control-sm' required></td>
              <td>
                <button type='submit' name='muuda_broneering' class='btn btn-success btn-sm'>Salvesta</button>
                <a href='admin.php' class='btn btn-secondary btn-sm'>Tühista</a>
              </td>
            </form>
          </tr>";
        } else {
          echo "<tr>
                <td>{$rida['id']}</td>
                <td>{$rida['klient']}</td>
                <td>{$rida['teenus']}</td>
                <td>{$rida['tookoht']}</td>
                <td>{$rida['kuupaev']}</td>
                <td>{$rida['algus_kellaaeg']}</td>
                <td>
                  <a href='?muuda_broneering={$rida['id']}' class='btn btn-warning btn-sm'>Muuda</a>
                  <a href='?kustuta_broneering={$rida['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Kas oled kindel, et soovid selle broneeringu kustutada?');\">Kustuta</a>
                </td>
              </tr>";
        }
      }
      ?>
    </tbody>
  </table>
</div>
</body>
</html>
