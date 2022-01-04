=== Culqi Integracion ===
Contributors: gonzalesc
Tags: culqi, full integration, payment method, peru, woocommerce
Donate link: https://www.paypal.me/letsgodev
Requires at least: 5.1
Tested up to: 5.2.3
Requires PHP: 5.6
Stable tag: 5.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Culqi es una pasarela de pago para Perú. Este plugin permite una integración con todos los cargos usando el API de Culqi.

== Description ==

Culqi Integracion te permite sincronizar todos los pagos con tu Wordpress.

Con este plugin podrás:

* Sincronizar los pagos desde Culqi con un click.
* Visualizar el detalle de cada pago como un CPT.
* Si usas Woocommerce podrás activar el método de pago para este fin.
* Tener un log de actividades de Culqi para cada pedido de Woocommerce.
* Agregar el logo de tu comercio a tu modal/popup de Culqi


> <strong>Woocommerce Culqi Integración Pago con un click</strong><br>
>
> Mira la **nueva versión premium** disponible en ([https://www.letsgodev.com/product/woocommerce-culqi-pago-con-un-click/](http://bit.ly/304QRdF))
>
> * Permite hacer el pago con un sólo click.
> * En tu página de checkout aparecerá el modal de Culqi al pagar
> * Irá directamente a la sección "Gracias por tu compra"
> * Es compatible con el plugin de Suscripciones de Culqi.
> * Aumentarán tus conversiones de compra al disminuir los pasos de pago.
> * Soporte Premium
>



> <strong>Woocommerce Culqi Integración Suscripciones</strong><br>
>
> Mira la **nueva versión premium** disponible en ([https://www.letsgodev.com/product/wordpress-culqi-integracion-subscripciones/](http://bit.ly/2UZO7j9))
>
> * Permite sincronizar con clientes, planes y suscripciones.
> * Permite crear y borrar planes.
> * Permite crear productos recurrentes.
> * Podrás relacionar uno o varios productos con un plan.
> * Podras usar la pasarela de pago de Woocommerce para pagos recurrentes.
> * Soporte Premium
>



> <strong>Wordpress Culqi Integración Botones de Pago</strong><br>
>
> Mira la **nueva versión premium** disponible en ([https://www.letsgodev.com/product/wordpress-culqi-integracion-botones-de-pago/](http://bit.ly/2oMUffe))
>
> * Permite colocar botones de pago en tu website.
> * Puedes personalizar cada botón
> * Puedes usar botones de diferentes monedas y con diferentes montos
> * Email personalizado por cada pago
> * No necesitas tener instalado un ecommerce
> * Soporte Premium
>


= Github =

Fork me in [https://github.com/gonzalesc/wp-culqi-integration.git](https://github.com/gonzalesc/wp-culqi-integration.git)

= Available Languages =

* English
* Spanish


= Woocommerce Payme ( Alignet ) =
Pasarela de pago Payme para Woocommerce con la mejor comisión en Perú [https://www.letsgodev.com/product/woocommerce-payme-alignet/](http://bit.ly/2V0wCiG)


== Installation ==
1. Descomprimir y subir el archivo 'culqi-integration' al directorio '/wp-content/plugins/'

2. Activar el plugin en la sección 'Plugins'

3. Ir a la configuración del plugin y poner su llave pública y llave secreta

4. Para usar Multipagos, debes activarlo en la pasarela de pago Culqi y debes configurar el Webhook.
- Debemos entrar al panel de Culqi e ir a la sección de `eventos` y al submenu de `webhooks`
- Debes elegir el evento : `order.status.changed`
- La URL que debes poner está en la configuración de la pasarela de pago Culqi para Woocommerce: `URL del Webhook`


== Frequently Asked Questions ==

= Cómo obtengo las llaves de Culqi ? =

Es fácil!, sólo debes registrarte aqui : [https://www.culqi.com/](https://www.culqi.com/)

= Tengo problemas cuando sincronizo los pagos =

Necesitamos validar si el servicio de Culqi está disponible, para ello te sugiero hagas una prueba que te tomará 10 minutos, por favor sigue esta guía : [https://blog.letsgodev.com/tips-es/verificar-servicio-de-culqi-en-10-minutos/](http://bit.ly/2V0wJe6)


== Screenshots ==

1. Página de bienvenida. 
2. Configuración del plugin
3. Método de pago para Woocommerce
4. Activar Multipagos
5. Configurar Webhook


== Changelog ==

= 1.0.0 =
* ready