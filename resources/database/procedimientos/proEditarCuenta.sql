IF OBJECT_ID('proEditarCuenta', 'P') IS NOT NULL
    DROP PROCEDURE proEditarCuenta;
GO

CREATE PROCEDURE proEditarCuenta
    @c_usuario VARCHAR(100),
    @nombre VARCHAR(100),
    @apellido VARCHAR(100),
    @fechaNacimiento DATE,
    @codigoEstudiante VARCHAR(20) = '',
    @idCarrera INT = NULL,
    @descripcionMentor TEXT = '',
    @linkedin VARCHAR(100) = '',
    @fotoPerfil VARCHAR(255) = ''
AS
BEGIN
    SET NOCOUNT ON;

    -- Actualizar datos del usuario
    UPDATE Usuario
    SET 
        l_nombre = @nombre,
        l_apellido = @apellido,
        f_nacimiento = @fechaNacimiento,
        l_fotoPerfil = CASE 
                         WHEN @fotoPerfil <> '' THEN @fotoPerfil 
                         ELSE l_fotoPerfil 
                       END
    WHERE c_usuario = @c_usuario;

    -- Obtener rol
    DECLARE @rol INT;
    SELECT @rol = c_rol FROM Usuario WHERE c_usuario = @c_usuario;

    -- Actualizar por rol
    IF @rol = 2
    BEGIN
        UPDATE MentorInvitado
        SET 
            l_descripcion = @descripcionMentor,
            l_linkedin = @linkedin
        WHERE c_usuario = @c_usuario;
    END
    ELSE IF @rol = 3
    BEGIN
        UPDATE Estudiante
        SET 
            c_estudiante = @codigoEstudiante,
            c_carrera = @idCarrera
        WHERE c_usuario = @c_usuario;
    END

    -- Devolver datos actualizados
    SELECT 
        c_usuario,
        l_nombre,
        l_apellido,
        c_rol,
        f_nacimiento,
        l_correoElectronico,
        l_fotoPerfil 
    FROM Usuario
    WHERE c_usuario = @c_usuario;
END
GO
