IF OBJECT_ID('proElimEquipo', 'P') IS NOT NULL
    DROP PROCEDURE proCrearEquipo;
GO

CREATE PROCEDURE proElimEquipo
    @c_equipo BIGINT
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        -- 1. Eliminar integrantes del equipo
        DELETE FROM EquipoDet
        WHERE c_equipo = @c_equipo;

        -- 2. Eliminar el equipo
        DELETE FROM Equipo
        WHERE c_equipo = @c_equipo;

        COMMIT;
    END TRY
    BEGIN CATCH
        ROLLBACK;

        -- Capturar información del error
        DECLARE @ErrorMessage NVARCHAR(4000);
        DECLARE @ErrorSeverity INT;
        DECLARE @ErrorState INT;

        SELECT 
            @ErrorMessage = ERROR_MESSAGE(),
            @ErrorSeverity = ERROR_SEVERITY(),
            @ErrorState = ERROR_STATE();

        -- Lanzar el error con detalles
        RAISERROR (@ErrorMessage, @ErrorSeverity, @ErrorState);
    END CATCH
END;
