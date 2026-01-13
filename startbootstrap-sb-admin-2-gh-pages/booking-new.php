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
              <h1 class="h3 mb-0 text-gray-800">Buchung anlegen</h1>
              <p class="text-muted mb-0">Erfasse eine neue Einnahme oder Ausgabe mit allen relevanten Informationen.</p>
            </div>
            <a href="bookings.html" class="btn btn-outline-secondary btn-sm mt-3 mt-md-0" aria-label="Zurück zur Buchungsliste">
              <i class="fas fa-arrow-left mr-1" aria-hidden="true"></i>
              Zurück zur Liste
            </a>
          </div>

          <div class="row">
            <div class="col-lg-8">
              <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Buchungsdetails</h6>
                  <span class="badge badge-primary">neu</span>
                </div>
                <div class="card-body">
                  <form>
                    <div class="form-row">
                      <div class="form-group col-md-4">
                        <label for="bookingDate">Datum</label>
                        <input type="date" class="form-control" id="bookingDate" value="2025-02-15">
                      </div>
                      <div class="form-group col-md-8">
                        <label for="bookingTitle">Beschreibung</label>
                        <input type="text" class="form-control" id="bookingTitle" placeholder="z. B. Gehalt Februar">
                      </div>
                    </div>

                    <div class="form-row">
                      <div class="form-group col-md-4">
                        <label for="bookingAmount">Betrag</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text" aria-hidden="true">€</span>
                          </div>
                          <input type="number" class="form-control" id="bookingAmount" value="0.00" step="0.01">
                        </div>
                        <small class="form-text text-muted">Negative Werte werden als Ausgaben behandelt.</small>
                      </div>
                      <div class="form-group col-md-4">
                        <label for="bookingCategory">Kategorie</label>
                        <select class="form-control" id="bookingCategory">
                          <option>Lebensmittel</option>
                          <option>Wohnen</option>
                          <option>Mobilität</option>
                          <option>Freizeit</option>
                          <option>Einkommen</option>
                        </select>
                      </div>
                      <div class="form-group col-md-4">
                        <label for="bookingType">Einnahme / Ausgabe</label>
                        <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                          <label class="btn btn-light active">
                            <input type="radio" name="bookingType" id="typeIncome" checked> Einnahme
                          </label>
                          <label class="btn btn-light">
                            <input type="radio" name="bookingType" id="typeExpense"> Ausgabe
                          </label>
                        </div>
                      </div>
                    </div>

                    <div class="form-row">
                      <div class="form-group col-md-6">
                        <label for="bookingAccount">Konto</label>
                        <select class="form-control" id="bookingAccount">
                          <option>Gemeinschaftskonto</option>
                          <option>Kreditkarte</option>
                          <option>Rücklagen</option>
                          <option>Bar</option>
                        </select>
                      </div>
                      <div class="form-group col-md-6">
                        <label for="bookingPayee">Empfänger / Zahler</label>
                        <input type="text" class="form-control" id="bookingPayee" placeholder="z. B. Arbeitgeber, Supermarkt">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="bookingNote">Notiz</label>
                      <textarea class="form-control" id="bookingNote" rows="3" placeholder="Zusätzliche Details oder Verknüpfungen"></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                      <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="bookingRecurring">
                        <label class="custom-control-label" for="bookingRecurring">Als wiederkehrend speichern</label>
                      </div>
                      <div>
                        <a href="bookings.html" class="btn btn-outline-secondary mr-2" aria-label="Buchung abbrechen">Abbrechen</a>
                        <button type="button" class="btn btn-primary" aria-label="Buchung speichern">Buchung speichern</button>
                      </div>
                    </div>
                  </form>
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
  <script src="js/household.min.js"></script>

</body>

</html>