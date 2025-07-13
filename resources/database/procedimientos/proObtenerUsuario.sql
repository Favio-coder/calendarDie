IF OBJECT_ID('proObtenerUsuario', 'P') IS NOT NULL
    DROP PROCEDURE proObtenerUsuario;
GO


CREATE PROCEDURE proObtenerUsuario
	@l_correoElectronico varchar(50)
AS
BEGIN
	SET NOCOUNT ON;

	select top 1 * from Usuario where l_correoElectronico=@l_correoElectronico
END
GO