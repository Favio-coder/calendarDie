IF OBJECT_ID('proEliminarCuenta', 'P') IS NOT NULL
    DROP PROCEDURE proEliminarCuenta;
GO

GO
CREATE PROCEDURE [dbo].[proEliminarCuenta]
   @idUsuario varchar(100),
	@c_rol char(1)
AS
BEGIN
	SET NOCOUNT ON;
	delete from Usuario where c_usuario = @idUsuario
	if @c_rol = '2'
	begin 
		delete from MentorInvitado where c_usuario = @idUsuario
	end
	else if @c_rol = '3'
	begin 
		DELETE FROM EquipoDet WHERE c_usuario = @idUsuario; 
		delete from Estudiante where c_usuario = @idUsuario
	end
END
GO