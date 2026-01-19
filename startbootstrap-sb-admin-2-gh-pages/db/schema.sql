-- Haushaltsplaner Datenbankschema
-- MySQL 8.0+ Syntax

DROP DATABASE IF EXISTS haushaltsplaner;
CREATE DATABASE haushaltsplaner;
USE haushaltsplaner;

CREATE TABLE persons (
    person_id        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    display_name     VARCHAR(100) NOT NULL,
    is_active        BOOLEAN NOT NULL DEFAULT TRUE,
    notes            VARCHAR(255)
);

CREATE TABLE accounts (
    account_id       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    account_name     VARCHAR(120) NOT NULL,
    account_type     ENUM('giro', 'savings', 'cash', 'paypal', 'credit_card', 'other') NOT NULL DEFAULT 'giro',
    institution      VARCHAR(120),
    person_id        BIGINT UNSIGNED,
    notes            VARCHAR(255),
    CONSTRAINT fk_accounts_persons
        FOREIGN KEY (person_id) REFERENCES persons(person_id)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE categories (
    category_id      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_name    VARCHAR(120) NOT NULL,
    parent_id        BIGINT UNSIGNED,
    CONSTRAINT fk_categories_parent
        FOREIGN KEY (parent_id) REFERENCES categories(category_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT uq_categories_parent_name UNIQUE (parent_id, category_name)
);

-- Optional helper to keep zweistufige Kategorien: only root or one parent
CREATE VIEW vw_category_levels AS
SELECT c.category_id,
       COALESCE(p.parent_id IS NOT NULL, FALSE) AS has_grandparent
FROM categories c
LEFT JOIN categories p ON c.parent_id = p.category_id;

CREATE TABLE payees (
    payee_id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payee_name       VARCHAR(150) NOT NULL,
    iban             VARCHAR(34),
    bic              VARCHAR(11),
    bank_name        VARCHAR(120),
    notes            VARCHAR(255)
);

CREATE TABLE bookings (
    booking_id       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_date     DATE NOT NULL,
    amount_cents     BIGINT NOT NULL,
    currency         CHAR(3) NOT NULL DEFAULT 'EUR',

    -- NEU: Einnahme/Ausgabe
    direction        ENUM('income', 'expense') NOT NULL DEFAULT 'expense',

    -- bleibt wie gewünscht: fixed/variable/unexpected gilt für income UND expense
    cost_type        ENUM('fixed', 'variable', 'unexpected') NOT NULL,

    -- NEU: Status für UI/Workflow
    status           ENUM('planned', 'due', 'booked', 'posted', 'cancelled') NOT NULL DEFAULT 'posted',

    title            VARCHAR(150) NOT NULL,
    description      TEXT,
    category_id      BIGINT UNSIGNED,
    account_id       BIGINT UNSIGNED,
    person_id        BIGINT UNSIGNED,
    payee_id         BIGINT UNSIGNED,
    created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_bookings_categories
        FOREIGN KEY (category_id) REFERENCES categories(category_id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_bookings_accounts
        FOREIGN KEY (account_id) REFERENCES accounts(account_id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_bookings_persons
        FOREIGN KEY (person_id) REFERENCES persons(person_id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_bookings_payees
        FOREIGN KEY (payee_id) REFERENCES payees(payee_id)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE booking_links (
    link_id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    source_booking_id BIGINT UNSIGNED NOT NULL,
    related_booking_id BIGINT UNSIGNED NOT NULL,
    relation_type     ENUM('installment', 'refund', 'reversal', 'adjustment', 'other') NOT NULL DEFAULT 'other',
    notes             VARCHAR(255),
    CONSTRAINT fk_booking_links_source
        FOREIGN KEY (source_booking_id) REFERENCES bookings(booking_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_booking_links_related
        FOREIGN KEY (related_booking_id) REFERENCES bookings(booking_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT uq_booking_links UNIQUE (source_booking_id, related_booking_id, relation_type)
);

-- Für schnellere Auswertungen
CREATE INDEX idx_bookings_category ON bookings(category_id);
CREATE INDEX idx_bookings_account  ON bookings(account_id);
CREATE INDEX idx_bookings_person   ON bookings(person_id);
CREATE INDEX idx_bookings_payee    ON bookings(payee_id);
CREATE INDEX idx_bookings_date     ON bookings(booking_date);

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
