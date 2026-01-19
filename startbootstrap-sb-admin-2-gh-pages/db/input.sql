-- ------------------------------------------------------------
-- TESTDATEN
-- ------------------------------------------------------------

INSERT INTO persons (display_name, notes)
VALUES
('Alex',  'Hauptperson'),
('Jamie', 'Partner'),
('Bella', 'Haustier');

INSERT INTO accounts (account_name, account_type, institution, person_id)
VALUES
('Gemeinschaftskonto', 'giro',        'Sparkasse',    1),
('Privatkonto Alex',   'giro',        'Erste Bank',   1),
('Privatkonto Jamie',  'giro',        'Bank Austria', 2),
('Kreditkarte',        'credit_card', 'Visa',         2),
('Haustierkonto',      'other',       'Tierbedarf',   3);

-- Root-Kategorien
INSERT INTO categories (category_name, parent_id)
VALUES
('Fixkosten', NULL),
('Variabel',  NULL),
('Tier',      NULL),
('Spezial',   NULL);

-- Unterkategorien (Fixkosten/Variabel/Tier/Spezial)
INSERT INTO categories (category_name, parent_id)
VALUES
('Wohnen',        1),
('Energie',       1),
('Versicherung',  1),
('Lebensmittel',  2),
('Gesundheit',    3),
('Geschenk',      4),

-- NEU für Einkommen
('Gehalt',        4),
('Zinsen',        4),
('Bonus',         4);

INSERT INTO payees (payee_name, bank_name)
VALUES
('Hausverwaltung',   'Wohnbau GmbH'),
('Supermarkt',       'REWE'),
('Tierarztpraxis',   'VetCare'),
('Stadtwerke',       'Energie AG'),
('Versicherung',     'Allianz'),

-- NEU für Einkommen
('Arbeitgeber GmbH', 'Hausbank'),
('Bank Austria',     'Bank Austria');

-- Buchungen (Ausgaben + Einkommen, verschiedene Status)
INSERT INTO bookings (
    booking_date,
    amount_cents,
    currency,
    direction,
    cost_type,
    status,
    title,
    description,
    category_id,
    account_id,
    person_id,
    payee_id
) VALUES
-- AUSGABEN
('2025-02-02', 110000, 'EUR', 'expense', 'fixed',     'posted',   'Miete Februar',          'Monatliche Miete',           5, 1, 1, 1),
('2025-02-06',   8650, 'EUR', 'expense', 'variable',  'posted',   'Wocheneinkauf',         'Lebensmittel',               8, 4, 2, 2),
('2025-02-10',  12000, 'EUR', 'expense', 'unexpected','planned',  'Tierarzt Kontrolle',    'Routineuntersuchung',        9, 5, 3, 3),
('2025-02-12',  21000, 'EUR', 'expense', 'fixed',     'booked',   'Energieabschlag',       'Strom Februar',              6, 1, 1, 4),
('2025-02-15',  23000, 'EUR', 'expense', 'fixed',     'due',      'Haftpflichtversicherung','Jahresbeitrag',              7, 1, 2, 5),
('2025-04-20',  33000, 'EUR', 'expense', 'variable',  'cancelled','Geburtstagsgeschenk',   'Geschenk für Alex',         10, 2, 1, NULL),

-- EINNAHMEN
('2025-02-01', 245000, 'EUR', 'income',  'fixed',     'posted',   'Gehalt Februar',        'Monatsgehalt',              11, 1, 1, 6),
('2025-02-11',  46531, 'EUR', 'income',  'variable',  'posted',   'Zinsen',                'Zinsgutschrift',            12, 2, 1, 7),
('2025-02-18',  35000, 'EUR', 'income',  'variable',  'booked',   'Projektbonus',          'Einmaliger Bonus',          13, 1, 1, 6),
('2025-02-22',  12990, 'EUR', 'income',  'unexpected','planned',  'Rückerstattung',         'Versicherung Rückzahlung',  7,  1, 2, 5);
