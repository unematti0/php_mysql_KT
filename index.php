<!DOCTYPE html>
<html lang="et">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mattias Elmers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>
<body>

<?php
// lehe kontroll
if (!empty($_GET['leht'])) {
    $leht = $_GET['leht'];
    $lubatud = array('Avaleht', 'minust', 'kontakt', 'admin');
    $kontroll = in_array($leht, $lubatud);
    if (!$kontroll) {
        $leht = 'Avaleht';
    }
} else {
    $leht = 'Avaleht'; 
}

?>
<!-- navbar -->
<nav class="navbar navbar-expand-lg navbar-dark position-absolute top-0 start-0 end-0 bg-transparent">
  <div class="container">

    <a class="navbar-brand text-white" href="index.php?leht=Avaleht">Mattias Elmers</a>

    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
      <span class="navbar-toggler-icon"></span>
    </button>
      <div class="collapse navbar-collapse" id="navmenu">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link text-white" href="index.php?leht=Avaleht">Avaleht</a></li>
           
            <li class="nav-item"><a class="nav-link text-white" href="admin.php">Admin</a></li>
      </ul>
      </div>
  </div>
</nav>

<!-- jumbo -->
<div class="d-flex align-items-center justify-content-center text-white text-center" style="height: 400px; background-image: url('workshop.jpg'); background-size: cover; background-position: center;">
  <div>
    <h1 class="display-4 fw-bold">Auto Parandus</h1>
    <p class="lead">Parim maailmas</p>
  </div>
</div>

<!-- sisu -->
<?php
  // Kuvame teated, kui neid on
  if (!empty($alert)) echo $alert;
    ?>
  <!-- Broneeringu vorm -->
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
      <input type="text" name="isikukood" class="form-control" pattern="\d{11}" maxlength="11" required>
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
        // Teenuste valik
        include("config.php");
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
        // Töökohtade valik
        $tk = mysqli_query($yhendus, "SELECT * FROM tookoht");
        while ($r = mysqli_fetch_assoc($tk)) {
            echo "<option value='".$r['id']."'>".$r['nimi']."</option>";
        }
        ?>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label">Kuupäev</label>
      <input type="date" name="kuupäev" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Algusaeg</label>
      <input 
        type="time" 
        name="kellaaeg" 
        class="form-control" 
        required
        <?php
          // Kui valitud kuupäev on täna, sea min väärtus praegune kellaaeg
          $min_time = "";
          if (isset($_POST['kuupäev']) && $_POST['kuupäev'] == date('Y-m-d')) {
              $min_time = date('H:i');
          }
          if (!isset($_POST['kuupäev']) && date('Y-m-d') == date('Y-m-d')) {
              $min_time = date('H:i');
          }
          if ($min_time) {
              echo "min=\"$min_time\"";
          }
        ?>
      >
    </div>

    <div class="col-12">
      <button type="submit" name="submit" class="btn btn-primary">Broneeri aeg</button>
    </div>
  </form>

  <hr class="my-5">
  <h2>Olemasolevad broneeringud</h2>
  <table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Klient</th>
            <th>Teenus</th>
            <th>Kuupäev</th>
            <th>Kellaaeg</th>
            <th>Töökoht</th>
            <th>Tegevus</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Kuvame kõik broneeringud ja vajadusel muutmise vormi
        $broneeringud = "
            SELECT b.id, b.kuupaev, b.algus_kellaaeg, 
                   k.eesnimi, k.perekonnanimi, 
                   t.nimi AS teenus, 
                   tk.nimi AS tookoht
            FROM broneering b
            JOIN klient k ON b.klient_id = k.id
            JOIN teenus t ON b.teenus_id = t.id
            JOIN tookoht tk ON b.tookoht_id = tk.id
            ORDER BY b.kuupaev DESC, b.algus_kellaaeg DESC
            LIMIT 50
        ";

        $tulemus = mysqli_query($yhendus, $broneeringud);

        // Muutmise vormi kuvamine
        $muuda_id = isset($_GET['muuda']) ? intval($_GET['muuda']) : 0;

        while ($rida = mysqli_fetch_assoc($tulemus)) {
            $broneeringu_aeg = strtotime($rida['kuupaev'] . ' ' . $rida['algus_kellaaeg']);
            $saab_muuta = ($broneeringu_aeg - time() >= 24 * 3600);

            if ($muuda_id === intval($rida['id']) && $saab_muuta) {
                // Muutmise vorm
                echo "<tr>
                <form method='post'>
                  <td>{$rida['eesnimi']} {$rida['perekonnanimi']}<input type='hidden' name='broneering_id' value='{$rida['id']}'></td>
                  <td>{$rida['teenus']}</td>
                  <td><input type='date' name='muuda_kuupaev' value='{$rida['kuupaev']}' class='form-control form-control-sm' required></td>
                  <td><input type='time' name='muuda_kellaaeg' value='{$rida['algus_kellaaeg']}' class='form-control form-control-sm' required></td>
                  <td>{$rida['tookoht']}</td>
                  <td>
                    <button type='submit' name='muuda_broneering' class='btn btn-success btn-sm'>Salvesta</button>
                    <a href='index.php' class='btn btn-secondary btn-sm'>Tühista</a>
                  </td>
                </form>
                </tr>";
            } else {
                echo "<tr>";
                echo "<td>".$rida['eesnimi']." ".$rida['perekonnanimi']."</td>";
                echo "<td>".$rida['teenus']."</td>";
                echo "<td>".$rida['kuupaev']."</td>";
                echo "<td>".$rida['algus_kellaaeg']."</td>";
                echo "<td>".$rida['tookoht']."</td>";
                echo "<td>";
                if ($saab_muuta) {
                    echo "<a href='?muuda={$rida['id']}' class='btn btn-warning btn-sm'>Muuda</a> ";
                    echo "<a href='?kustuta=1&id={$rida['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Kas oled kindel, et soovid selle broneeringu tühistada?');\">Tühista</a>";
                } else {
                    echo "<span class='text-muted'>Muuta/tühistada ei saa</span>";
                }
                echo "</td>";
                echo "</tr>";
            }
        }
        ?>


<?php
// ===================== PHP LOGIKA =====================

/**
 * 1. Valideerimisfunktsioonid
 * - is_valid_email: kontrollib, kas email on korrektne
 * - is_valid_isikukood: kontrollib, kas isikukood on täpselt 11 numbrit
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
function is_valid_isikukood($isikukood) {
    return preg_match('/^\d{11}$/', $isikukood);
}

$alert = "";

/**
 * 2. Broneeringu tühistamine (ainult kui vähemalt 24h enne aega)
 * - Kontrollib, kas broneeringut saab tühistada (vähemalt 24h enne aega)
 * - Kui saab, kustutab broneeringu
 */
if (isset($_GET['kustuta']) && isset($_GET['id'])) {
    $broneering_id = intval($_GET['id']);
    $broneering = mysqli_query($yhendus, "SELECT kuupaev, algus_kellaaeg FROM broneering WHERE id=$broneering_id");
    if ($row = mysqli_fetch_assoc($broneering)) {
        $broneeringu_aeg = strtotime($row['kuupaev'] . ' ' . $row['algus_kellaaeg']);
        if ($broneeringu_aeg - time() >= 24 * 3600) {
            mysqli_query($yhendus, "DELETE FROM broneering WHERE id=$broneering_id");
            $alert = '<div class="alert alert-success">Broneering tühistatud!</div>';
        } else {
            $alert = '<div class="alert alert-danger">Broneeringut saab tühistada ainult kuni 24 tundi enne aega!</div>';
        }
    }
}

/**
 * 3. Broneeringu muutmine (ainult kui vähemalt 24h enne aega)
 * - Kontrollib, kas broneeringut saab muuta (vähemalt 24h enne aega)
 * - Kontrollib, et uus aeg poleks minevikus ega kattuks teise broneeringuga
 * - Uuendab broneeringu aega
 */
if (isset($_POST["muuda_broneering"])) {
    $broneering_id = intval($_POST["broneering_id"]);
    $uus_kuupaev = mysqli_real_escape_string($yhendus, $_POST["muuda_kuupaev"]);
    $uus_kellaaeg = mysqli_real_escape_string($yhendus, $_POST["muuda_kellaaeg"]);

    $broneering = mysqli_query($yhendus, "SELECT kuupaev, algus_kellaaeg FROM broneering WHERE id=$broneering_id");
    if ($row = mysqli_fetch_assoc($broneering)) {
        $broneeringu_aeg = strtotime($row['kuupaev'] . ' ' . $row['algus_kellaaeg']);
        if ($broneeringu_aeg - time() < 24 * 3600) {
            $alert = '<div class="alert alert-danger">Broneeringut saab muuta ainult kuni 24 tundi enne aega!</div>';
        } else {
            $uus_aeg = strtotime($uus_kuupaev . ' ' . $uus_kellaaeg);
            if ($uus_aeg < time()) {
                $alert = '<div class="alert alert-danger">Minevikku ei saa aega muuta.</div>';
            } else {
                $b = mysqli_query($yhendus, "SELECT id FROM broneering WHERE kuupaev='$uus_kuupaev' AND algus_kellaaeg='$uus_kellaaeg' AND id!=$broneering_id");
                if (mysqli_num_rows($b) > 0) {
                    $alert = '<div class="alert alert-danger">Sellel ajal on juba broneering!</div>';
                } else {
                    mysqli_query($yhendus, "UPDATE broneering SET kuupaev='$uus_kuupaev', algus_kellaaeg='$uus_kellaaeg' WHERE id=$broneering_id");
                    $alert = '<div class="alert alert-success">Broneeringut uuendati!</div>';
                }
            }
        }
    }
}

/**
 * 4. Uue broneeringu lisamine
 * - Kontrollib, et aeg poleks minevikus
 * - Kontrollib isikukoodi ja emaili korrektsust
 * - Kontrollib emaili unikaalsust
 * - Kontrollib, et töökoht oleks vaba
 * - Lisab vajadusel uue kliendi
 * - Lisab broneeringu
 */
if (isset($_POST["submit"])) {
    $eesnimi = mysqli_real_escape_string($yhendus, $_POST["eesnimi"]);
    $perekonnanimi = mysqli_real_escape_string($yhendus, $_POST["perekonnanimi"]);
    $isikukood = mysqli_real_escape_string($yhendus, $_POST["isikukood"]);
    $email = mysqli_real_escape_string($yhendus, $_POST["email"]);
    $teenus_id = intval($_POST["teenus"]);
    $tookoht_id = intval($_POST["töökoht"]);
    $kuupaev = mysqli_real_escape_string($yhendus, $_POST["kuupäev"]);
    $kellaaeg = mysqli_real_escape_string($yhendus, $_POST["kellaaeg"]);

    $broneeringu_aeg = strtotime($kuupaev . ' ' . $kellaaeg);

    // Valideerimised
    if ($broneeringu_aeg < time()) {
        $alert = '<div class="alert alert-danger">Minevikku ei saa aega broneerida.</div>';
    } elseif (!is_valid_isikukood($isikukood)) {
        $alert = '<div class="alert alert-danger">Isikukood peab olema 11 numbrit.</div>';
    } elseif (!is_valid_email($email)) {
        $alert = '<div class="alert alert-danger">E-posti aadress ei ole korrektne.</div>';
    } else {
        // E-posti unikaalsuse kontroll
        $email_q = "SELECT id FROM klient WHERE email='$email'";
        $email_r = mysqli_query($yhendus, $email_q);
        if (mysqli_num_rows($email_r) > 0) {
            $alert = '<div class="alert alert-danger">See e-posti aadress on juba kasutusel.</div>';
        } else {
            // Kontroll kas töökoht on sel ajal vaba
            $kontroll = "SELECT * FROM broneering WHERE tookoht_id=$tookoht_id AND kuupaev='$kuupaev' AND algus_kellaaeg='$kellaaeg'";
            $kontroll_tulemus = mysqli_query($yhendus, $kontroll);

            if (mysqli_num_rows($kontroll_tulemus) > 0) {
                $alert = '<div class="alert alert-danger">Valitud töökoht on sellel ajal juba broneeritud.</div>';
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

                $insert_bron = "INSERT INTO broneering (klient_id, teenus_id, tookoht_id, kuupaev, algus_kellaaeg)
                                VALUES ($klient_id, $teenus_id, $tookoht_id, '$kuupaev', '$kellaaeg')";
                mysqli_query($yhendus, $insert_bron);

                $alert = '<div class="alert alert-success">Broneering edukalt salvestatud!</div>';
            }
        }
    }
}
?>
  




    </tbody>
  </table>


<footer class="text-center pt-4 mt-5 border-top">
  <p class="mb-4">Mattias Elmers</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>