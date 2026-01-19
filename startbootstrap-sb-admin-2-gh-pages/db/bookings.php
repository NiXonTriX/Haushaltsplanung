<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Repository-Klasse für alle SQL-Operationen rund um Buchungen.
 *
 * Damit bleibt der SQL-Code an einer Stelle und kann leicht wiederverwendet werden.
 */
class Bookings
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        // Optionale Übergabe der Verbindung ist praktisch für Tests.
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * Holt alle Buchungen aus der Datenbank.
     */
public function selectAll(): array
{
    $sql = "
        SELECT
            b.booking_id,
            b.booking_date,
            b.title,
            b.amount_cents,
            b.currency,
            b.cost_type,
            b.direction,
            b.status,

            c.category_name AS category,
            cp.category_name AS category_parent,

            a.account_name AS account_name,
            p.display_name AS person_name,
            pay.payee_name AS payee_name

        FROM bookings b
        LEFT JOIN categories c  ON b.category_id = c.category_id
        LEFT JOIN categories cp ON c.parent_id = cp.category_id
        LEFT JOIN accounts a    ON b.account_id = a.account_id
        LEFT JOIN persons p     ON b.person_id = p.person_id
        LEFT JOIN payees pay    ON b.payee_id = pay.payee_id

        ORDER BY b.booking_date DESC, b.booking_id DESC
    ";

    return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * Holt genau eine Buchung anhand der ID (inkl. Joins wie in selectAll()).
 */
public function selectOne(int $id): ?array
{
    $sql = "
        SELECT
            b.booking_id,
            b.booking_date,
            b.title,
            b.description,
            b.amount_cents,
            b.currency,
            b.cost_type,
            b.direction,
            b.status,
            b.category_id,
            b.account_id,
            b.person_id,
            b.payee_id,

            c.category_name AS category,
            cp.category_name AS category_parent,

            a.account_name AS account_name,
            p.display_name AS person_name,
            pay.payee_name AS payee_name

        FROM bookings b
        LEFT JOIN categories c  ON b.category_id = c.category_id
        LEFT JOIN categories cp ON c.parent_id = cp.category_id
        LEFT JOIN accounts a    ON b.account_id = a.account_id
        LEFT JOIN persons p     ON b.person_id = p.person_id
        LEFT JOIN payees pay    ON b.payee_id = pay.payee_id
        WHERE b.booking_id = :id
        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['id' => $id]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}


    /**
     * Holt Buchungen basierend auf Filterkriterien.
     *
     * Erwartete Filter-Keys (alle optional):
     * - date_from (YYYY-MM-DD)
     * - date_to (YYYY-MM-DD)
     * - amount_min (z. B. "12,34")
     * - amount_max (z. B. "99,99")
     * - category (Textsuche)
     * - cost_type (z. B. income/expense)
     * - title (Textsuche)
     * - payee (Textsuche)
     * - person (Textsuche)
     * - account (Textsuche)
     */
    public function selectByFilter(array $filter): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filter['date_from'])) {
            $conditions[] = 'b.booking_date >= :date_from';
            $params['date_from'] = $filter['date_from'];
        }

        if (!empty($filter['date_to'])) {
            $conditions[] = 'b.booking_date <= :date_to';
            $params['date_to'] = $filter['date_to'];
        }

        $amountMin = $this->normalizeAmountToCents($filter['amount_min'] ?? null);
        if ($amountMin !== null) {
            $conditions[] = 'b.amount_cents >= :amount_min';
            $params['amount_min'] = $amountMin;
        }

        $amountMax = $this->normalizeAmountToCents($filter['amount_max'] ?? null);
        if ($amountMax !== null) {
            $conditions[] = 'b.amount_cents <= :amount_max';
            $params['amount_max'] = $amountMax;
        }

        if (!empty($filter['category'])) {
            $conditions[] = '(c.category_name LIKE :category_q OR cp.category_name LIKE :category_parent_q)';
            $params['category_q'] = '%' . $filter['category'] . '%';
            $params['category_parent_q'] = '%' . $filter['category'] . '%';
        }


        if (!empty($filter['cost_type']) && in_array($filter['cost_type'], ['fixed', 'variable', 'unexpected'], true)) {
            $conditions[] = 'b.cost_type = :cost_type';
            $params['cost_type'] = $filter['cost_type'];
        }

        if (!empty($filter['title'])) {
            $conditions[] = 'b.title LIKE :title';
            $params['title'] = '%' . $filter['title'] . '%';
        }

        if (!empty($filter['payee'])) {
            $conditions[] = 'pay.payee_name LIKE :payee';
            $params['payee'] = '%' . $filter['payee'] . '%';
        }

        if (!empty($filter['person'])) {
            $conditions[] = 'p.display_name LIKE :person';
            $params['person'] = '%' . $filter['person'] . '%';
        }

        if (!empty($filter['account'])) {
            $conditions[] = 'a.account_name LIKE :account';
            $params['account'] = '%' . $filter['account'] . '%';
        }

        if (!empty($filter['direction']) && in_array($filter['direction'], ['income', 'expense'], true)) {
            $conditions[] = 'b.direction = :direction';
            $params['direction'] = $filter['direction'];
        }

        if (!empty($filter['status']) && in_array($filter['status'], ['planned', 'due', 'booked', 'posted', 'cancelled'], true)) {
            $conditions[] = 'b.status = :status';
            $params['status'] = $filter['status'];
        }

    $sql = "
        SELECT
            b.booking_id,
            b.booking_date,
            b.title,
            b.amount_cents,
            b.currency,
            b.cost_type,
            b.direction,
            b.status,

            c.category_name AS category,
            cp.category_name AS category_parent,

            a.account_name AS account_name,
            p.display_name AS person_name,
            pay.payee_name AS payee_name

        FROM bookings b
        LEFT JOIN categories c  ON b.category_id = c.category_id
        LEFT JOIN categories cp ON c.parent_id = cp.category_id
        LEFT JOIN accounts a    ON b.account_id = a.account_id
        LEFT JOIN persons p     ON b.person_id = p.person_id
        LEFT JOIN payees pay    ON b.payee_id = pay.payee_id
    ";


        if ($conditions) {
            $sql .= "\nWHERE " . implode(' AND ', $conditions);
        }
        $sql .= "\nORDER BY b.booking_date DESC, b.booking_id DESC";


        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    /**
     * Legt eine neue Buchung an und gibt die ID zurück.
     */
    public function insert(array $buchung): int
{
    $sql = "
        INSERT INTO bookings (
            booking_date,
            title,
            description,
            amount_cents,
            currency,
            direction,
            cost_type,
            status,
            category_id,
            account_id,
            person_id,
            payee_id
        ) VALUES (
            :booking_date,
            :title,
            :description,
            :amount_cents,
            :currency,
            :direction,
            :cost_type,
            :status,
            :category_id,
            :account_id,
            :person_id,
            :payee_id
        )
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        'booking_date' => $buchung['booking_date'],
        'title'        => $buchung['title'],
        'description'  => $buchung['description'] ?? null,
        'amount_cents' => $buchung['amount_cents'],
        'currency'     => $buchung['currency'] ?? 'EUR',
        'direction'    => $buchung['direction'],              // income/expense
        'cost_type'    => $buchung['cost_type'],              // fixed/variable/unexpected
        'status'       => $buchung['status'] ?? 'posted',     // planned/due/booked/posted/cancelled
        'category_id'  => $buchung['category_id'] ?? null,
        'account_id'   => $buchung['account_id'] ?? null,
        'person_id'    => $buchung['person_id'] ?? null,
        'payee_id'     => $buchung['payee_id'] ?? null,
    ]);

    return (int)$this->db->lastInsertId();
}


    /**
     * Aktualisiert eine vorhandene Buchung.
     */
public function update(int $bookingId, array $buchung): bool
{
    $sql = "
        UPDATE bookings
        SET
            booking_date = :booking_date,
            title        = :title,
            description  = :description,
            amount_cents = :amount_cents,
            currency     = :currency,
            direction    = :direction,
            cost_type    = :cost_type,
            status       = :status,
            category_id  = :category_id,
            account_id   = :account_id,
            person_id    = :person_id,
            payee_id     = :payee_id
        WHERE booking_id = :id
    ";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        'id'          => $bookingId,
        'booking_date'=> $buchung['booking_date'],
        'title'       => $buchung['title'],
        'description' => $buchung['description'] ?? null,
        'amount_cents'=> $buchung['amount_cents'],
        'currency'    => $buchung['currency'] ?? 'EUR',
        'direction'   => $buchung['direction'],
        'cost_type'   => $buchung['cost_type'],
        'status'      => $buchung['status'] ?? 'posted',
        'category_id' => $buchung['category_id'] ?? null,
        'account_id'  => $buchung['account_id'] ?? null,
        'person_id'   => $buchung['person_id'] ?? null,
        'payee_id'    => $buchung['payee_id'] ?? null,
    ]);
}


    /**
     * Löscht eine Buchung anhand der ID.
     */
    public function delete(int $bookingId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM bookings WHERE booking_id = :id');
        return $stmt->execute(['id' => $bookingId]);
    }


    /**
     * Wandelt einen Geldbetrag (z. B. "12,34") in Cent um.
     */
    private function normalizeAmountToCents(?string $amount): ?int
    {
        if ($amount === null) {
            return null;
        }

        $normalized = trim($amount);
        if ($normalized === '') {
            return null;
        }

        $normalized = str_replace(['€', ' '], '', $normalized);
        $normalized = str_replace(',', '.', $normalized);

        if (!is_numeric($normalized)) {
            return null;
        }

        return (int) round(((float) $normalized) * 100);
    }
}