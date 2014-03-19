Botijon
============

El bot está escrito en __php__. Originalmente fue ideado para ayudar a los usuarios del canal _#php-es_ de la red _freenode_ a localizar información acerca de las funciones del lenguaje _PHP_, pero teniendo en 
mente la facilidad de agregar funcionalidad en forma sencilla, para otros fines.

Funcionamiento
-----------------------

Renombra el arhivo <code>config/config-sample.php</code> a <code>config/config.php</code> y editalo con tu configuración (nick, canal, red, etc...)

El bot se corre desde la linea de comandos de la siguiente forma:

<code>/usr/bin/php /ruta/al/bot/bot.php</code> 

Para correrlo y que quede corriendo en el fondo (background) sería de la siguiente forma:

<code>nohup /usr/bin/php /ruta/al/bot/bot.php &</code>

Cuando el bot ya está corriendo, las funciones disponibles se utilizan dándole los comandos
de la siguiente manera:
<code>![comando]</code>

Por ejemplo:  <code>!help</code>

Requerimientos
-----------------------------------

Debido a que varias funciones del bot requieren del uso de API's, para poder ejecutar el bot de manera correcta se requiere un token de las siguientes cuentas, para ello se requiere una cuenta.

bitly: 		https://bitly.com => Command{Acortador}.
twitter: 	https://dev.twitter.com => Command{twitter}
youtube:	https://cloud.google.com/console => Command{Youtube}

sqlite3:
			CentOS/Fedora/RHEL
			<code>yum -y install sqlite</code>
			Debian/Ubuntu
			<code>apt-get install sqlite</code>

modulo php-pdo:
			CentOS/Fedora/RHEL
			<code>yum -y install php-pdo</code>
			Debian/Ubuntu
			<code>apt-get install php-pdo</code>

Configuracion
-----------------------------------
sqlite3:
			Dentro de la carpeta sql/ se encuentra el esquema de base de datos , esta debe de ser cargada al engine de sqlite.
			<code> nix_promp$ sqlite3 db_name < schema_name </code>

Como agregar funciones 
-----------------------------------

Las funciones del bot, estan en los archivos del directorio <code>_commands_</code>.

En cada archivo reside una de las funciones del bot.

Dichos archivos contienen una clase php la cual es clase hija de la clase <code>_command_</code>.

Estos archivos de preferencia deben ser nombrados de acuerdo al nombre de la función que desempeñan.

Puedes tomar como base alguno de los archivos ahi presentes para crear nuevas funciones.

De inicio tienes que modificar las variables <code>$name</code> y <code>$public</code> de acuerdo a tu comando.

La primera variable contiene el nombre del comando, y la segunda indica si el comando es público o no.

Si el comando va a ser disponible solo para el dueño del bot, <code>$public</code> debe ser puesto en <code>false</code>.

Para agregar la funcionalidad solo tienes que actualizar la función <code>process</code>.
Dentro de esta función, tu código debe cambiar el contenido de la variable <code>$output</code><code>$output</code> es en realidad lo que el bot va a enviar al server, ya sea algún comando irc, o algún texto que se va a desplegar en el chat room.
