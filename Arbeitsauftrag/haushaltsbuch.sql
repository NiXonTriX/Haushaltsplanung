create table empfaenger
(
    id           int auto_increment
        primary key,
    name         varchar(255) not null,
    bankkonto    varchar(255) null,
    beschreibung text         null
)
    charset = latin1;

create table kategorien
(
    id                int auto_increment
        primary key,
    title             varchar(64) not null,
    parent_id         int         null,
    default_person    int         null comment 'Default wenn leer',
    default_kostenart int         null comment 'default für Kostenart',
    constraint kategorien_kategorien_id_fk
        foreign key (parent_id) references kategorien (id)
);

create table konten
(
    id           int auto_increment
        primary key,
    name         varchar(255) not null,
    beschreibung text         null
)
    charset = latin1;

create table personen
(
    id           int auto_increment
        primary key,
    name         varchar(255) not null,
    beschreibung text         null
)
    charset = latin1;

create table buchungen
(
    id            int auto_increment
        primary key,
    datum         date                                   not null,
    betrag        decimal(10, 2)                         not null,
    typ           enum ('fix', 'variabel', 'unerwartet') null,
    kategorie_id  int                                    null,
    konto_id      int                                    null,
    person_id     int                                    null,
    empfaenger_id int                                    null,
    kommentar     text                                   null,
    constraint buchungen_ibfk_1
        foreign key (kategorie_id) references kategorien (id),
    constraint buchungen_ibfk_2
        foreign key (konto_id) references konten (id),
    constraint buchungen_ibfk_3
        foreign key (person_id) references personen (id),
    constraint buchungen_ibfk_4
        foreign key (empfaenger_id) references empfaenger (id)
)
    charset = latin1;

create table buchung_verknuepfung
(
    id             int auto_increment
        primary key,
    buchung_id     int                                  not null,
    verknuepfte_id int                                  not null,
    typ            enum ('Gutschrift', 'Folgerechnung') not null,
    constraint buchung_verknuepfung_ibfk_1
        foreign key (buchung_id) references buchungen (id)
            on delete cascade,
    constraint buchung_verknuepfung_ibfk_2
        foreign key (verknuepfte_id) references buchungen (id)
            on delete cascade
)
    charset = latin1;

create index buchung_id
    on buchung_verknuepfung (buchung_id);

create index verknuepfte_id
    on buchung_verknuepfung (verknuepfte_id);

