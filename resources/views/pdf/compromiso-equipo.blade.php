<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Compromiso de Conformación</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 14px; margin: 40px; line-height: 1.6; }
    h1, h2 { text-align: center; }
    .firma { margin-top: 60px; text-align: center; }
    .logo { max-height: 80px; margin-bottom: 20px; text-align: center; }
    table { width: 100%; margin-top: 20px; border-collapse: collapse; }
    td { padding: 8px; border: 1px solid #999; }
  </style>
</head>
<body>

  {{-- @if($logo)
    <div class="logo">
      <img src="{{ public_path('logos_equipo/' . $logo) }}" style="max-height: 80px;">
    </div>
  @endif --}}

  <h1>Compromiso de Conformación de Equipo</h1>

  <p>Los estudiantes que firman este documento declaran haber formado el equipo denominado <strong>"{{ $equipo }}"</strong> con el fin de participar en el programa de emprendimiento de la universidad.</p>

  <p>Asimismo, se comprometen a:</p>
  <ul>
    <li>Colaborar activamente en el desarrollo del proyecto.</li>
    <li>Asistir a las reuniones, talleres y actividades programadas.</li>
    <li>Respetar la distribución de roles acordada entre los miembros.</li>
    <li>Mantener una comunicación fluida y respetuosa entre los integrantes.</li>
  </ul>

  <h2>Integrantes del Equipo</h2>
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
      @foreach($integrantes as $i)
        <tr>
          <td>{{ $i->l_nombre }}</td>
          <td>{{ $i->l_apellido }}</td>
          <td>{{ $i->c_estudiante}}</td>
          <td></td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div class="firma">
    <p>Lugar y fecha: _________________________</p>
  </div>

</body>
</html>
