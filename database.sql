-- Table des types de matière
CREATE TABLE material_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Table des matières
CREATE TABLE materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    material_type_id INT NOT NULL,
    unit VARCHAR(20) NOT NULL,
    FOREIGN KEY (material_type_id) REFERENCES material_types(id)
) ENGINE=InnoDB;

-- Table des machines
CREATE TABLE machines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- Table des classes
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Table des professeurs
CREATE TABLE professors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- Table des responsables de réalisation
CREATE TABLE responsibles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- Table des usages (registre de consommation)
CREATE TABLE usages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    datetime DATETIME NOT NULL,
    class_id INT NOT NULL,
    project VARCHAR(255),
    professor_id INT,
    responsible_id INT,
    material_id INT NOT NULL,
    machine_id INT NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    notes TEXT,
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (professor_id) REFERENCES professors(id),
    FOREIGN KEY (responsible_id) REFERENCES responsibles(id),
    FOREIGN KEY (material_id) REFERENCES materials(id),
    FOREIGN KEY (machine_id) REFERENCES machines(id)
) ENGINE=InnoDB;
