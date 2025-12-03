-- Haushaltsplaner Datenbankschema
-- MySQL 8.0+ Syntax
Drop DATABASE haushaltsplaner;
Create DATABASE haushaltsplaner;
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
    cost_type        ENUM('fixed', 'variable', 'unexpected') NOT NULL,
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
    link_id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    source_booking_id BIGINT UNSIGNED NOT NULL,
    related_booking_id BIGINT UNSIGNED NOT NULL,
    relation_type    ENUM('installment', 'refund', 'reversal', 'adjustment', 'other') NOT NULL DEFAULT 'other',
    notes            VARCHAR(255),
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
CREATE INDEX idx_bookings_account ON bookings(account_id);
CREATE INDEX idx_bookings_person ON bookings(person_id);
CREATE INDEX idx_bookings_payee ON bookings(payee_id);
CREATE INDEX idx_bookings_date ON bookings(booking_date);