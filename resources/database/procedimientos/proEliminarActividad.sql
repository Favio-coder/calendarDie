IF OBJECT_ID('proElimActividad', 'P') IS NOT NULL
    DROP PROCEDURE proElimActividad;
GO

CREATE PROCEDURE proElimActividad
    @c_actividad BIGINT
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        
        DELETE FROM ActividadDetalle
        WHERE c_actividad = @c_actividad;

        DELETE FROM Actividad
        WHERE c_actividad = @c_actividad;

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
