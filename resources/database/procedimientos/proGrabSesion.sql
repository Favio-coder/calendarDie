IF OBJECT_ID('proGrabSesion', 'P') IS NOT NULL
    DROP PROCEDURE proCrearEquipo;
GO

CREATE PROCEDURE proGrabSesion
    @l_sesion NVARCHAR(255),
    @c_programa INT,
    @l_descripcion TEXT,
    @l_linkSesion NVARCHAR(500),
    @l_linkGrabacion NVARCHAR(500),
    @l_fotoSesion NVARCHAR(500),
    @tablaRecursos SesionRecursoTableType READONLY  -- Tabla tipo
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        -- 1. Insertar la sesión
        INSERT INTO sesion (l_sesion, c_programa, l_descripcion, l_linkSesion, l_linkGrabacion, l_fotoSesion)
        VALUES (@l_sesion, @c_programa, @l_descripcion, @l_linkSesion, @l_linkGrabacion, @l_fotoSesion);

        -- 2. Obtener el ID insertado
        DECLARE @c_sesion INT = SCOPE_IDENTITY();

        -- 3. Insertar los recursos relacionados
        INSERT INTO SesionRecurso (c_sesion, l_nombre, l_tipo, l_linkarchivo)
        SELECT @c_sesion, l_nombre, l_tipo, l_linkarchivo
        FROM @tablaRecursos;

        COMMIT;
    END TRY
    BEGIN CATCH
        ROLLBACK;
        THROW;
    END CATCH
END;

