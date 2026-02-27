-- SQL Diplomado Detallado Cursos 1,2,3

UPDATE cursos
SET
    contenido_markdown = '
# Planeación Estratégica

## 1. Fundamentación Conceptual

La planeación estratégica es un proceso sistemático mediante el cual una organización define su direccionamiento a mediano y largo plazo, estableciendo objetivos estratégicos alineados con su misión, visión y valores corporativos. Implica el análisis del entorno interno y externo para anticipar escenarios futuros y diseñar ventajas competitivas sostenibles.

Entre las herramientas fundamentales se encuentran el análisis DOFA (Debilidades, Oportunidades, Fortalezas y Amenazas), el análisis PESTEL (Político, Económico, Social, Tecnológico, Ecológico y Legal) y el modelo de las Cinco Fuerzas de Porter. Estas metodologías permiten comprender dinámicas sectoriales y formular estrategias realistas.

## 2. Marco Normativo en Colombia

En el sector público colombiano, la planeación estratégica se articula con:
- Constitución Política de Colombia, Artículos 339 y 340 (Plan Nacional de Desarrollo).
- Ley 152 de 1994 (Ley Orgánica del Plan de Desarrollo).
- Decreto 1499 de 2017 (Modelo Integrado de Planeación y Gestión - MIPG).

El MIPG establece lineamientos para que las entidades públicas integren planeación, gestión y evaluación bajo criterios de eficiencia y transparencia.

## 3. Componentes Técnicos

- Formulación de objetivos bajo metodología SMART.
- Diseño de indicadores KPI.
- Mapa estratégico (Balanced Scorecard).
- Gestión de riesgos estratégicos.
- Alineación presupuestal con metas institucionales.

## 4. Aplicación Profesional

En el sector público, permite estructurar planes de acción institucional y planes indicativos alineados al Plan Nacional de Desarrollo. En el sector privado, facilita decisiones de inversión, expansión y posicionamiento competitivo.

Caso práctico: una entidad territorial formula su plan estratégico a 4 años alineado al Plan de Desarrollo Departamental, estableciendo metas medibles y responsables por dependencia.

## 5. Glosario Técnico

- **DOFA:** Herramienta de diagnóstico estratégico.
- **KPI:** Indicador Clave de Desempeño.
- **Balanced Scorecard:** Cuadro de Mando Integral.
- **Ventaja competitiva:** Capacidad diferenciadora sostenible.

## 6. Referencias Técnicas

- Ley 152 de 1994, Artículos 3–6.
- Decreto 1499 de 2017 (MIPG).
- Kaplan & Norton (1996). The Balanced Scorecard.

## 7. Bibliografía

- https://www.funcionpublica.gov.co/web/mipg
- https://www.dnp.gov.co
- https://hbr.org/1996/01/using-the-balanced-scorecard-as-a-strategic-management-system
',
    video_url_1 = 'https://www.youtube.com/results?search_query=planeacion+estrategica+curso+avanzado',
    video_url_2 = 'https://www.youtube.com/results?search_query=MIPG+planeacion+estrategica'
WHERE id = 1;

UPDATE cursos
SET
    contenido_markdown = '
# Seguridad Ciudadana

## 1. Fundamentación Conceptual

La seguridad ciudadana comprende políticas públicas orientadas a prevenir el delito y fortalecer la convivencia pacífica. Integra análisis criminológico, estadística delictiva y georreferenciación del delito.

Se basa en enfoques preventivos y no exclusivamente reactivos. Incluye estrategias de prevención situacional, intervención comunitaria y fortalecimiento institucional.

## 2. Marco Normativo en Colombia

- Constitución Política, Artículo 2 (fines esenciales del Estado).
- Ley 62 de 1993 (Organización de la Policía Nacional).
- Ley 1801 de 2016 (Código Nacional de Seguridad y Convivencia Ciudadana).

## 3. Componentes Técnicos

- Análisis de mapas del delito.
- Modelos predictivos de criminalidad.
- Gestión interinstitucional.
- Evaluación de impacto de políticas públicas.

## 4. Aplicación Profesional

Permite diseñar planes integrales de seguridad territorial. Ejemplo: implementación de cámaras de vigilancia y programas sociales en zonas de alta incidencia delictiva.

## 5. Glosario Técnico

- **Prevención situacional:** Reducción de oportunidades delictivas.
- **Percepción de seguridad:** Sensación subjetiva ciudadana.
- **Georreferenciación:** Ubicación espacial del delito.

## 6. Referencias Técnicas

- Ley 1801 de 2016.
- Política Marco de Convivencia y Seguridad Ciudadana.
- ONU-Hábitat (Seguridad Urbana).

## 7. Bibliografía

- https://www.mininterior.gov.co
- https://www.policia.gov.co
- https://unhabitat.org/topic/safety
',
    video_url_1 = 'https://www.youtube.com/results?search_query=seguridad+ciudadana+politicas+publicas',
    video_url_2 = 'https://www.youtube.com/results?search_query=ley+1801+de+2016+explicacion'
WHERE id = 2;

UPDATE cursos
SET
    contenido_markdown = '
# Control Interno

## 1. Fundamentación Conceptual

El control interno es un sistema integrado de políticas y procedimientos diseñado para garantizar cumplimiento normativo, eficiencia administrativa y protección de activos.

Se estructura en cinco componentes según COSO: ambiente de control, evaluación del riesgo, actividades de control, información y comunicación, y supervisión.

## 2. Marco Normativo en Colombia

- Ley 87 de 1993 (Sistema de Control Interno).
- Decreto 1499 de 2017 (MIPG).
- Modelo Estándar de Control Interno (MECI).

## 3. Componentes Técnicos

- Identificación y valoración del riesgo.
- Mapas de riesgo institucional.
- Auditoría interna.
- Planes de mejoramiento.

## 4. Aplicación Profesional

Fortalece la transparencia institucional y la rendición de cuentas. Ejemplo: auditoría interna para detectar debilidades en contratación pública.

## 5. Glosario Técnico

- **COSO:** Marco internacional de control interno.
- **Riesgo residual:** Riesgo posterior a controles aplicados.
- **Auditoría interna:** Evaluación independiente de procesos.

## 6. Referencias Técnicas

- Ley 87 de 1993.
- COSO ERM Framework.
- Decreto 1499 de 2017.

## 7. Bibliografía

- https://www.funcionpublica.gov.co
- https://www.contraloria.gov.co
- https://www.coso.org
',
    video_url_1 = 'https://www.youtube.com/results?search_query=control+interno+MECI',
    video_url_2 = 'https://www.youtube.com/results?search_query=ley+87+de+1993+control+interno'
WHERE id = 3;

