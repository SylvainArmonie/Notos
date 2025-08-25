CREATE TABLE QF_PHP.LIVRES (
    ID_LIVRE     INTEGER GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1) PRIMARY KEY,
    TITRE        VARCHAR(200) CCSID 1208 NOT NULL,
    AUTEUR       VARCHAR(100) CCSID 1208 NOT NULL,
    PRIX         DECIMAL(6,2) NOT NULL,
    STOCK        INTEGER NOT NULL DEFAULT 0,
    DESCRIPTION  CLOB(10K) CCSID 1208, -- Pour résumé du livre
    IMAGE_COUV   VARCHAR(255) CCSID 1208, -- Chemin vers image sur IFS
    CATEGORIE    VARCHAR(100) CCSID 1208,
    DATE_AJOUT   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



INSERT INTO QF_PHP.LIVRES (TITRE, AUTEUR, PRIX, STOCK, IMAGE_COUV) VALUES
('Le Capital - Livre I',           'Karl Marx',                19.90, 12, '/images/livres/le-capital-l1.jpg'),
('Le Capital - Livre II',          'Karl Marx',                18.90, 10, NULL),
('Le Capital - Livre III',         'Karl Marx',                21.90,  8, NULL),
('Germinal',                       'Émile Zola',               9.90,  25, NULL),
('L''Étranger',                    'Albert Camus',             8.90,  30, NULL),
('1984',                           'George Orwell',            7.90,  40, NULL),
('Le Petit Prince',                'Antoine de Saint-Exupéry', 6.90,  35, NULL),
('Madame Bovary',                  'Gustave Flaubert',        10.90,  20, NULL),
('Les Misérables',                 'Victor Hugo',             12.50,  15, NULL),
('La Peste',                       'Albert Camus',             9.50,  22, NULL),
('Le Rouge et le Noir',            'Stendhal',                11.90,  18, NULL),
('Le Comte de Monte-Cristo',       'Alexandre Dumas',         14.90,  16, NULL),
('Au bonheur des dames',           'Émile Zola',               9.20,  12, NULL),
('Candide',                        'Voltaire',                 5.90,  50, NULL),
('Le Père Goriot',                 'Honoré de Balzac',         8.50,  14, NULL);
