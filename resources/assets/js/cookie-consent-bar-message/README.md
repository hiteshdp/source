# jquery.cookieMessage.js

Plugin jQuery que muestra y gestion mensage de autorización de cookie para páginas web custom. 

Para usar el plugin añadelo a tu html de la siguiente manere despues de añadir jQuery:

```html
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript" src="path/to/jquery.cookieMessage.min.js"></script>
```

Para poner en funcioanmeiento el plugin sólo necesitas llamar a la siguiente funcion:
```html
<script type="text/javascript">
$.cookieMessage({
    'mainMessage': 'Utilizamos cookies propias para garantizar la mejor experiencia a nuestros usuarios, elaborar información estadística y mejorar el rendimiento de nuestro sitio web. <br> Si clicas en <strong>Aceptar</strong> o continúas navegando, estarás aceptando los términos indicados en nuestra <a href="/path/to/politica-privacidad.html">política de cookies</a>. ',
    'acceptButton': 'Aceptar',
});
</script>
```

Puedes customizar el mensaje con las siguientes opciones:

```html
<script type="text/javascript">
$.cookieMessage({
    mainMessage: "",
    acceptButton: "Aceptar",
    expirationDays: 20,
    backgroundColor: '#666',
    fontSize: '14px',
    fontColor: 'white',
    btnBackgroundColor: '#f2a920',
    btnFontSize: '11px',
    btnFontColor: 'white',
    linkFontColor: '#ffff00',
    cookieName: 'cookieMessage'
});
</script>
```
Este pluging a sido desarrollado por el <a href="https://desarrollador.ninja/">desarrollador ninja</a>.
