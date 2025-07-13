IF OBJECT_ID('[proListActividadesCompleto]', 'P') IS NOT NULL
    DROP PROCEDURE [proListActividadesCompleto];
GO

CREATE PROC proListActividadesCompleto
AS
BEGIN
    SET NOCOUNT ON;

    SELECT
        a.c_actividad,
        a.l_actividad,
        a.l_descripcion,
        a.l_diaActividad,
        a.l_horaActividad,

        u.c_usuario,
        u.l_nombre,
        u.l_apellido,

        r.c_recurso,
        r.l_recurso
    FROM  Actividad a
    JOIN  Usuario   u  ON a.c_usuario = u.c_usuario          
    LEFT JOIN ActividadDetalle ad ON a.c_actividad = ad.c_actividad
    LEFT JOIN Recurso r          ON ad.c_recurso   = r.c_recurso
    ORDER BY a.c_actividad, r.c_recurso;
END