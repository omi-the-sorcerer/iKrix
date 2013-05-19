#!/bin/bash
# Instalador PiPanel

if [[ $EUID -ne 0 ]]; then
   echo "Necesitas ser superusuario para instalar PiPanel" 1>&2
   echo "Prueba: sudo "$0 1>&2
   exit 1
fi

echo -e "\nInstalador PiPanel\n-----------------"

echo -e "\nInstalando los pre-requisitos...."
apt-get -y install ethtool apache2 php5 pwauth git || { echo -e "Instalacion fallida!" 1>&2; exit 1; }

echo -e "\nBorrando antiguas copias de PiPanel...."
rm -fr /usr/share/pipanel || { echo -e "Instalacion fallida!" 1>&2; exit 1; }

echo -e "\nDescargar ultima version de PiPanel desde gitHub...."
git clone https://github.com/NeonHorizon/pipanel.git /usr/share/pipanel/

echo -e "\nCopiando la configuracion por defecto...."
cp -R /usr/share/pipanel/default_config/apache2 /etc || { echo -e "Instalacion fallida!" 1>&2; exit 1; }
cp -R /usr/share/pipanel/default_config/sudoers.d /etc || { echo -e "Instalacion fallida!" 1>&2; exit 1; }
chmod 440 /etc/sudoers.d/pipanel || { echo -e "Instalacion fallida!" 1>&2; exit 1; }

echo -e "\nCreando los directorios para logs...."
if [ ! -d /var/log/pipanel ]; then
  mkdir /var/log/pipanel
fi

echo -e "\nActivando los modulos requeridos de apache...."
a2enmod rewrite authnz_external || { echo -e "Instalacion fallida!" 1>&2; exit 1; }

echo -e "\nActivando la configuracion del sitio PiPanel...."
a2dissite default || { echo -e "Instalacion fallida!" 1>&2; exit 1; }
a2ensite pipanel || { echo -e "Instalacion fallida!" 1>&2; exit 1; }

echo -e "\nReiniciando apache...."
service apache2 restart || { echo -e "Instalacion fallida!" 1>&2; exit 1; }

echo -e "\nDando permisos nuevos..."
/usr/share/pipanel/privwww

echo -e "\nInstalacion completada!"