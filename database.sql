-- Table des types de matière (Filament, Bois, Papier, etc.)
CREATE TABLE material_types (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

-- Table des matières précises (PLA blanc, MDF 3mm, etc.)
CREATE TABLE materials (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    material_type_id INTEGER NOT NULL,
    unit TEXT NOT NULL, -- g, m², feuille, etc.
    FOREIGN KEY (material_type_id) REFERENCES material_types(id)
);

-- Table des machines (Ender 3, Raise 3D, Laser, etc.)
CREATE TABLE machines (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    category TEXT NOT NULL -- Impression 3D, Découpe Laser, etc.
);

-- Table des classes (importée de Pronote)
CREATE TABLE classes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE -- ex: 1STI2D1
);

-- Table des professeurs (importée de Pronote)
CREATE TABLE professors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL -- ex: "Jean Dupont"
);

-- Table des responsables de réalisation (fabmanager ou élève)
CREATE TABLE responsibles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL -- ex: "Élève", "Julien (Fabmanager)"
);

-- Table des usages (registre de consommation)
CREATE TABLE usages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    datetime DATETIME NOT NULL,
    class_id INTEGER NOT NULL,
    project TEXT,
    professor_id INTEGER,
    responsible_id INTEGER,
    material_id INTEGER NOT NULL,
    machine_id INTEGER NOT NULL,
    quantity REAL NOT NULL,
    notes TEXT,
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (professor_id) REFERENCES professors(id),
    FOREIGN KEY (responsible_id) REFERENCES responsibles(id),
    FOREIGN KEY (material_id) REFERENCES materials(id),
    FOREIGN KEY (machine_id) REFERENCES machines(id)
);
