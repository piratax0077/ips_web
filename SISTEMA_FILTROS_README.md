# Sistema Unificado de Búsqueda y Filtros

## 📋 Descripción General

He implementado un sistema centralizado de búsqueda y filtros que funciona desde el **header** y se integra con las vistas de **repuestos** y **buscador**.

## 🎯 Características Principales

### 1. **Header Inteligente**
- Captura parámetros GET de la URL
- Ofrece funciones de filtrado unificadas
- Se comunica con las vistas mediante eventos personalizados

### 2. **Funciones Disponibles desde el Header**

#### `buscar()`
Búsqueda principal que redirige al buscador:
```javascript
// Desde el input de búsqueda en el header
buscar() // Toma el valor del input #tags y redirige a /{termino}/buscador
```

#### `filtrarPorCategoria(categoria)`
Filtra repuestos por categoría:
```javascript
filtrarPorCategoria('neumaticos')  // Va a /repuestos?categoria=neumaticos
filtrarPorCategoria('aceites')     // Va a /repuestos?categoria=aceites
filtrarPorCategoria('filtros')     // Va a /repuestos?categoria=filtros
```

#### `filtrarOfertas()`
Muestra solo productos en oferta:
```javascript
filtrarOfertas()  // Va a /repuestos?oferta=1
```

#### `aplicarFiltros(filtros)`
Aplica múltiples filtros simultáneamente:
```javascript
aplicarFiltros({
    categoria: 'neumaticos',
    marca: 'Michelin',
    precio_min: 10000,
    precio_max: 50000
})
```

#### `buscarConFiltros(termino, filtros)`
Búsqueda con filtros adicionales:
```javascript
buscarConFiltros('frenos', { marca: 'Brembo', oferta: 1 })
// Va a /frenos/buscador?marca=Brembo&oferta=1
```

## 🔄 Flujo de Funcionamiento

### Escenario 1: Usuario hace clic en "Neumáticos"
```
1. Click en dropdown "Productos" → "Neumáticos"
2. Ejecuta: filtrarPorCategoria('neumaticos')
3. Redirige a: /repuestos?categoria=neumaticos
4. La vista repuestos.blade.php detecta el parámetro
5. Dispara evento 'aplicarFiltrosDesdeURL'
6. Ejecuta aplicarFiltrosRepuestos({ categoria: 'neumaticos' })
7. DataTable filtra la columna de descripción
8. Muestra solo repuestos de neumáticos
```

### Escenario 2: Usuario busca desde el header
```
1. Usuario escribe "frenos" en el input de búsqueda
2. Presiona Enter o hace clic en el botón buscar
3. Ejecuta: buscar()
4. Redirige a: /frenos/buscador
5. buscador.blade.php extrae el término "frenos"
6. Carga repuestos con: cargarRepuestos('frenos')
7. Muestra resultados
```

### Escenario 3: Ofertas especiales
```
1. Click en "Ofertas Especiales"
2. Ejecuta: filtrarOfertas()
3. Redirige a: /repuestos?oferta=1
4. Filtra DataTable por la columna de precio (busca "Oferta")
5. Muestra solo productos con badge de oferta
```

## 📂 Archivos Modificados

### 1. `resources/views/fragm/header.blade.php`
**Funciones agregadas:**
- `obtenerParametrosURL()` - Extrae parámetros GET
- `buscar()` - Búsqueda principal
- `enter_buscar(event)` - Permite buscar con Enter
- `aplicarFiltros(filtros)` - Aplica filtros a la vista actual
- `filtrarPorCategoria(categoria)` - Filtro por categoría
- `filtrarOfertas()` - Filtro de ofertas
- `buscarConFiltros(termino, filtros)` - Búsqueda con filtros
- `inicializarFiltrosDeURL()` - Detecta y aplica filtros de la URL

**Eventos:**
- Dispara `aplicarFiltrosDesdeURL` cuando detecta parámetros en la URL

### 2. `resources/views/repuestos.blade.php`
**Funciones agregadas:**
- `aplicarFiltrosRepuestos(filtros)` - Aplica filtros a la tabla de repuestos

**Escucha:**
- Evento `aplicarFiltrosDesdeURL` del header

**Filtros disponibles:**
- `categoria` - Busca en columna de descripción
- `marca` - Busca en columna de marca
- `oferta` - Busca "Oferta" en columna de precio
- `busqueda` - Búsqueda general en toda la tabla

### 3. `resources/views/buscador.blade.php`
**Funciones agregadas:**
- `aplicarFiltrosBuscador(filtros)` - Aplica filtros a los resultados

**Escucha:**
- Evento `aplicarFiltrosDesdeURL` del header

**Filtros disponibles:**
- `marca` - Filtra por marca
- `oferta` - Filtra ofertas

## 🎨 Ejemplos de Uso

### Desde el Header (HTML)
```html
<!-- Ya implementado en el header -->
<a href="javascript:void(0)" onclick="filtrarPorCategoria('neumaticos')">
    Neumáticos
</a>

<a href="javascript:void(0)" onclick="filtrarOfertas()">
    Ofertas Especiales
</a>
```

### Programáticamente (JavaScript)
```javascript
// Filtrar por categoría
filtrarPorCategoria('aceites');

// Buscar término
document.getElementById('tags').value = 'frenos';
buscar();

// Aplicar múltiples filtros
aplicarFiltros({
    categoria: 'neumaticos',
    marca: 'Michelin'
});

// Búsqueda avanzada
buscarConFiltros('filtros', {
    marca: 'Mann',
    oferta: 1
});
```

### URLs Generadas
```
/repuestos                           → Todos los repuestos
/repuestos?categoria=neumaticos      → Solo neumáticos
/repuestos?oferta=1                  → Solo ofertas
/repuestos?marca=Michelin            → Solo marca Michelin
/frenos/buscador                     → Búsqueda de "frenos"
/frenos/buscador?marca=Brembo        → Búsqueda de "frenos" marca Brembo
```

## 🔍 Parámetros GET Soportados

| Parámetro | Descripción | Ejemplo | Vista Compatible |
|-----------|-------------|---------|------------------|
| `categoria` | Filtra por categoría de producto | `?categoria=neumaticos` | repuestos |
| `marca` | Filtra por marca | `?marca=Michelin` | repuestos, buscador |
| `oferta` | Muestra solo ofertas | `?oferta=1` | repuestos, buscador |
| `busqueda` | Búsqueda general | `?busqueda=frenos` | repuestos |
| `precio_min` | Precio mínimo (futuro) | `?precio_min=10000` | - |
| `precio_max` | Precio máximo (futuro) | `?precio_max=50000` | - |

## 🚀 Ventajas del Sistema

1. **Centralizado**: Una sola fuente de verdad en el header
2. **Reutilizable**: Funciona en múltiples vistas
3. **Extensible**: Fácil agregar nuevos filtros
4. **SEO Friendly**: URLs con parámetros descriptivos
5. **User Friendly**: Feedback visual con SweetAlert2
6. **Compatible**: Funciona con DataTables existentes

## 🔧 Mantenimiento

### Agregar un nuevo filtro

1. **En el header** - Agregar función de filtrado:
```javascript
function filtrarPorNuevoTipo(tipo) {
    window.location.href = `/repuestos?tipo=${encodeURIComponent(tipo)}`;
}
```

2. **En repuestos.blade.php** - Manejar el filtro:
```javascript
function aplicarFiltrosRepuestos(filtros) {
    // ... código existente ...
    
    if (filtros.tipo) {
        tabla.column(X).search(filtros.tipo, false, false);
    }
}
```

3. **En el menú** - Agregar opción:
```html
<li><a class="dropdown-item" href="javascript:void(0)" 
       onclick="filtrarPorNuevoTipo('valor')">
    Nuevo Filtro
</a></li>
```

## 📱 Responsive

El sistema funciona en dispositivos móviles gracias a:
- Eventos JavaScript universales
- URLs compatibles con navegación móvil
- DataTables responsive integrado

## ⚡ Rendimiento

- Filtros aplicados en el cliente (DataTables)
- Sin recargas innecesarias
- Feedback inmediato al usuario
- Carga asíncrona de datos

## 🎓 Notas Técnicas

- **Eventos Personalizados**: Se usa `CustomEvent` para comunicación entre componentes
- **URLSearchParams**: Para manejar parámetros GET de forma moderna
- **DataTables API**: Para filtrado eficiente en el cliente
- **SweetAlert2**: Para notificaciones elegantes
- **Compatibilidad**: ES6+ con fallbacks para navegadores antiguos
