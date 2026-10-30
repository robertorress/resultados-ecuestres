document.addEventListener('DOMContentLoaded', function() {
  var contrastEnabled = window.localStorage.getItem('resultados-contrast') === 'true';
  
  // Seleccionamos todos los botones de contraste que ya existen en el HTML
  var contrastLinks = document.querySelectorAll('.contrast-menu-item');

  function updateContrastMode(enabled) {
    document.body.classList.toggle('contrast-mode', enabled);
    
    contrastLinks.forEach(function(link) {
      link.setAttribute('aria-pressed', enabled ? 'true' : 'false');
      
      // Buscamos el contenedor del texto dentro de cada botón para actualizarlo
      var textSpan = link.querySelector('.btn-contrast-text');
      if (textSpan) {
        textSpan.textContent = enabled ? 'MODO NORMAL' : 'MODO CONTRASTE';
      }
    });
  }

  // Asignamos el evento click a todos los botones encontrados
  contrastLinks.forEach(function(link) {
    link.addEventListener('click', function(event) {
      event.preventDefault();
      var enabled = !document.body.classList.contains('contrast-mode');
      updateContrastMode(enabled);
      window.localStorage.setItem('resultados-contrast', enabled ? 'true' : 'false');
    });
  });

  // Ejecutamos el estado inicial guardado
  updateContrastMode(contrastEnabled);

  // Inicialización correcta y limpia de los Sidenavs de Materialize
  var sidenavs = document.querySelectorAll('.sidenav');
  M.Sidenav.init(sidenavs);

  document.querySelectorAll('.sidenav-trigger').forEach(function(trigger) {
    trigger.addEventListener('click', function(event) {
      event.preventDefault();
      event.stopPropagation();

      var target = document.getElementById(trigger.getAttribute('data-target'));
      if (!target) return;

      var instance = M.Sidenav.getInstance(target) || M.Sidenav.init(target);
      instance.open();
    });
  });
});

/*Antiguo con la navegación lateral y menú de contraste dinámico*/
/*
document.addEventListener('DOMContentLoaded', function() {
  var contrastEnabled = window.localStorage.getItem('resultados-contrast') === 'true';
  var contrastItems = [];

  function updateContrastMode(enabled) {
    document.body.classList.toggle('contrast-mode', enabled);
    contrastItems.forEach(function(item) {
      item.setAttribute('aria-pressed', enabled ? 'true' : 'false');
      item._contrastLabel.nodeValue = enabled ? 'MODO NORMAL' : 'MODO CONTRASTE';
    });
  }

  var menus = document.querySelectorAll('.sidenav, .nav-wrapper > ul.right');
  menus.forEach(function(menu) {
    if (menu.matches('.nav-wrapper > ul.right') && window.innerWidth < 993) {
      return;
    }

    var item = document.createElement('li');
    var link = document.createElement('a');
    var icon = document.createElement('i');
    var label = document.createTextNode('MODO CONTRASTE');

    link.href = '#';
    link.className = 'contrast-menu-item';
    link.setAttribute('role', 'button');
    link.setAttribute('aria-pressed', 'false');
    icon.className = 'left material-icons';
    icon.textContent = 'contrast';
    link.appendChild(icon);
    link.appendChild(label);
    link._contrastLabel = label;
    item.appendChild(link);
    menu.appendChild(item);
    contrastItems.push(link);

    link.addEventListener('click', function(event) {
      event.preventDefault();
      var enabled = !document.body.classList.contains('contrast-mode');
      updateContrastMode(enabled);
      window.localStorage.setItem('resultados-contrast', enabled ? 'true' : 'false');
    });
  });

  updateContrastMode(contrastEnabled);

  var sidenavs = document.querySelectorAll('.sidenav');
  M.Sidenav.init(sidenavs);

  document.querySelectorAll('.sidenav-trigger').forEach(function(trigger) {
    trigger.addEventListener('click', function(event) {
      event.preventDefault();
      event.stopPropagation();

      var target = document.getElementById(trigger.getAttribute('data-target'));
      if (!target) {
        return;
      }

      var instance = M.Sidenav.getInstance(target) || M.Sidenav.init(target);
      instance.open();
    });
  });
});
*/