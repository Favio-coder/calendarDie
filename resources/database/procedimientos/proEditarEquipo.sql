IF OBJECT_ID('proEditarEquipo', 'P') IS NOT NULL
    DROP PROCEDURE proEditarEquipo;
GO


CREATE PROCEDURE proEditarEquipo
    @c_equipo        BIGINT,
    @nombreEquipo    VARCHAR(255),
    @descripcionEquipo TEXT,
    @logoEquipo      VARCHAR(255),        
    @jsonEliminar    NVARCHAR(MAX),
    @jsonEstudiantes NVARCHAR(MAX)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRAN;
        UPDATE Equipo
        SET l_equipo      = @nombreEquipo,
            l_descripEquipo = @descripcionEquipo,
            l_logoEquipo  = CASE 
                              WHEN ISNULL(@logoEquipo, '') <> '' 
                                   THEN @logoEquipo 
                              ELSE l_logoEquipo 
                            END
        WHERE c_equipo = @c_equipo;

        CREATE TABLE #TempEliminar (
            c_usuario     VARCHAR(100),
            c_estudiante  VARCHAR(20)
        );

        INSERT INTO #TempEliminar (c_usuario, c_estudiante)
        SELECT
            JSON_VALUE([value], '$.c_usuario'),
            JSON_VALUE([value], '$.c_estudiante')
        FROM OPENJSON(@jsonEliminar);

        DELETE ed
        FROM  EquipoDet ed
        JOIN  #TempEliminar t
             ON ed.c_equipo     = @c_equipo
            AND ed.c_usuario    = t.c_usuario
            AND ed.c_estudiante = t.c_estudiante;

        DROP TABLE #TempEliminar;

        CREATE TABLE #TempUpdate (
            c_usuario     VARCHAR(100),
            c_estudiante  VARCHAR(20),
            q_lider       NUMERIC(1,0)
        );

        INSERT INTO #TempUpdate (c_usuario, c_estudiante, q_lider)
        SELECT
            JSON_VALUE([value], '$.c_usuario'),
            JSON_VALUE([value], '$.c_estudiante'),
            JSON_VALUE([value], '$.q_lider')
        FROM OPENJSON(@jsonEstudiantes);

        UPDATE ed
           SET ed.q_lider = u.q_lider
        FROM EquipoDet ed
        JOIN #TempUpdate u
             ON ed.c_equipo     = @c_equipo
            AND ed.c_usuario    = u.c_usuario
            AND ed.c_estudiante = u.c_estudiante;

        DROP TABLE #TempUpdate;

        COMMIT TRAN;
    END TRY
    BEGIN CATCH
        ROLLBACK TRAN;
        THROW;
    END CATCH
END
GO
