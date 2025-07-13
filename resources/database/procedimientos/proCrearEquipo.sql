IF OBJECT_ID('proCrearEquipo', 'P') IS NOT NULL
    DROP PROCEDURE proCrearEquipo;
GO

CREATE PROCEDURE proCrearEquipo
    @nombreEquipo VARCHAR(255),
    @descripcionEquipo text,
    @logoEquipo VARCHAR(255),
    @jsonEstudiantes NVARCHAR(MAX)
AS
BEGIN
    SET NOCOUNT ON;

    -- Insertar equipo
    INSERT INTO Equipo (l_equipo, l_logoEquipo, l_descripEquipo)
    VALUES (@nombreEquipo, @logoEquipo, @descripcionEquipo);

    -- Obtener ID autogenerado del equipo insertado
    DECLARE @c_equipo BIGINT = SCOPE_IDENTITY();

    -- Tabla temporal para estudiantes
    CREATE TABLE #TempEstudiantes (
        c_usuario varchar(100),
        c_estudiante VARCHAR(20),
        q_lider numeric(1,0)
    );

    -- Parsear el JSON 
    INSERT INTO #TempEstudiantes (c_usuario, c_estudiante, q_lider)
    SELECT 
        JSON_VALUE(value, '$.c_usuario'),
        JSON_VALUE(value, '$.c_estudiante'),
        JSON_VALUE(value, '$.q_lider')
    FROM OPENJSON(@jsonEstudiantes);

    -- Insertar detalles del equipo
    INSERT INTO EquipoDet (c_equipo, c_usuario, c_estudiante, q_lider)
    SELECT @c_equipo, c_usuario, c_estudiante, q_lider
    FROM #TempEstudiantes;

    DROP TABLE #TempEstudiantes;
END
GO
