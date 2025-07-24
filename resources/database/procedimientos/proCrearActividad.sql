IF OBJECT_ID('proCrearActividad', 'P') IS NOT NULL
    DROP PROCEDURE proCrearActividad;
GO

CREATE  PROCEDURE proCrearActividad
    @c_usuario        VARCHAR(100),
    @l_actividad      VARCHAR(255),
    @l_descripcion    TEXT,
    @l_horaActividad  TIME,
    @l_diaActividad   DATE,
    @jsonRecursos     NVARCHAR(MAX)   
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRAN;

        /* 1) Insertar la actividad */
        INSERT INTO Actividad
            (c_usuario, l_actividad, l_descripcion, l_horaActividad, l_diaActividad)
        VALUES
            (@c_usuario, @l_actividad, @l_descripcion, @l_horaActividad, @l_diaActividad);

        DECLARE @c_actividad BIGINT = SCOPE_IDENTITY();  -- ID recién creado

        /* 2) Parsear el JSON de recursos y cargarlo a tabla temporal */
        IF (ISNULL(@jsonRecursos,'') <> '')
        BEGIN
            INSERT INTO ActividadDetalle (c_recurso, c_actividad)
            SELECT
                JSON_VALUE([value], '$') AS c_recurso,
                @c_actividad            AS c_actividad
            FROM OPENJSON(@jsonRecursos);
        END

        COMMIT TRAN;

        SELECT @c_actividad AS c_actividad;  -- opcional: devolverlo al frontend
    END TRY
    BEGIN CATCH
        ROLLBACK TRAN;
        THROW;
    END CATCH
END
GO
