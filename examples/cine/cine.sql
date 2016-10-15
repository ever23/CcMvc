CREATE TABLE actor (
    Id_actor VARCHAR(6) PRIMARY KEY,
    Nombre VARCHAR(40),
    pais VARCHAR(40),
    Sexo VARCHAR(1),
    Fecha_nacimiento DATE,
    Comisión REAL
);

CREATE TABLE pelicula (
    Id_pelicula VARCHAR(6) PRIMARY KEY,
    Titulo VARCHAR(100),
    Anno VARCHAR(4),
    Duracion TIME,
    Costoproduccion REAL,
    Ganancia REAL,
    Id_estudio VARCHAR(6)
);
CREATE TABLE estudio (
    Id_estudio VARCHAR(6) PRIMARY KEY,
    Nombre VARCHAR(100),
    Pais VARCHAR(30)
);
CREATE TABLE elenco (
    Id_actor VARCHAR(6),
    Id_pelicula VARCHAR(6),
    papel VARCHAR(40),
    salario REAL,
    PRIMARY KEY (Id_actor , Id_pelicula)
     
);

INSERT INTO `actor` VALUES
('000010', 'Chistine haas', 'Canada', 'F', '1965-01-01', 4220),
('000020', 'Michael Thompson', 'Usa', 'F', '1973-10-10', 3300),
('000030', 'Sally kwan', 'Usa', 'F', '1975-05-04', 3060),
('000050', 'Gabriela Vergara', 'Colombia', 'F', '1949-08-17', 3214),
('000060', 'Irving Stern', 'Guatemala', 'M', '1973-09-14', 2580),
('000070', 'Eva peron', 'Argentina', 'F', '1945-09-30', 2893),
('000090', 'Eileen dasilva', 'Brasil', 'F', '1970-08-15', 2380),
('000100', 'Theodore spenser', 'Ecuador', 'M', '1980-10-19', 2092),
('000110', 'Vincenzolucchessi', 'Usa', 'M', '1958-05-16', 3720),
('000120', 'Sean oconnell', 'Usa', 'M', '1963-05-12', 2340),
('000130', 'Dolores quintana', 'Cuba', 'F', '1971-07-28', 1904),
('000140', 'Heather nicholls', 'Usa', 'F', '1976-12-15', 2274),
('000150', 'Bruce adamson', 'Usa', 'M', '1972-12-02', 2022),
('000160', 'Patricia velasquez', 'Venezuela', 'F', '1977-11-10', 1780),
('000170', 'Masatoshiyoshiruma', 'Japon', 'M', '1978-09-15', 1974),
('000180', 'Marilyn scoutten', 'Usa', 'F', '1973-07-07', 1707),
('000190', 'Ja wa', 'China', 'F', '1974-07-26', 1636),
('000200', 'David Brown', 'Usa', 'M', '1966-03-03', 2217),
('000210', 'William heimann', 'Usa', 'M', '1979-11-04', 2323),
('000220', 'Jennifer lutz', 'Usa', 'F', '1968-08-29', 2387),
('000221', 'Tobey maguire', 'Usa', 'M', '1981-07-21', 2547.54),
('000222', 'Arnold Schwarzenegger', 'Usa', 'M', '1947-07-30', 7854.87),
('000223', 'Sandahl bergman ', 'Usa', 'F', '1951-11-14', 4587.45),
('000224', 'Rachael leigh cook', 'Usa', 'F', '1942-10-14', 1544),
('000225', 'Tom cruise', 'USA', 'M', '1967-05-03', 9999),
('000226', 'will smit', 'USA', 'M', '1968-05-02', 20000);


INSERT INTO `estudio`  VALUES
('000001', 'Aol time-warner', 'Usa'),
('000002', 'Warner bros', 'Usa'),
('000003', 'Pixar', 'Usa'),
('000004', 'Walt Disney', 'Usa'),
('000005', '20th century fox', 'Usa'),
('000006', 'Sony pictures', 'Usa'),
('000007', 'Metro-golddwyn-mayer (MGM)', 'Usa'),
('000008', 'Paramount', 'Usa'),
('000009', 'Universal studios', 'Usa'),
('000010', 'Rko pictures', 'Usa'),
('000011', 'Filmax', 'Usa'),
('000012', 'Marvel studio', 'Usa');

INSERT INTO `pelicula`   VALUES
('0000A1', 'Spiderman', '2002', '01:21:00', 70, 80, '000006'),
('0000A2', 'El mago de oz', '1939', '01:00:00', 35, 70, '000007'),
('0000A3', 'Regreso al futuro', '1985', '01:16:00', 90, 180, '000009'),
('0000A4', 'Ghost', '1990', '01:24:00', 27, 80, '000008'),
('0000A5', 'Casablanca', '1942', '01:02:00', 87, 99, '000007'),
('0000A6', 'Juana de arco', '1948', '01:43:00', 150, 165, '000010'),
('0000A7', 'El exorcista', '1973', '01:24:00', 115, 160, '000002'),
('0000A8', 'Fantasia 2000', '1999', '01:15:00', 175, 221, '000004'),
('0000A9', 'Alien 3', '1992', '01:15:00', 75, 10, '000005'),
('000A10', 'La mosca', '1958', '01:20:00', 45, 35, '000005'),
('000A11', 'La momia regresa', '2001', '01:20:00', 120, 120, '000009'),
('000A12', 'Guerra de los mundos', '2006', '01:20:00', 120, 150, '000008'),
('000A13', 'Conan el barbaro', '1982', '01:15:00', 10, 50, '000009'),
('000A14', 'Destino fatal', '2006', '01:30:00', 45, 21, '000011'),
('000A15', 'The Avenger', '2010', '01:30:00', 100, 120, '000012');

INSERT INTO `elenco`  VALUES
('000010', '000A14', 'Oficinista', 22104),
('000020', '000A12', 'Contrafigura', 35487),
('000050', '000A10', 'Protagonista', 547451),
('000060', '0000A9', 'Policía', 47571),
('000160', '000A11', 'Protagonista', 3500),
('000221', '0000A1', 'Protagonista', 12000),
('000222', '000A13', 'Protagonista', 1542),
('000223', '000A13', 'Coprotagonista', 354045),
('000224', '000A14', 'Protagonista', 72541),
('000225', '000A12', 'Protagonista', 4454754),
('000226', '000A15', 'Protagonista', 445475);