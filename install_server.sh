#!/bin/bash
#/ Usage: tarifador.sh [-vh]
#/
#/ Install tarifador open source.
#/
#/ OPTIONS:
#/   -v | --verbose    Enable verbose output.
#/   -h | --help       Show this message.

######################################################
#           Tarifador Install Script                 #
#      Script Adaptado por Rafael Benedicto          #
#              rafhaeu@gmail.com                     #
#                                                    #
#          Script created by Mike Tucker             #
#            mtucker6784@gmail.com                   #
#                                                    #
# Feel free to modify, but please give               #
# credit where it's due. Thanks!                     #
######################################################

# Parse arguments
while true; do
  case "$1" in
    -h|--help)
      show_help=true
      shift
      ;;
    -v|--verbose)
      set -x
      verbose=true
      shift
      ;;
    -*)
      echo "Error: invalid argument: '$1'" 1>&2
      exit 1
      ;;
    *)
      break
      ;;
  esac
done

print_usage () {
  grep '^#/' <"$0" | cut -c 4-
  exit 1
}

if [ -n "$show_help" ]; then
  print_usage
else
  for x in "$@"; do
    if [ "$x" = "--help" ] || [ "$x" = "-h" ]; then
      print_usage
    fi
  done
fi

# ensure running as root
if [ "$(id -u)" != "0" ]; then
    if ! hash sudo 2>/dev/null; then
        exec su -c "$0" "$@"
    else
        exec sudo "$0" "$@"
    fi
fi

clear

readonly APP_USER="benetelecom"
readonly APP_NAME="tarifador"
readonly APP_PATH="/var/www/$APP_NAME"

progress () {
  spin[0]="-"
  spin[1]="\\"
  spin[2]="|"
  spin[3]="/"

  echo -n " "
  while kill -0 "$pid" > /dev/null 2>&1; do
    for i in "${spin[@]}"; do
      echo -ne "\\b$i"
      sleep .3
    done
  done
  echo ""
}

log () {
  if [ -n "$verbose" ]; then
    eval "$@" |& tee -a /var/log/tarifador-install.log
  else
    eval "$@" |& tee -a /var/log/tarifador-install.log >/dev/null 2>&1
  fi
}

install_packages () {
  case $distro in
    ubuntu|debian)
		echo " Script not suport this distro"
		exit 1;
        ;;
    centos)
      for p in $PACKAGES; do
        if yum list installed "$p" >/dev/null 2>&1; then
          echo "  * $p already installed"
        else
          echo "  * Installing $p"
          log "yum -y install $p"
        fi
      done;
      ;;
    fedora)
        echo " Script not suport this distro"
		exit 1;
		;;
  esac
}

create_virtualhost () {
  {
    echo "<VirtualHost *:80>"
    echo "  <Directory $APP_PATH/public>"
    echo "      Allow From All"
    echo "      AllowOverride All"
    echo "      Options -Indexes"
    echo "  </Directory>"
    echo ""
    echo "  DocumentRoot $APP_PATH/public"
    echo "  ServerName $fqdn"
    echo "</VirtualHost>"
  } >> "$apachefile"
}

create_user () {
  echo "* Creating tarifador user."

    adduser "$APP_USER"
	usermod -a -G "$apache_group" "$APP_USER"
}

run_as_app_user () {
  if ! hash sudo 2>/dev/null; then
      su -c "$@" $APP_USER
  else
      sudo -i -u $APP_USER "$@"
  fi
}

install_composer () {
  # https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md
  EXPECTED_SIGNATURE="$(wget -q -O - https://composer.github.io/installer.sig)"
  run_as_app_user php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  ACTUAL_SIGNATURE="$(run_as_app_user php -r "echo hash_file('SHA384', 'composer-setup.php');")"

  if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]
  then
      >&2 echo 'ERROR: Invalid composer installer signature'
      run_as_app_user rm composer-setup.php
      exit 1
  fi

  run_as_app_user php composer-setup.php
  run_as_app_user rm composer-setup.php

  mv "$(eval echo ~$APP_USER)"/composer.phar /usr/local/bin/composer
}

install_tarifador () {
  create_user

  echo "* Creating PostgreSql Database/User."
  echo "* Please Input your Postgresql root password:"
  echo 'create database billing;' | runuser -l postgres -c "psql"
  echo 'create user benetelecom;' | runuser -l postgres -c "psql"
  echo "ALTER USER benetelecom WITH ENCRYPTED password 'b1ll1ng';" | runuser -l postgres -c "psql"
  echo "GRANT ALL PRIVILEGES ON DATABASE billing TO benetelecom;" | runuser -l postgres -c "psql"
  
  echo "* Cloning tarifador from github to the web directory."
  log "git clone https://github.com/benetelecom/desenvolvimento.git $APP_PATH"

  echo "* Configuring .env file."
  cp "$APP_PATH/.env.example" "$APP_PATH/.env"

  #TODO escape SED delimiter in variables
  sed -i '1 i\#Created By BeneTelecom Installer' "$APP_PATH/.env"
  sed -i "s|^\\(APP_TIMEZONE=\\).*|\\1$tzone|" "$APP_PATH/.env"
  sed -i "s|^\\(DB_HOST=\\).*|\\1localhost|" "$APP_PATH/.env"
  sed -i "s|^\\(DB_DATABASE=\\).*|\\1billing|" "$APP_PATH/.env"
  sed -i "s|^\\(DB_USERNAME=\\).*|\\1benetelecom|" "$APP_PATH/.env"
  sed -i "s|^\\(DB_PASSWORD=\\).*|\\1b1ll1ng|" "$APP_PATH/.env"
  sed -i "s|^\\(APP_URL=\\).*|\\1http://$fqdn|" "$APP_PATH/.env"

  echo "* Installing composer."
  install_composer

  echo "* Setting permissions."
  for chmod_dir in "$APP_PATH/storage" "$APP_PATH/public"; do
    chmod -R 775 "$chmod_dir"
  done

  chown -R "$APP_USER":"$apache_group" "$APP_PATH"

  echo "* Running composer."
  # We specify the path to composer because CentOS lacks /usr/local/bin in $PATH when using sudo
  run_as_app_user /usr/local/bin/composer install --no-dev --prefer-source --working-dir "$APP_PATH"

  sudo chgrp -R "$apache_group" "$APP_PATH/vendor"

  echo "* Generating the application key."
  log "php $APP_PATH/artisan key:generate --force"

  echo "* Artisan Migrate."
  log "php $APP_PATH/artisan migrate --force"

  echo "* Artisan Seed."
  log "php $APP_PATH/artisan seed --force"

  echo "* Creating scheduler cron."
  (crontab -l ; echo "* * * * * /usr/bin/php $APP_PATH/artisan schedule:run >> /dev/null 2>&1") | crontab -
}

set_firewall () {
  if [ "$(firewall-cmd --state)" == "running" ]; then
    echo "* Configuring firewall to allow HTTP traffic only."
    log "firewall-cmd --zone=public --add-port=http/tcp --permanent"
    log "firewall-cmd --reload"
  fi
}

set_selinux () {
  #Check if SELinux is enforcing
  if [ "$(getenforce)" == "Enforcing" ]; then
    echo "* Configuring SELinux."
    #Required for ldap integration
    setsebool -P httpd_can_connect_ldap on
    #Sets SELinux context type so that scripts running in the web server process are allowed read/write access
    chcon -R -h -t httpd_sys_rw_content_t "$APP_PATH/storage/"
    chcon -R -h -t httpd_sys_rw_content_t "$APP_PATH/public/"
  fi
}

set_hosts () {
  echo "* Setting up hosts file."
  echo >> /etc/hosts "127.0.0.1 $(hostname) $fqdn"
}

if [ -f /etc/os-release ]; then
  distro="$(source /etc/os-release && echo "$ID")"
  version="$(source /etc/os-release && echo "$VERSION_ID")"
elif [ -f /etc/centos-release ]; then
  distro="centos"
  version="6"
else
  distro="unsupported"
fi

echo '
       
  ____                          _______          _                                   
 |  _ \                        |__   __|        | |                                  
 | |_) |   ___   _ __     ___     | |      ___  | |   ___    ___    ___    _ __ ___  
 |  _ <   / _ \ |  _ \   / _ \    | |     / _ \ | |  / _ \  / __|  / _ \  |  _   _ \ 
 | |_) | |  __/ | | | | |  __/    | |    |  __/ | | |  __/ | (__  | (_) | | | | | | |
 |____/   \___| |_| |_|  \___|    |_|     \___| |_|  \___|  \___|  \___/  |_| |_| |_|
                                                                                     
                                                                                     
'
echo ""
echo "  Instalador do Tarifador BeneTelecom para o CentOS!"
echo ""
shopt -s nocasematch
case $distro in
  *centos*|*redhat*|*ol*|*rhel*)
    echo "  O Instalador detectou o Sistema $distro Versão $version."
    distro=centos
    apache_group=apache
    apachefile=/etc/httpd/conf.d/$APP_NAME.conf
    ;;
  *fedora*)
    echo "  The installer has detected $distro version $version."
    distro=fedora
    apache_group=apache
    apachefile=/etc/httpd/conf.d/$APP_NAME.conf
    ;;
  *)
    echo "  O Instalador não Conseguiu Detectar o seu Sistema Operacional"
    exit 1
    ;;
esac
shopt -u nocasematch

echo -n "  Q. Qual o Endereço FQDN do seu Servidor? ($(hostname --fqdn)): "
read -r fqdn
if [ -z "$fqdn" ]; then
  readonly fqdn="$(hostname --fqdn)"
fi
echo "     Configurando o FQDN para $fqdn"
echo ""

ans=default
until [[ $ans == "yes" ]] || [[ $ans == "no" ]]; do
echo -n "  Q. Deseja criar automaticamente uma senha para o Banco de Dados? (y/n) "
read -r setpw

case $setpw in
  [yY] | [yY][Ee][Ss] )
    psqluserpw="$(< /dev/urandom tr -dc _A-Z-a-z-0-9 | head -c16; echo)"
    echo ""
    ans="yes"
    ;;
  [nN] | [n|N][O|o] )
    echo -n  "  Q. Defina uma Senha para o Banco de Dados?"
    read -rs psqluserpw
    echo ""
    ans="no"
    ;;
  *)  echo "  Resposta Invalida. Por favor precione y ou n"
    ;;
esac
done

case $distro in
  centos)
  if [[ "$version" =~ ^7 ]]; then
    # Install for CentOS/Redhat 7
    tzone=$(timedatectl | gawk -F'[: ]' ' $9 ~ /zone/ {print $11}');

    echo "* Adicionando, epel-release, PHP and PostgreSql repositorios."
    log "yum update -y"
	log "yum groupinstall 'Development Tools' -y"
    log "yum -y install wget epel-release"
    log "yum -y install yum-utils"
    log "rpm -Uvh http://rpms.remirepo.net/enterprise/remi-release-7.rpm"
    log "yum-config-manager --enable remi-php73"
    log "yum -y install https://download.postgresql.org/pub/repos/yum/reporpms/EL-7-x86_64/pgdg-redhat-repo-latest.noarch.rpm"
    
    echo "* Instalando Apache httpd, PHP, Postgres1 e outras dependencias."
    PACKAGES="httpd postgresql11 postgresql11-server postgresql11-contrib git unzip vim php php-mcrypt php-cli php-gd php-curl php-ldap php-zip php-fileinfo php-common php-opcache php-mysql php-xml php-mbstring php-pgsql php-fpm php-bcmath php-embedded php-json php-simplexml php-process"
    install_packages

    echo "* Configurando o Apache."
    create_virtualhost

    set_hosts

    echo "* instalando o postgres."
    /usr/pgsql-11/bin/postgresql-11-setup initdb
    
	echo "* Configurando o postgres para inicializar no boot e iniciando o postgres."
    #log "/usr/pgsql-11/bin/postgresql-11-setup initdb"
    log "systemctl enable postgresql-11"
    log "systemctl start postgresql-11"
	
	log "cp /var/lib/pgsql/11/data/pg_hba.conf /var/lib/pgsql/11/data/pg_hba.conf.bak"
	sed -i "s|^\\(local   all             all                                     \\).*|\\1md5|" "/var/lib/pgsql/11/data/pg_hba.conf"
	sed -i "s|^\\(host    all             all             127.0.0.1/32            \\).*|\\1md5|" "/var/lib/pgsql/11/data/pg_hba.conf"
	sed -i "s|^\\(host    all             all             ::1/128                 \\).*|\\1md5|" "/var/lib/pgsql/11/data/pg_hba.conf"
    
    log "systemctl restart postgresql-11"

    install_tarifador

    set_firewall

    set_selinux

    echo "* Configurando o Apache httpd para inicializar no boot e iniciando o service."
    log "systemctl enable httpd.service"
    log "systemctl restart httpd.service"
  else
    echo "Versão do CentOS Não Suportada. Versão encontrada: $version"
    exit 1
  fi
  ;;
esac

setupmail=default
until [[ $setupmail == "yes" ]] || [[ $setupmail == "no" ]]; do
echo -n "  Q. Deseja Configurar o Servidor Email? (y/n) "
read -r setupmail

case $setupmail in
  [yY] | [yY][Ee][Ss] )
    echo -n "  Servidor de Saida de Email:"
    read -r mailhost
    sed -i "s|^\\(MAIL_HOST=\\).*|\\1$mailhost|" "$APP_PATH/.env"

    echo -n "  Porta do Servidor de Email:"
    read -r mailport
    sed -i "s|^\\(MAIL_PORT=\\).*|\\1$mailport|" "$APP_PATH/.env"

    echo -n "  Usuario:"
    read -r mailusername
    sed -i "s|^\\(MAIL_USERNAME=\\).*|\\1$mailusername|" "$APP_PATH/.env"

    echo -n "  Senha:"
    read -rs mailpassword
    sed -i "s|^\\(MAIL_PASSWORD=\\).*|\\1$mailpassword|" "$APP_PATH/.env"
    echo ""

    echo -n "  Encryption(null/TLS/SSL):"
    read -r mailencryption
    sed -i "s|^\\(MAIL_ENCRYPTION=\\).*|\\1$mailencryption|" "$APP_PATH/.env"

    echo -n "  From address:"
    read -r mailfromaddr
    sed -i "s|^\\(MAIL_FROM_ADDR=\\).*|\\1$mailfromaddr|" "$APP_PATH/.env"

    echo -n "  From name:"
    read -r mailfromname
    sed -i "s|^\\(MAIL_FROM_NAME=\\).*|\\1$mailfromname|" "$APP_PATH/.env"

    echo -n "  Reply to address:"
    read -r mailreplytoaddr
    sed -i "s|^\\(MAIL_REPLYTO_ADDR=\\).*|\\1$mailreplytoaddr|" "$APP_PATH/.env"

    echo -n "  Reply to name:"
    read -r mailreplytoname
    sed -i "s|^\\(MAIL_REPLYTO_NAME=\\).*|\\1$mailreplytoname|" "$APP_PATH/.env"
    setupmail="yes"
    ;;
  [nN] | [n|N][O|o] )
    setupmail="no"
    ;;
  *)  echo "  Resposta Invalida. Por Favor Selecione y or n"
    ;;
esac
done

echo ""
echo "  ***Abra no Navegador http://$fqdn  para acesso ao tarifador.***"
echo ""
echo ""
echo "*  Limpando a Tela..."
echo "* Finalizado!"
echo '

 ________                      __   ______                   __                     
|        \                    |  \ /      \                 |  \                    
 \########  ______    ______   \##|  ######\  ______    ____| ##  ______    ______  
   | ##    |      \  /      \ |  \| ##_  \## |      \  /      ## /      \  /      \ 
   | ##     \######\|  ######\| ##| ## \      \######\|  #######|  ######\|  ######\
   | ##    /      ##| ##   \##| ##| ####     /      ##| ##  | ##| ##  | ##| ##   \##
   | ##   |  #######| ##      | ##| ##      |  #######| ##__| ##| ##__/ ##| ##      
   | ##    \##    ##| ##      | ##| ##       \##    ## \##    ## \##    ##| ##      
    \##     \####### \##       \## \##        \#######  \#######  \######  \##      
                                                                                    
                                                                                    
'                                                                                  
sleep 1
