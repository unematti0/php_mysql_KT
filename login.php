<?php 
session_start();
include('config.php');

if (isset($_SESSION['tuvastamine'])) {
    header('Location: admin.php');
    exit();
}

if (!empty($_POST['login']) && !empty($_POST['pass'])) {
    $login = mysqli_real_escape_string($yhendus, trim($_POST['login']));
    $pass = $_POST['pass'];

    $paring = "SELECT * FROM kasutaja WHERE kasutaja='$login'";
    $valjund = mysqli_query($yhendus, $paring);

    if ($valjund && mysqli_num_rows($valjund) === 1) {
        $user = mysqli_fetch_assoc($valjund);
        if (password_verify($pass, $user['parool'])) {
            $_SESSION['tuvastamine'] = $user['kasutaja'];
            header('Location: admin.php');
            exit();
        } else {
            $error = "Vale parool.";
        }
    } else {
        $error = "Kasutajat ei leitud.";
    }
}
?>
<!doctype html>
<html lang="et">
<head>
    <meta charset="utf-8">
    <title>Spordipäev 2025 - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h1>Logi sisse</h1>
    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="post" class="mb-3">
        <div class="mb-3">
            <label for="login" class="form-label">Kasutajanimi</label>
            <input type="text" name="login" id="login" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="pass" class="form-label">Parool</label>
            <input type="password" name="pass" id="pass" class="form-control" required>
        </div>
        <input type="submit" value="Logi sisse" class="btn btn-primary">

        <a href="index.php" class="btn btn-secondary">Tagasi avalehele</a>
 
    </form>
</body>
</html>
