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
        <a class="nav-link" href="bookings.html" data-page="bookings" aria-label="Seite Buchungen öffnen">
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
        <button 
          class="rounded-circle border-0" 
          id="sidebarToggle"
          type="button"
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

          <button 
            id="sidebarToggleTop" 
            class="btn btn-link d-md-none rounded-circle mr-3"
            type="button"
            aria-label="Sidebar ein- oder ausklappen">
            <i class="fa fa-bars" aria-hidden="true"></i>
          </button>

          <form class="form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
            <div class="input-group">
              <input type="text" class="form-control bg-light border-0 small" placeholder="Schnellsuche in Buchungen"
                aria-label="Schnellsuche in Buchungen" aria-describedby="basic-addon2">
              <div class="input-group-append">
                <button 
                  class="btn btn-primary" 
                  type="button"
                  aria-label="Suche starten">
                  <i class="fas fa-search fa-sm" aria-hidden="true"></i>
                </button>
              </div>
            </div>
          </form>

          <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown no-arrow d-sm-none">
              <a 
                class="nav-link dropdown-toggle" 
                href="#" 
                id="searchDropdown" 
                role="button" 
                data-toggle="dropdown"
                aria-haspopup="true" 
                aria-expanded="false"
                aria-label="Suchmenü öffnen">
                <i class="fas fa-search fa-fw" aria-hidden="true"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                  <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Schnellsuche"
                      aria-label="Schnellsuche" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                      <button 
                        class="btn btn-primary" 
                        type="button"
                        aria-label="Suche starten">
                        <i class="fas fa-search fa-sm" aria-hidden="true"></i>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </li>

            <div class="topbar-divider d-none d-sm-block"></div>

            <li class="nav-item dropdown no-arrow">
              <a 
                class="nav-link dropdown-toggle" 
                href="#" 
                id="userDropdown" 
                role="button" 
                data-toggle="dropdown"
                aria-haspopup="true" 
                aria-expanded="false"
                aria-label="Benutzermenü öffnen">
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
              <a href="booking-new.html" class="btn btn-primary btn-sm mr-2" aria-label="Neue Buchung anlegen">
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
                <button type="button" class="btn btn-outline-secondary active" aria-label="Filter Monat aktivieren">Monat</button>
                <button type="button" class="btn btn-outline-secondary" aria-label="Filter Quartal aktivieren">Quartal</button>
                <button type="button" class="btn btn-outline-secondary" aria-label="Filter Jahr aktivieren">Jahr</button>
              </div>

            </div>
          </div>

          <!-- Tabellenbereich (unverändert) -->
          
          <div class="card shadow mb-4">
            <div class="card-body">
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
                  <tbody>
                    <tr>
                      <td>02.02.2025</td>
                      <td><a href="booking-detail.html" class="font-weight-semibold text-primary">Miete Februar</a></td>
                      <td><span class="badge badge-light">Fixkosten</span> Wohnen</td>
                      <td>Alex — Gemeinschaftskonto</td>
                      <td>Hausverwaltung</td>
                      <td class="text-right font-weight-bold text-danger">€ 1.100,00</td>
                      <td><span class="badge badge-success">gebucht</span></td>
                    </tr>
                    <tr>
                      <td>06.02.2025</td>
                      <td><a href="booking-detail.html" class="font-weight-semibold text-primary">Wocheneinkauf</a></td>
                      <td><span class="badge badge-light">Variabel</span> Lebensmittel</td>
                      <td>Jamie — Kreditkarte</td>
                      <td>Supermarkt</td>
                      <td class="text-right font-weight-bold text-danger">€ 86,50</td>
                      <td><span class="badge badge-primary">verbucht</span></td>
                    </tr>
                    <tr>
                      <td>10.02.2025</td>
                      <td><a href="booking-detail.html" class="font-weight-semibold text-primary">Tierarzt Kontrolle</a>
                      <td><span class="badge badge-light">Tier</span> Gesundheit</td>
                      <td>Bella — Haustierkonto</td>
                      <td>Tierarztpraxis</td>
                      <td class="text-right font-weight-bold text-danger">€ 120,00</td>
                      <td><span class="badge badge-warning">geplant</span></td>
                    </tr>
                    <tr>
                      <td>12.02.2025</td>
                      <td>Energieabschlag</td>
                      <td><span class="badge badge-light">Fixkosten</span> Energie</td>
                      <td>Alex — Gemeinschaftskonto</td>
                      <td>Stadtwerke</td>
                      <td class="text-right font-weight-bold text-danger">€ 210,00</td>
                      <td><span class="badge badge-success">gebucht</span></td>
                    </tr>
                    <tr>
                      <td>15.02.2025</td>
                      <td>Haftpflichtversicherung</td>
                      <td><span class="badge badge-light">Fixkosten</span> Versicherung</td>
                      <td>Jamie — Gemeinschaftskonto</td>
                      <td>Versicherung</td>
                      <td class="text-right font-weight-bold text-danger">€ 230,00</td>
                      <td><span class="badge badge-info">fällig</span></td>
                    </tr>
                    <tr>
                      <td>20.04.2025</td>
                      <td><a href="booking-detail.html" class="font-weight-semibold text-primary">Geburtstagsgeschenk</a>
                      <td><span class="badge badge-light">Spezial</span>Geschenk</td>
                      <td>Alex — Privatkonto</td>
                      <td>Alex</td>
                      <td class="text-right font-weight-bold text-success">€ 330,00</td>
                      <td><span class="badge badge-warning">geplant</span></td>
                    </tr>
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
  <script src="js/household.min.js"></script>

</body>

</html>
