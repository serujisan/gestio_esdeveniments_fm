# Gestió d'Espectacles - Plugin WordPress

Plugin de WordPress per gestionar esdeveniments i espectacles amb formulari públic, gestió de proveïdors i categories.

## Característiques

### Gestió d'Esdeveniments
- Custom Post Type per esdeveniments amb camps personalitzats:
  - Proveïdor
  - Artista/DJ
  - Nom de l'espectacle
  - Categoria
  - Enllaç web
  - Informació adicional
  - Imatges (1 obligatòria)
  - Lloc de l'esdeveniment
  - Població
  - Província
  - Data i hora d'inici
  - Data i hora final
  - Codi setmanal
  - Marcatge de patrocinat

### Formulari Públic
- Formulari frontend per a proveïdors autenticats
- Validació de camps obligatoris
- Pujada d'imatges múltiples
- Els esdeveniments enviats queden pendents de revisió

### Visualització d'Esdeveniments
- Llistat d'esdeveniments de la setmana actual
- Filtres per província, població i categoria
- Esdeveniments patrocinats apareixen primers
- Ordenació per data i hora d'inici
- Disseny responsive amb grid

### Gestió de Proveïdors
- Custom Post Type per proveïdors
- Associació amb usuaris de WordPress
- Control d'estat actiu/inactiu
- Panell d'administració dedicat

### Gestió de Categories
- Taxonomia per categories d'esdeveniments
- Camps configurables per generar text:
  - Nom artista
  - Municipi
  - Any
  - Què fer
  - Concert
  - Notes

## Instal·lació

1. Puja la carpeta `gestio-espectacles` al directori `/wp-content/plugins/`
2. Activa el plugin des del menú 'Plugins' de WordPress
3. Configura els proveïdors i categories des del panell d'administració

## Ús

### Shortcodes

#### Formulari per enviar esdeveniments
```
[ge_formulari_esdeveniment]
```

#### Llistat d'esdeveniments
```
[ge_esdeveniments_list]
```

### Configuració

1. **Crear Proveïdors**: Ves a `Esdeveniments > Gestió Proveïdors` per crear proveïdors i associar-los amb usuaris de WordPress.

2. **Crear Categories**: Ves a `Esdeveniments > Categories` per crear categories i configurar els camps per generar text.

3. **Afegir Pàgines**: El plugin crea automàticament una pàgina "Esdeveniments" en activar-se. Pots crear una altra pàgina amb el shortcode del formulari per als proveïdors.

### Per als Proveïdors

Els usuaris associats a un proveïdor actiu poden accedir al formulari públic per enviar esdeveniments. Els esdeveniments enviats quedaran pendents de revisió per l'administrador.

### Per als Administradors

- Gestiona tots els esdeveniments des de `Esdeveniments`
- Aprova o rebutja esdeveniments enviats pels proveïdors
- Marca esdeveniments com a patrocinats
- Gestiona proveïdors i el seu estat
- Configura categories amb camps personalitzats

## Requisits

- WordPress 5.0 o superior
- PHP 7.2 o superior

## Autor

Seruji

## Llicència

GPL v2 o posterior
