IF OBJECT_ID('proGrabModiRecurso', 'P') IS NOT NULL
    DROP PROCEDURE proCrearEquipo;
GO

CREATE PROCEDURE proGrabModiRecurso
  @modo NVARCHAR(10),              -- 'insertar' o 'editar'
  @id_recurso INT = NULL,          
  @nombre_recurso NVARCHAR(100),
  @descripcion_recurso NVARCHAR(MAX)
AS
BEGIN
  SET NOCOUNT ON;

  IF @modo = 'insertar'
  BEGIN
    INSERT INTO Recurso (l_recurso, l_descripcion)
    VALUES (@nombre_recurso, @descripcion_recurso);
  END
  ELSE IF @modo = 'editar'
  BEGIN
    UPDATE Recurso
    SET 
      l_recurso = @nombre_recurso,
      l_descripcion = @descripcion_recurso
    WHERE c_recurso = @id_recurso;
  END
END
GO
