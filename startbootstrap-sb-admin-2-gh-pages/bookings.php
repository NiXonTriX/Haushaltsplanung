<?php
declare(strict_types=1);

require_once __DIR__ . '/db/bookings.php';

// Die Filterwerte kommen per GET aus dem Formular.
$filters = [
  'date_from'  => trim($_GET['date_from'] ?? ''),
  'date_to'    => trim($_GET['date_to'] ?? ''),
  'amount_min' => trim($_GET['amount_min'] ?? ''),
  'amount_max' => trim($_GET['amount_max'] ?? ''),

  'category'   => trim($_GET['category'] ?? ''),
  'title'      => trim($_GET['title'] ?? ''),
  'payee'      => trim($_GET['payee'] ?? ''),
  'person'     => trim($_GET['person'] ?? ''),
  'account'    => trim($_GET['account'] ?? ''),

  'direction'  => trim($_GET['direction'] ?? ''),   // income/expense
  'cost_type'  => trim($_GET['cost_type'] ?? ''),   // fixed/variable/unexpected
  'status'     => trim($_GET['status'] ?? ''),      // planned/due/booked/posted/cancelled
];


$hasFilters = false;
foreach ($filters as $value) {
  if ($value !== '') {
    $hasFilters = true;
    break;
  }
}

$bookings = [];
$errorMessage = null;
// Platzhalter für Filteroptionen (DropDowns)
$categoryOptions = [];
$payeeOptions = [];
$personOptions = [];
$accountOptions = [];

try {
  $repo = new Bookings();
  $bookings = $hasFilters ? $repo->selectByFilter($filters) : $repo->selectAll();
  $optionSource = $hasFilters ? $repo->selectAll() : $bookings;

  $collectOptions = static function (array $rows, string $key): array {
    $values = [];
    foreach ($rows as $row) {
      $value = trim((string) ($row[$key] ?? ''));
      if ($value === '') {
        continue;
      }
      $values[$value] = true;
    }

    $options = array_keys($values);
    natcasesort($options);
    return array_values($options);
  };

  $categoryOptions = $collectOptions($optionSource, 'category');
  $payeeOptions = $collectOptions($optionSource, 'payee_name');
  $personOptions = $collectOptions($optionSource, 'person_name');
  $accountOptions = $collectOptions($optionSource, 'account_name');
  // $bookings = $hasFilters ? $repo->selectByFilter($filters) : $repo->selectAll();
} catch (Throwable $exception) {
  $errorMessage = $exception->getMessage();
}

?>
<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Haushaltsplaner Übersicht">
  <meta name="author" content="Haushaltsplaner">

  <title>Haushaltsplaner | Buchungen</title>

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
    <ul class="navbar-nav bg-gradient-secondary_new sidebar sidebar-dark accordion" id="accordionSidebar">
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

        <div class="container-fluid">
          <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div>
              <h1 class="h3 mb-0 text-gray-800">Buchungen</h1>
              <p class="text-muted mb-0">Alle Haushaltsbuchungen mit Zuordnung zu Konten, Kategorien und Empfängern</p>
            </div>
            <div class="mt-3 mt-md-0">
              <a href="booking-new.php" class="btn btn-primary btn-sm mr-2" aria-label="Neue Buchung anlegen">
                <i class="fas fa-plus mr-1" aria-hidden="true"></i>
                Buchung anlegen
              </a>
              <a href="#" class="btn btn-outline-secondary btn-sm" aria-label="Buchungen importieren">
                <i class="fas fa-file-import mr-1" aria-hidden="true"></i>
                Import
              </a>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Buchungsliste</h6>

              <div class="btn-group btn-group-sm" role="group" aria-label="Zeitraum filtern">
                <button type="button" class="btn btn-outline-secondary active"
                  aria-label="Filter Monat aktivieren">Monat</button>
                <button type="button" class="btn btn-outline-secondary"
                  aria-label="Filter Quartal aktivieren">Quartal</button>
                <button type="button" class="btn btn-outline-secondary"
                  aria-label="Filter Jahr aktivieren">Jahr</button>
              </div>

            </div>
          </div>

          <!-- Tabellenbereich (unverändert) -->

          <div class="card shadow mb-4">
            <div class="card-body">

              <!-- Alle Filterkriterien Ein- oder Ausblenden + Gesamtkosten -->
                <div class="booking-toolbar">
                <button id="toggleFilters" type="button"
                        class="btn btn-outline-secondary btn-sm"
                        data-toggle="collapse"
                        data-target="#filterPanel"
                        aria-expanded="true"
                        aria-controls="filterPanel">
                    <i class="fas fa-filter mr-1" aria-hidden="true"></i>
                    Filter ausblenden
                </button>

                <div class="booking-total">
                    <span class="label">Summe sichtbarer Buchungen</span>
                    <span id="bookingSum" class="value">€ 0,00</span>
                </div>
                </div>

              <!-- Alles in diesem Div wird durch betätigen ein- oder ausgeblendet -->
              <div id="filterPanel" class="collapse show mt-3">



                <!-- Filteroptionen für die Gesamte Buchungen -->
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-3">
                  <div class="btn-group btn-group-sm mb-2 mb-lg-0" role="group" aria-label="Buchungen nach Art filtern">
                    <button type="button" class="btn btn-primary" data-booking-filter="all" aria-pressed="true">
                      Alle Buchungen
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-booking-filter="income"
                      aria-pressed="false">
                      Nur Einnahmen
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-booking-filter="expense"
                      aria-pressed="false">
                      Nur Ausgaben
                    </button>
                  </div>

                  <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-end">
                    <!-- 
                  <button
                    type="button"
                    class="btn btn-link btn-sm p-0 mr-sm-3 mb-2 mb-sm-0 text-secondary"
                    id="toggleFilters"
                    aria-expanded="true"
                    aria-controls="bookingFilters"
                    aria-label="Allgemeine Filter ein- oder ausblenden">
                    Filterkriterien ausblenden
                  </button>
                  -->
                  </div>
                </div>


                <!-- Einfaches Filterformular, das per GET die PHP-Logik anspricht -->
                <form method="get" class="card card-body border-0 p-0 mb-3" aria-label="Filterformular">
                  <div class="form-row">
                    <div class="form-group col-md-3">
                      <label for="date_from">Datum von</label>
                      <input type="date" class="form-control" id="date_from" name="date_from"
                        value="<?php echo htmlspecialchars($filters['date_from']); ?>">
                    </div>
                    <div class="form-group col-md-3">
                      <label for="date_to">Datum bis</label>
                      <input type="date" class="form-control" id="date_to" name="date_to"
                        value="<?php echo htmlspecialchars($filters['date_to']); ?>">
                    </div>
                    <div class="form-group col-md-3">
                      <label for="amount_min">Betrag min.</label>
                      <input type="text" class="form-control" id="amount_min" name="amount_min" placeholder="z. B. 10,00"
                        value="<?php echo htmlspecialchars($filters['amount_min']); ?>">
                    </div>
                    <div class="form-group col-md-3">
                      <label for="amount_max">Betrag max.</label>
                      <input type="text" class="form-control" id="amount_max" name="amount_max" placeholder="z. B. 99,99"
                        value="<?php echo htmlspecialchars($filters['amount_max']); ?>">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-3">
                      <label for="category">Kategorie</label>
                      <!-- DropDown - "Kategorie" -->
                      <select class="form-control" id="category" name="category">
                        <option value="">Alle</option>
                        <?php foreach ($categoryOptions as $option) : ?>
                          <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $filters['category'] === $option ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($option); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="direction">Ein-/Ausgabe</label>
                        <select class="form-control" id="direction" name="direction">
                            <?php
                            $directionOptions = [
                            '' => 'Alle',
                            'income' => 'Einnahmen',
                            'expense' => 'Ausgaben',
                            ];
                            foreach ($directionOptions as $value => $label) {
                            $selected = ($filters['direction'] === $value) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($value) . "\" $selected>" . htmlspecialchars($label) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                      <label for="title">Beschreibung</label>
                      <input type="text" class="form-control" id="title" name="title" placeholder="z. B. Einkauf"
                        value="<?php echo htmlspecialchars($filters['title']); ?>">
                    </div>
                    <div class="form-group col-md-3">
                      <label for="payee">Empfänger</label>
                      <!-- DropDown - "Empfänger" -->
                      <select class="form-control" id="payee" name="payee">
                        <option value="">Alle</option>
                        <?php foreach ($payeeOptions as $option) : ?>
                          <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $filters['payee'] === $option ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($option); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-3">
                      <label for="person">Person</label>
                      <!-- DropDown - "Person" -->
                      <select class="form-control" id="person" name="person">
                        <option value="">Alle</option>
                        <?php foreach ($personOptions as $option) : ?>
                          <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $filters['person'] === $option ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($option); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group col-md-3">
                      <label for="account">Konto</label>
                      <!-- DropDown - "Konto" -->
                      <select class="form-control" id="account" name="account">
                        <option value="">Alle</option>
                        <?php foreach ($accountOptions as $option) : ?>
                          <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $filters['account'] === $option ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($option); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group col-md-6 d-flex align-items-end">
                      <div>
                        <button type="submit" class="btn btn-primary mr-2">Filtern</button>
                        <a href="bookings.php" class="btn btn-outline-secondary">Filter zurücksetzen</a>
                      </div>
                    </div>
                  </div>
                </form>

                <!-- Filterfunktion -->
                <div class="d-flex flex-wrap mb-3" role="group" aria-label="Buchungen nach Merkmalen filtern"
                  id="bookingFilters">
                  <button type="button" class="filter-chip" data-column="0">
                    <i class="fas fa-calendar-alt mr-2" aria-hidden="true"></i>
                    <span>Datum</span>
                  </button>

                  <button type="button" class="filter-chip" data-column="1">
                    <i class="fas fa-align-left mr-2" aria-hidden="true"></i>
                    <span>Beschreibung</span>
                  </button>

                  <!-- Betrag = Spalte 5 -->
                  <button type="button" class="filter-chip" data-column="5">
                    <i class="fas fa-euro-sign mr-2" aria-hidden="true"></i>
                    <span>Betrag</span>
                  </button>

                  <!-- Kategorie = Spalte 2 -->
                  <button type="button" class="filter-chip" data-column="2">
                    <i class="fas fa-tags mr-2" aria-hidden="true"></i>
                    <span>Kategorie</span>
                  </button>

                  <!-- Ein- & Ausgaben = "Spezial"-Sortierung, nutzen wir als eigenes Kriterium -->
                  <button type="button" class="filter-chip" data-column="4">
                    <i class="fas fa-exchange-alt mr-2" aria-hidden="true"></i>
                    <span>Ein- &amp; Ausgaben</span>
                  </button>
                </div>

              </div>
              <!-- ENDE Filter ein- oder ausklappen -->

              <!-- Tabelleninhalt -->
              <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                  <thead>
                    <tr>
                      <th>Datum</th>
                      <th>Beschreibung</th>
                      <th>Kategorie</th>
                      <th>Person / Konto</th>
                      <th>Empfänger</th>
                      <th class="text-right">Betrag</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                    <tbody id="bookingsTableBody">
                      <?php if ($errorMessage !== null) : ?>
                        <tr>
                          <td colspan="7" class="text-danger">
                            <?php echo htmlspecialchars($errorMessage); ?>
                          </td>
                        </tr>
                      <?php elseif (empty($bookings)) : ?>
                        <tr>
                          <td colspan="7" class="text-muted">Keine Buchungen gefunden.</td>
                        </tr>
                      <?php else : ?>
                        <?php foreach ($bookings as $booking) : ?>
                            <?php
                            $amountCents = (int) ($booking['amount_cents'] ?? 0);
                            $amount = number_format($amountCents / 100, 2, ',', '.');
                            $isIncome = ($booking['direction'] ?? 'expense') === 'income';

                            // ----------- Kategorie und Kategorie-Eltern ermitteln und Badge richtig färben -----------
                            $category       = trim((string)($booking['category'] ?? ''));
                            $categoryParent = trim((string)($booking['category_parent'] ?? ''));

                            // Parent -> Badge (Mapping)
                            $categoryParentMap = [
                                'Fixkosten' => ['Fixkosten', 'badge-secondary'],
                                'Variabel'  => ['Variabel',  'badge-primary'],
                                'Tier'      => ['Tier',      'badge-info'],
                                'Spezial'   => ['Spezial',   'badge-warning'],
                            ];

                            // Default, falls Parent leer/unbekannt
                            [$parentLabel, $parentClass] = $categoryParentMap[$categoryParent] ?? [$categoryParent, 'badge-secondary'];

                            // Text für Kategorie-Spalte bauen
                            if ($categoryParent !== '') {
                                $categoryText =
                                    '<span class="badge badge-pill ' . htmlspecialchars($parentClass) . ' mr-1">'
                                    . htmlspecialchars($parentLabel)
                                    . '</span> '
                                    . htmlspecialchars($category !== '' ? $category : '-');
                            } else {
                                $categoryText = htmlspecialchars($category !== '' ? $category : '-');
                            }
                            // ----------- ENDE Kategorie -----------

                            $personName = htmlspecialchars($booking['person_name'] ?? '-');
                            $accountName = htmlspecialchars($booking['account_name'] ?? '-');
                            ?>
                          <!-- Überprüft, ob es sich um eine Ausgabe oder Einnahme handelt -->
                          <tr data-booking-type="<?php echo htmlspecialchars($booking['direction'] ?? 'expense'); ?>">
                            <td><?php echo htmlspecialchars($booking['booking_date'] ?? ''); ?></td>
                            <td>
                            <!-- Titel klickbar machen, um über die Buchung-ID bearbeiten und löschen zu ermöglichen -->
                            <a href="booking-detail.php?id=<?php echo (int)($booking['booking_id'] ?? 0); ?>">
                              <?php echo htmlspecialchars($booking['title'] ?? ''); ?>
                            </a>
                          </td>
                            <td><?php echo $categoryText; ?></td>
                            <td><?php echo $personName; ?> — <?php echo $accountName; ?></td>
                            <td><?php echo htmlspecialchars($booking['payee_name'] ?? '-'); ?></td>
                            <td class="text-right font-weight-bold <?php echo $isIncome ? 'text-success' : 'text-danger'; ?>">
                              € <?php echo $amount; ?>
                            </td>
                            <!-- Dynamische Statusanzeige -->
                            <?php
                                $status = $booking['status'] ?? 'posted';

                                $statusMap = [
                                'planned'   => ['geplant',   'badge-warning'],
                                'due'       => ['fällig',    'badge-info'],
                                'booked'    => ['gebucht',   'badge-success'],
                                'posted'    => ['verbucht',  'badge-primary'],
                                'cancelled' => ['storniert', 'badge-secondary'],
                                ];

                                [$statusLabel, $statusClass] = $statusMap[$status] ?? ['verbucht', 'badge-primary'];
                                ?>
                                <td>
                                <span class="badge <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($statusLabel); ?>
                                </span>
                                </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                </table>
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
  <!-- Filter -->
  <script src="js/bookings.min.js"></script>
  <script src="js/household.min.js"></script>
  <script src="js/filter-chip.min.js"></script>
  <script src="js/filterOptions.min.js"></script>
</body>

</html>
