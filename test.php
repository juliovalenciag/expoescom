SELECT
          a.boleta,
          a.nombre,
          a.apellido_paterno,
          a.apellido_materno,
          a.curp,
          a.genero,
          a.telefono,
          a.semestre,
          a.carrera,
          a.correo,
          e.id                   AS equipo_id,
          e.nombre_equipo,
          e.nombre_proyecto,
          e.horario_preferencia,
          e.academia_id,
          me.unidad_id,
          e.es_ganador,
          s.salon_id,
          hb.tipo                AS bloque,
          hb.hora_inicio,
          hb.hora_fin
        FROM alumnos a
        JOIN miembros_equipo me   ON me.alumno_boleta = a.boleta
        JOIN equipos e           ON e.id            = me.equipo_id
        LEFT JOIN asignaciones s ON s.equipo_id     = e.id
        LEFT JOIN horarios_bloques hb 
          ON hb.id = s.horario_id
        ORDER BY a.boleta