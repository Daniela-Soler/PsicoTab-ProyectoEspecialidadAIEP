# PsicoTab - Sistema Web Psicométrico 🧠📊

Estado del proyecto: 🏗️ En fase de diseño y arquitectura (Inicio de etapa de codificación: Noviembre 2026).

## 📖 Descripción del Proyecto

PsicoTab es una plataforma web orientada al sector clínico, diseñada para la automatización, tabulación, baremación y reportabilidad de instrumentos psicométricos abiertos. El sistema busca optimizar los tiempos de análisis de los profesionales de la salud mental y garantizar la precisión matemática en la evaluación de resultados.

## 🔐 Seguridad y Arquitectura de Datos

Dada la naturaleza clínica y altamente sensible de la información gestionada, el sistema está siendo diseñado bajo estrictos estándares de manejo de datos:

Seudonimización de Datos Clínicos: Diseño de una arquitectura que disocia la identidad personal del paciente de sus resultados de evaluación, protegiendo su privacidad ante posibles vulnerabilidades.

Modelado Relacional: Estructuración de una base de datos robusta para gestionar la relación entre profesionales, pacientes, baterías de test y reportes generados.

## 🛠️ Stack Tecnológico Proyectado

Backend: PHP

Base de Datos: MariaDB (MySQL)

Frontend: HTML5, CSS3, JavaScript puro

## 🗺️ Roadmap de Desarrollo

El desarrollo se ejecuta bajo la metodología ágil eXtreme Programming (XP), priorizando ciclos cortos y revisión continua de código durante el segundo semestre de 2026.

**Fase 1: Levantamiento y Diseño**
- [x] Definición de Requerimientos Funcionales y No Funcionales (RF/RNF).
- [x] Análisis de Riesgos de Seguridad aplicando el Modelo STRIDE.
- [x] Diseño UI/UX y prototipado navegable de alta fidelidad en Figma.
- [ ] Modelado normalizado de la Base de Datos relacional en MySQL.

**Fase 2: Desarrollo Frontend y Backend**
- [ ] Implementación de Módulo de Autenticación seguro (hash bcrypt y control de sesiones).
- [ ] Desarrollo del Módulo de Registro de Pacientes.
- [ ] Maquetación responsiva de cuestionarios (34 reactivos) con Bootstrap 5 y validación en cliente con JavaScript.
- [ ] Programación del Motor de Baremación algorítmica en PHP puro (PHQ-9, GAD-7, ASRS v1.1 y WHODAS 2.0).

**Fase 3: Pruebas y Despliegue**
- [ ] Ejecución de pruebas unitarias y validación bidireccional de datos (Prevención de inyección SQL mediante sentencias preparadas).
- [ ] Generación automatizada de Fichas Resumen con semaforización de riesgo y alertas tempranas.
- [ ] Despliegue funcional en entorno de servidor local (XAMPP).

Desarrollado como proyecto de especialidad - Técnico en Programación y Análisis de Sistemas.
