IF OBJECT_ID('proCrearCuenta', 'P') IS NOT NULL
    DROP PROCEDURE proLogin;
GO

GO
CREATE PROCEDURE [dbo].[proCrearCuenta]
    @rol BIGINT,
    @nombre VARCHAR(100),
    @apellido VARCHAR(100),
    @fechaNacimiento DATE,
    @correo VARCHAR(50),
    @contrasena VARCHAR(255),
    @codigoEstudiante varchar(20) = '',
    @idCarrera INT,
    @idMentorCreador varchar(100),
    @descripcionMentor text = '',
    @linkedin varchar(100),
    @fotoPerfil VARCHAR(255) = '' 
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @idUsua VARCHAR(100)
    SET @idUsua = CONVERT(VARCHAR(100), NEWID())

    -- Insertar en tabla Usuario con foto de perfil
    INSERT INTO Usuario (
        c_usuario,
        l_nombre,
        l_apellido,
        c_rol,
        f_nacimiento,
        l_correoElectronico,
        l_contrasena,
        l_fotoPerfil -- Campo de la foto
    )
    VALUES (
        @idUsua,
        @nombre,
        @apellido,
        @rol,
        @fechaNacimiento,
        @correo,
        @contrasena,
        @fotoPerfil -- Valor de la foto
    );

    -- Insertar según rol
    IF @rol = 1
    BEGIN
        INSERT INTO MentorOficial(c_usuario)
        VALUES (@idUsua);
    END
    ELSE IF @rol = 2
    BEGIN
        INSERT INTO MentorInvitado(c_usuario, c_mentorCreador, l_descripcion, l_linkedin)
        VALUES (@idUsua, @idMentorCreador, @descripcionMentor, @linkedin);
    END
    ELSE
    BEGIN
        INSERT INTO Estudiante(c_usuario, c_estudiante, c_carrera)
        VALUES (@idUsua, @codigoEstudiante, @idCarrera);
    END

    -- Devolver datos del nuevo usuario (sin contraseña)
    SELECT 
        c_usuario,
        l_nombre,
        l_apellido,
        c_rol,
        f_nacimiento,
        l_correoElectronico,
        l_fotoPerfil 
    FROM Usuario
    WHERE c_usuario = @idUsua
END
GO