---
name: select-activity-template
description: Selecciona el template de actividad educativa más adecuado según el alumno, el objetivo de aprendizaje y la solicitud del representante. Usar al interpretar qué necesita practicar un alumno, al recomendar entre reconocer y seleccionar, escuchar o leer y responder, y unir con líneas, o al decidir si falta información antes de elegir un ejercicio.
---

# Seleccionar template de actividad

Recomendar un solo template disponible sin generar todavía la actividad.

## Reunir el contexto

Usar primero la información ya disponible en la conversación y del alumno seleccionado. Considerar:

- Edad del alumno.
- Tema que necesita practicar.
- Acción u objetivo de aprendizaje esperado.
- Preferencia de dificultad, si existe.
- Necesidad pedagógica de imágenes o audios.

No volver a preguntar datos que ya estén disponibles. Si falta información que cambia la elección, formular únicamente la pregunta más discriminante y detener la recomendación.

## Elegir el template

Usar exclusivamente estos identificadores:

| Intención principal | Template | Elegir cuando |
| --- | --- | --- |
| Reconocer o distinguir | `recognize_and_select` | El alumno debe identificar una respuesta correcta entre varias opciones a partir de texto, imagen o audio. |
| Comprender y responder | `listen_read_and_respond` | El alumno debe escuchar, leer u observar un contenido breve y luego demostrar comprensión. |
| Relacionar dos grupos | `match_with_lines` | El alumno debe asociar elementos uno a uno, como sílabas con palabras o conceptos con imágenes. |

Priorizar la acción cognitiva solicitada sobre el tema. Para una petición compatible con varios templates, elegir el formato más simple que cumpla el objetivo. Si la acción no está clara, preguntar si el alumno debe reconocer, comprender o relacionar.

No inventar templates ni recomendar uno que no cubra la interacción solicitada.

## Ajustar dificultad y medios

- Respetar una dificultad explícita cuando sea adecuada para la edad.
- Recomendar `easy` para tareas directas, familiares y con poca carga cognitiva.
- Recomendar `medium` para mayor variedad, distractores moderados o medios combinados.
- Recomendar `hard` para contenido más abstracto, extenso o con menor apoyo.
- Usar la edad para ajustar lenguaje, cantidad de información y nivel de apoyo, no como único criterio.
- Recomendar audio cuando escuchar, discriminar sonidos o practicar pronunciación sea esencial.
- Recomendar imágenes cuando faciliten conceptos concretos, vocabulario o acceso a alumnos que aún no leen con fluidez.
- Recomendar ambos medios solo cuando el objetivo realmente los necesite; evitar medios decorativos.

## Entregar la decisión

Cuando haya información suficiente, responder en español con esta estructura:

```text
Estado: listo
Objetivo de aprendizaje: <objetivo concreto>
Template recomendado: <nombre visible> (<identificador>)
Dificultad: <easy|medium|hard>
Medios sugeridos: <ninguno|imágenes|audios|imágenes y audios>
Motivo: <explicación breve vinculada al objetivo y al alumno>
```

Cuando falte información decisiva, responder:

```text
Estado: necesita_informacion
Objetivo de aprendizaje: <objetivo conocido o por precisar>
Pregunta necesaria: <una sola pregunta>
```

No generar preguntas del ejercicio, opciones, respuestas correctas, ayudas, imágenes, audios ni un payload de `ActivityDefinition`. La generación ocurre después de que el representante apruebe el plan.

## Comprobar la selección

Antes de responder, confirmar que:

- El template coincide con la acción que realizará el alumno.
- La dificultad considera edad y complejidad.
- Los medios tienen una función pedagógica.
- La respuesta contiene una recomendación o una pregunta, nunca ambas.
