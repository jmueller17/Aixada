# Pruebas en Aixada

Se usan dos versiones de PHP: 7.4 y 8.5, lo cual garantizar la compatibilidad en  
versiones intermedias.

Se emplean las herramientas [PHPStan](https://phpstan.org/) y
[PHPUnit](https://phpunit.de).

Estas herramientas no vienen instaladas en Aixada, pero su descarga está
configurada (ver apartado Composer).

## Análisis estático con PHPStan

El análisis estático solo se ha probado en PHP 8.5 y detecta:
- uso de variables, funciones, clases, etc. no declaradas
- uso de elementos obsoletos
- sintaxis incorrectas
- etc.

## Pruebas de ejecución con PHPUnit

Se ha creado un pequeño conjunto de pruebas con PHPUnit que se ejecuta en PHP
7.4 y 8.5 para probar:
- importación de hojas de cálculo
- envío de correos
- cambios recientes en el código de Aixada

## Composer

Se usa [Composer](https://getcomposer.org/download/) tanto para instalar las
herramientas como para ejecutarlas.

Composer se invoca mediante procedimientos con los sufijos `_t74` y `_t85`
que determinan el PHP usado. Cada composer determina que confgración usa:
`./composer_t74.json` o `./composer_t85.json` via la variable de entorno
`COMPOSER`.


Instalación:
```
composer_t85 install
composer_t74 install
```

Ejecución de PHPUnit:
```
composer_t85 test
composer_t74 test
```

Ejecución de PHPStan:
```
composer_t85 analyse
  o
composer_t85 analyse_to_log
```

### Uso de dos versiones de PHP

Para usar Composer con dos configuraciones distintas en el mismo proyecto, se
deben crear archivos bash/bat específicos para cada versión y cargar
configuraciones diferentes en cada uno.

Por ejemplo, en Windows, dentro del directorio de Composer se añade 
`composer_t74.bat` con:

```bat
setlocal DISABLEDELAYEDEXPANSION
set PATH=c:\php74_27_vc15_x64;%PATH%
set COMPOSER=composer_t74.json
c:\php74_27_vc15_x64\php "%~dp0composer.phar" %*
```

Así se garantiza que use el archivo de configuración `composer_t74.json` y que
se ejecute con PHP 7.4 (añadiendolo PATH).

Con PHP 8.5 se hace lo mismo.
