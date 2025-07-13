<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Compromiso de Conformación</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 14px; margin: 40px; line-height: 1.6; }
    h1, h2 { text-align: center; }
    .firma { margin-top: 60px; text-align: center; }
    table { width: 100%; margin-top: 20px; border-collapse: collapse; }
    th, td { border: 1px solid #999; padding: 8px; text-align: center; }
  </style>
</head>
<body>

  <h1>Compromiso de Conformación de Equipo</h1>

  <p>
    Yo, <strong>{{ $usuario['l_nombre'] }} {{ $usuario['l_apellido'] }}</strong>,
    con código de estudiante <strong>{{ $usuario['c_estudiante'] }}</strong>,
    declaro haber conformado el equipo <strong>"{{ $equipo }}"</strong>
    con el objetivo de participar en el <strong>{{ $programa }}</strong> de la universidad.
  </p>

  <p>Me comprometo a:</p>
  <ul>
    <li>Colaborar activamente en el desarrollo del proyecto.</li>
    <li>Asistir a todas las reuniones, talleres y actividades convocadas.</li>
    <li>Respetar la distribución de funciones entre los miembros del equipo.</li>
    <li>Mantener una comunicación constante, respetuosa y efectiva.</li>
  </ul>

  <h2>Integrante del Equipo</h2>
  <table>
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Código</th>
        <th>Firma</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>{{ $usuario['l_nombre'] }}</td>
        <td>{{ $usuario['l_apellido'] }}</td>
        <td>{{ $usuario['c_estudiante'] }}</td>
        <td></td>
      </tr>
    </tbody>
  </table>

  <div class="firma">
    <p>Lugar y fecha: _____________________, {{ $fecha }}</p>
    <p style="margin-top: 80px;">Firma del estudiante</p>
  </div>

</body>
</html>
