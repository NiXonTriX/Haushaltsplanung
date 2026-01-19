<?php
declare(strict_types=1);

require_once __DIR__ . '/db/bookings.php';
require_once __DIR__ . '/db/db.php';

$pdo = Database::getConnection();
$repo = new Bookings();

function euroToCents(string $value): int {
    $value = trim($value);
    $value = str_replace(['€', ' '], '', $value);
    $value = str_replace('.', '', $value);      // Tausenderpunkte raus
    $value = str_replace(',', '.', $value);     // Komma->Punkt
    return (int) round(((float)$value) * 100);
}

// Dropdown-Daten laden
$persons = $pdo->query("SELECT person_id, display_name FROM persons ORDER BY display_name")->fetchAll(PDO::FETCH_ASSOC);
$accounts = $pdo->query("SELECT account_id, account_name FROM accounts ORDER BY account_name")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("
    SELECT c.category_id, c.category_name, p.category_name AS parent_name
    FROM categories c
    LEFT JOIN categories p ON c.parent_id = p.category_id
    ORDER BY COALESCE(p.category_name, c.category_name), c.category_name
")->fetchAll(PDO::FETCH_ASSOC);
$payees = $pdo->query("SELECT payee_id, payee_name FROM payees ORDER BY payee_name")->fetchAll(PDO::FETCH_ASSOC);

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $bookingDate = trim($_POST['booking_date'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $amount = trim($_POST['amount'] ?? '0');
        $direction = trim($_POST['direction'] ?? 'expense'); // income/expense
        $costType = trim($_POST['cost_type'] ?? 'variable'); // fixed/variable/unexpected
        $status = trim($_POST['status'] ?? 'posted');        // planned/due/booked/posted/cancelled

        $categoryId = ($_POST['category_id'] ?? '') !== '' ? (int)$_POST['category_id'] : null;
        $accountId  = ($_POST['account_id'] ?? '') !== '' ? (int)$_POST['account_id'] : null;
        $personId   = ($_POST['person_id'] ?? '') !== '' ? (int)$_POST['person_id'] : null;
        $payeeId    = ($_POST['payee_id'] ?? '') !== '' ? (int)$_POST['payee_id'] : null;

        if ($bookingDate === '' || $title === '') {
            throw new RuntimeException('Bitte Datum und Beschreibung ausfüllen.');
        }

        $amountCents = euroToCents($amount);

        // optional: wenn user "Ausgabe" gewählt hat, aber positiver Betrag: ist ok
        // wir lassen direction das "Vorzeichen" sein; Betrag bleibt immer positiv.
        $newId = $repo->insert([
            'booking_date' => $bookingDate,
            'title' => $title,
            'description' => $description !== '' ? $description : null,
            'amount_cents' => abs($amountCents),
            'currency' => 'EUR',
            'direction' => $direction,
            'cost_type' => $costType,
            'status' => $status,
            'category_id' => $categoryId,
            'account_id' => $accountId,
            'person_id' => $personId,
            'payee_id' => $payeeId,
        ]);

        // Relativer Pfad zur Buchungsdetail-Seite + Erfolgsmeldung
        header('Location: ./booking-detail.php?id=' . $newId . '&success=created');
        exit;

    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}


// --- Direction Badge ---
$dir = $booking['direction'] ?? 'expense';
$directionMap = [
  'income'  => ['Einnahme', 'badge-success'],
  'expense' => ['Ausgabe',  'badge-danger'],
];
[$directionLabel, $directionBadgeClass] = $directionMap[$dir] ?? ['—', 'badge-secondary'];

// --- Status Badge ---
$st = $booking['status'] ?? 'posted';
$statusMap = [
  'planned'   => ['geplant',   'badge-warning'],
  'due'       => ['fällig',    'badge-info'],
  'booked'    => ['gebucht',   'badge-success'],
  'posted'    => ['verbucht',  'badge-primary'],
  'cancelled' => ['storniert', 'badge-secondary'],
];
[$statusLabel, $statusClass] = $statusMap[$st] ?? ['unbekannt', 'badge-secondary'];


// Message-Helfer -> Flash-Messages über GET-Parameter
function flashSuccessFromGet(): ?string {
    $key = $_GET['success'] ?? '';
    return match ($key) {
        'created' => '✅ Buchung wurde erfolgreich angelegt.',
        'updated' => '✅ Änderungen wurden erfolgreich gespeichert.',
        'deleted' => '✅ Buchung wurde erfolgreich gelöscht.',
        default => null,
    };
}

function flashErrorFromGet(): ?string {
    $key = $_GET['error'] ?? '';
    return match ($key) {
        'notfound' => '⚠️ Buchung wurde nicht gefunden.',
        'invalid'  => '⚠️ Ungültige Eingaben.',
        default => null,
    };
}

$successMsg = flashSuccessFromGet();
$errorMsg   = flashErrorFromGet();
?>


<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Haushaltsplaner Übersicht">
  <meta name="author" content="Haushaltsplaner">

  <title>Haushaltsplaner | Neue Buchung</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link
    href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
    rel="stylesheet">

  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <link href="css/household.css" rel="stylesheet">
</head>

<body id="page-top" data-page="bookings">
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
          <i class="fas fa-wallet" aria-hidden="true"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Haushalt</div>
      </a>

      <hr class="sidebar-divider my-0">

      <li class="nav-item" data-page="dashboard">
        <a class="nav-link" href="index.html" data-page="dashboard" aria-label="Dashboard öffnen">
          <i class="fas fa-fw fa-chart-pie" aria-hidden="true"></i>
          <span>Dashboard</span></a>
      </li>

      <li class="nav-item" data-page="bookings">
        <a class="nav-link" href="bookings.php" data-page="bookings" aria-label="Seite Buchungen öffnen">
          <i class="fas fa-fw fa-list-ul" aria-hidden="true"></i>
          <span>Buchungen</span></a>
      </li>

      <li class="nav-item" data-page="categories">
        <a class="nav-link" href="categories.html" data-page="categories" aria-label="Seite Kategorien öffnen">
          <i class="fas fa-fw fa-tags" aria-hidden="true"></i>
          <span>Kategorien</span></a>
      </li>

      <li class="nav-item" data-page="accounts">
        <a class="nav-link" href="accounts.html" data-page="accounts" aria-label="Seite Konten öffnen">
          <i class="fas fa-fw fa-university" aria-hidden="true"></i>
          <span>Konten</span></a>
      </li>

      <li class="nav-item" data-page="people">
        <a class="nav-link" href="people.html" data-page="people" aria-label="Seite Personen und Tiere öffnen">
          <i class="fas fa-fw fa-user-friends" aria-hidden="true"></i>
          <span>Personen &amp; Tiere</span></a>
      </li>

      <li class="nav-item" data-page="payees">
        <a class="nav-link" href="payees.html" data-page="payees" aria-label="Seite Empfänger öffnen">
          <i class="fas fa-fw fa-store" aria-hidden="true"></i>
          <span>Empfänger</span></a>
      </li>

      <li class="nav-item" data-page="reports">
        <a class="nav-link" href="reports.html" data-page="reports" aria-label="Seite Auswertungen öffnen">
          <i class="fas fa-fw fa-chart-line" aria-hidden="true"></i>
          <span>Auswertungen</span></a>
      </li>

      <hr class="sidebar-divider d-none d-md-block">

      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle" type="button"
          aria-label="Sidebar ein- oder ausklappen">
        </button>
      </div>
    </ul>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3" type="button"
            aria-label="Sidebar ein- oder ausklappen">
            <i class="fa fa-bars" aria-hidden="true"></i>
          </button>

          <form class="form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
            <div class="input-group">
              <input type="text" class="form-control bg-light border-0 small" placeholder="Schnellsuche in Buchungen"
                aria-label="Schnellsuche in Buchungen" aria-describedby="basic-addon2">
              <div class="input-group-append">
                <button class="btn btn-primary" type="button" aria-label="Suche starten">
                  <i class="fas fa-search fa-sm" aria-hidden="true"></i>
                </button>
              </div>
            </div>
          </form>

          <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown no-arrow d-sm-none">
              <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false" aria-label="Suchmenü öffnen">
                <i class="fas fa-search fa-fw" aria-hidden="true"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                  <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Schnellsuche"
                      aria-label="Schnellsuche" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                      <button class="btn btn-primary" type="button" aria-label="Suche starten">
                        <i class="fas fa-search fa-sm" aria-hidden="true"></i>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </li>

            <div class="topbar-divider d-none d-sm-block"></div>

            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false" aria-label="Benutzermenü öffnen">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Haushalt</span>
                <i class="fas fa-home" aria-hidden="true"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="accounts.html">
                  <i class="fas fa-university fa-sm fa-fw mr-2 text-gray-400" aria-hidden="true"></i>
                  Konten
                </a>
                <a class="dropdown-item" href="people.html">
                  <i class="fas fa-users fa-sm fa-fw mr-2 text-gray-400" aria-hidden="true"></i>
                  Personen &amp; Tiere
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="reports.html">
                  <i class="fas fa-chart-line fa-sm fa-fw mr-2 text-gray-400" aria-hidden="true"></i>
                  Auswertungen
                </a>
              </div>
            </li>
          </ul>

        </nav>

        <!-- Top-Bar END -->

        <div class="container-fluid">
          <!-- Insert/Update/Delete - Alerts -->
          <?php if (!empty($successMsg)) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?php echo htmlspecialchars($successMsg); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Schließen">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>

          <?php if (!empty($errorMsg)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?php echo htmlspecialchars($errorMsg); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Schließen">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>

          <?php if (!empty($error)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              ❌ <?php echo htmlspecialchars($error); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Schließen">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>
          <!-- END - Insert/Update/Delete - Alerts -->
          <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div>
              <h1 class="h3 mb-0 text-gray-800">Buchung anlegen</h1>
              <p class="text-muted mb-0">Erfasse eine neue Einnahme oder Ausgabe mit allen relevanten Informationen.</p>
            </div>
            <a href="bookings.php" class="btn btn-outline-secondary btn-sm mt-3 mt-md-0"
              aria-label="Zurück zur Buchungsliste">
              <i class="fas fa-arrow-left mr-1" aria-hidden="true"></i>
              Zurück zur Liste
            </a>
          </div>

          <div class="row">
            <div class="col-lg-8">
              <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Buchungsdetails</h6>
                  <!-- Ausrichtung - Rechtsbündig -->
                  <div class="d-flex align-items-center">
                    <!-- Dynamische -> Einnahme / Ausgabe -->
                    <span id="directionBadge" class="badge badge-pill <?php echo htmlspecialchars($directionBadgeClass ?? 'badge-secondary'); ?> mr-2">
                      <?php echo htmlspecialchars($directionLabel ?? '—'); ?>
                    </span>
                  <span class="badge badge-primary">neu</span>
                  </div>
                </div>
                <div class="card-body">

                  <!-- POST-Methode, um Buchung in Datenbank zu speichern -->
                  <form id="bookingForm" method="post" action="booking-new.php" novalidate>
                    <!-- optional: einfacher Action-Flag -->
                    <input type="hidden" name="action" value="create">

                    <div class="form-row">
                      <div class="form-group col-md-4">
                        <label for="bookingDate">Datum</label>
                        <input type="date" class="form-control" id="bookingDate" name="booking_date"
                          value="<?php echo htmlspecialchars($_POST['booking_date'] ?? date('Y-m-d')); ?>">
                      </div>

                      <div class="form-group col-md-8">
                        <label for="bookingTitle">Beschreibung</label>
                        <input type="text" class="form-control" id="bookingTitle" name="title"
                          placeholder="z. B. Gehalt Februar"
                          value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                      </div>
                    </div>

                    <div class="form-row">
                      <div class="form-group col-md-4">
                        <label for="bookingAmount">Betrag</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text" aria-hidden="true">€</span>
                          </div>
                          <!-- Wichtig: name="amount" (nicht bookingAmount) -->
                          <input type="text" class="form-control" id="bookingAmount" name="amount"
                            placeholder="z. B. 123,45"
                            value="<?php echo htmlspecialchars($_POST['amount'] ?? '0,00'); ?>">
                        </div>
                        <small class="form-text text-muted">Einnahmen positiv, Ausgaben über Auswahl rechts (nicht über negative Werte).</small>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="bookingCategory">Kategorie</label>
                        <!-- Wichtig: name="category_id" + value = ID -->
                        <select class="form-control" id="bookingCategory" name="category_id">
                          <option value="">Bitte wählen…</option>
                          <?php if (!empty($categories)) : ?>
                            <?php foreach ($categories as $c) : ?>
                              <?php
                                $label = !empty($c['parent_name'])
                                  ? $c['parent_name'] . ' > ' . $c['category_name']
                                  : $c['category_name'];
                                $selected = ((string)($c['category_id']) === (string)($_POST['category_id'] ?? '')) ? 'selected' : '';
                              ?>
                              <option value="<?php echo (int)$c['category_id']; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($label); ?>
                              </option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                      </div>

                      <div class="form-group col-md-4">
                        <label>Einnahme / Ausgabe</label>
                        <?php $dir = $booking['direction'] ?? 'expense'; ?>
                        <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons" id="detailDirection">
                          <label class="btn btn-light <?php echo ($dir === 'income') ? 'active' : ''; ?>">
                            <input type="radio" name="direction" value="income" <?php echo ($dir === 'income') ? 'checked' : ''; ?>>
                            Einnahme
                          </label>
                          <label class="btn btn-light <?php echo ($dir === 'expense') ? 'active' : ''; ?>">
                            <input type="radio" name="direction" value="expense" <?php echo ($dir === 'expense') ? 'checked' : ''; ?>>
                            Ausgabe
                          </label>
                        </div>
                      </div>
                    </div>

                    <div class="form-row">
                      <div class="form-group col-md-4">
                        <label for="bookingCostType">Kostenart</label>
                        <select class="form-control" id="bookingCostType" name="cost_type">
                          <?php $ct = $_POST['cost_type'] ?? 'variable'; ?>
                          <option value="fixed" <?php echo ($ct === 'fixed') ? 'selected' : ''; ?>>fix</option>
                          <option value="variable" <?php echo ($ct === 'variable') ? 'selected' : ''; ?>>variabel</option>
                          <option value="unexpected" <?php echo ($ct === 'unexpected') ? 'selected' : ''; ?>>unerwartet</option>
                        </select>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="bookingStatus">Status</label>
                        <select class="form-control" id="bookingStatus" name="status">
                          <?php $st = $_POST['status'] ?? 'posted'; ?>
                          <option value="planned" <?php echo ($st === 'planned') ? 'selected' : ''; ?>>geplant</option>
                          <option value="due" <?php echo ($st === 'due') ? 'selected' : ''; ?>>fällig</option>
                          <option value="booked" <?php echo ($st === 'booked') ? 'selected' : ''; ?>>gebucht</option>
                          <option value="posted" <?php echo ($st === 'posted') ? 'selected' : ''; ?>>verbucht</option>
                          <option value="cancelled" <?php echo ($st === 'cancelled') ? 'selected' : ''; ?>>storniert</option>
                        </select>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="bookingPerson">Person</label>
                        <select class="form-control" id="bookingPerson" name="person_id">
                          <option value="">Bitte wählen…</option>
                          <?php if (!empty($persons)) : ?>
                            <?php foreach ($persons as $p) : ?>
                              <?php
                                $selected = ((string)($p['person_id']) === (string)($_POST['person_id'] ?? '')) ? 'selected' : '';
                              ?>
                              <option value="<?php echo (int)$p['person_id']; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($p['display_name']); ?>
                              </option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-row">
                      <div class="form-group col-md-6">
                        <label for="bookingAccount">Konto</label>
                        <select class="form-control" id="bookingAccount" name="account_id">
                          <option value="">Bitte wählen…</option>
                          <?php if (!empty($accounts)) : ?>
                            <?php foreach ($accounts as $a) : ?>
                              <?php
                                $selected = ((string)($a['account_id']) === (string)($_POST['account_id'] ?? '')) ? 'selected' : '';
                              ?>
                              <option value="<?php echo (int)$a['account_id']; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($a['account_name']); ?>
                              </option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                      </div>

                      <div class="form-group col-md-6">
                        <label for="bookingPayee">Empfänger / Zahler</label>
                        <!-- Empfehlung: Dropdown mit payee_id -->
                        <select class="form-control" id="bookingPayee" name="payee_id">
                          <option value="">Bitte wählen…</option>
                          <?php if (!empty($payees)) : ?>
                            <?php foreach ($payees as $pay) : ?>
                              <?php
                                $selected = ((string)($pay['payee_id']) === (string)($_POST['payee_id'] ?? '')) ? 'selected' : '';
                              ?>
                              <option value="<?php echo (int)$pay['payee_id']; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($pay['payee_name']); ?>
                              </option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                        <small class="form-text text-muted">Falls du lieber Text willst: dann musst du beim Speichern payee anlegen/finden.</small>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="bookingNote">Notiz</label>
                      <textarea class="form-control" id="bookingNote" name="description" rows="3"
                        placeholder="Zusätzliche Details oder Verknüpfungen"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                      <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="bookingRecurring" name="is_recurring" value="1"
                          <?php echo !empty($_POST['is_recurring']) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="bookingRecurring">Als wiederkehrend speichern</label>
                      </div>
                      <div>
                        <a href="bookings.php" class="btn btn-outline-secondary mr-2" aria-label="Buchung abbrechen">Abbrechen</a>
                        <button type="submit" class="btn btn-primary" aria-label="Buchung speichern">Buchung speichern</button>
                      </div>
                    </div>
                  </form>
                  <!-- Form END -->

                </div>
              </div>
            </div>

            <div class="col-lg-4">
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Tipps zur Erfassung</h6>
                </div>
                <div class="card-body">
                  <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex">
                      <span class="badge badge-pill badge-info mr-2">1</span>
                      <div>
                        <strong>Beschreibung klar halten.</strong>
                        <div class="text-gray-600">Nutze Namen wie "Miete Februar" oder "Supermarkt Wochenende".</div>
                      </div>
                    </li>
                    <li class="mb-3 d-flex">
                      <span class="badge badge-pill badge-info mr-2">2</span>
                      <div>
                        <strong>Kategorie auswählen.</strong>
                        <div class="text-gray-600">So bleiben Auswertungen übersichtlich und vergleichbar.</div>
                      </div>
                    </li>
                    <li class="d-flex">
                      <span class="badge badge-pill badge-info mr-2">3</span>
                      <div>
                        <strong>Einnahme vs. Ausgabe.</strong>
                        <div class="text-gray-600">Einnahmen erscheinen grün, Ausgaben rot – analog zur Übersichtstabelle.</div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

        </div>

        <footer class="sticky-footer bg-white">
          <div class="container my-auto">
            <div class="copyright text-center my-auto">
              <span>Haushaltsplaner &copy; Dein Haushalt 2025</span>
            </div>
          </div>
        </footer>

      </div>

    </div>

    <a class="scroll-to-top rounded" href="#page-top" aria-label="Zurück nach oben">
      <i class="fas fa-angle-up" aria-hidden="true"></i>
    </a>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <!-- Validation-Framework -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/validate.js/0.13.1/validate.min.js"></script>
    <script src="js/booking-detail.min.js"></script>
    <script src="js/sucessAlert.min.js"></script>
</body>

</html>
