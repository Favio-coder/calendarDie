IF OBJECT_ID('proLogin', 'P') IS NOT NULL
    DROP PROCEDURE proLogin;
GO

GO
CREATE PROCEDURE [dbo].[proLogin] 
    @l_correoElectronico VARCHAR(50),
    @l_contrasena VARCHAR(255)
AS
BEGIN
    SET NOCOUNT ON;

    IF EXISTS (
        SELECT 1 
        FROM Usuario 
        WHERE l_correoElectronico = @l_correoElectronico 
          AND l_contrasena = @l_contrasena
    )
    BEGIN
        -- Devuelve todos los datos del usuario si las credenciales son correctas
        SELECT * 
        FROM Usuario 
        WHERE l_correoElectronico = @l_correoElectronico 
          AND l_contrasena = @l_contrasena
    END
    ELSE
    BEGIN
        -- Devuelve un indicador de fallo
        SELECT 0 AS Resultado 
    END
END
