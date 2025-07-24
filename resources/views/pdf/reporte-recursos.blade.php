<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Recursos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h1 {
            text-align: center;
            margin-bottom: 10px;
        }

        .fecha {
            text-align: right;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 8px;
            border: 1px solid #444;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Recursos Asignados</h1>

    <div class="fecha">
        Fecha de emisión: {{ $fecha }}
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Recurso</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($recursos as $index => $recurso)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $recurso['l_recurso'] }}</td>
                    <td>{{ $recurso['l_descripcion'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total de recursos:</strong> {{ count($recursos) }}</p>
</body>
</html>
