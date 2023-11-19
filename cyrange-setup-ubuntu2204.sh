#!/bin/bash

#
# cyrange-setup-ubuntu2204.sh
#
# Install cyrange on a fresh Ubuntu 22.04 server
#
# This script will:
# 
# 1. install VirtualBox
# 2. install Docker
# 3. install Cyrange
# 4. configure the firewall
#

# abort on any error
set -e

if [ "$EUID" -ne 0 ]; then
  echo "Please run as root"
  exit
fi

if [[ $(lsb_release -rs) != "22.04" ]]; then
  echo "This script requires Ubuntu 22.04"
  exit
fi

echo "## Install VirtualBox ..."
apt-get update
apt-get install -y --no-install-recommends virtualbox virtualbox-dkms virtualbox-ext-pack build-essential

echo "## Set password for user www-data ..."
PASSWD=$(openssl rand -base64 12)
echo "www-data:$PASSWD" | chpasswd


echo "## Create the home directory for www-data ..."
mkdir -p /var/www
chown www-data:www-data /var/www

echo "## Add www-data to the users allowed to run VirtualBox ..."
adduser www-data vboxusers

echo "## Enable VirtualBox web service ..."
echo "VBOXWEB_USER=www-data" >  /etc/default/virtualbox
echo "VBOXWEB_TIMEOUT=600" >>  /etc/default/virtualbox
echo "VBOXWEB_HOST=0.0.0.0" >>  /etc/default/virtualbox

service virtualbox start

cp /usr/lib/virtualbox/vboxweb-service.sh /etc/init.d/vboxweb-service
systemctl daemon-reload
service vboxweb-service restart

ss -ltpn | grep vbox

echo "## Install Docker ..."
apt-get install -y apt-transport-https ca-certificates curl gnupg-agent software-properties-common

curl -fsSL https://download.docker.com/linux/ubuntu/gpg | apt-key add -
apt-key fingerprint 0EBFCD88
add-apt-repository -y \
   "deb [arch=amd64] https://download.docker.com/linux/ubuntu \
   $(lsb_release -cs) \
   stable"
apt-get update
apt-get install -y docker-ce docker-ce-cli containerd.io

echo "## Install docker-compose ..."
wget https://github.com/docker/compose/releases/download/1.28.3/docker-compose-Linux-x86_64
mv docker-compose-Linux-x86_64 docker-compose
chmod +x docker-compose
mv docker-compose /usr/bin/

echo "## Install cyrange ..."
apt-get install -y unzip
mkdir -p /home/cyrange
cd /home/cyrange
wget https://artifacts.cylab.be/cyrange-web/latest/cyrange.zip

# this will overwrite existing files!
unzip -o cyrange.zip
rm cyrange.zip

# set the password for accessing www-data
sed -i "s/change-me/$PASSWD/g" cyrange.env

# set the IP of the server
#    list IP addresses   | filter out localhost | take first | trim  | extract IP      | remove mask
IP=$(ip a | grep "inet " | grep -v "scope host" | head -1    | xargs | cut -d " " -f 2 | cut -d "/" -f 1)
echo "Using first IP address $IP ..."
sed -i "s/localhost/$IP/g" cyrange.env

# in case the script is run twice...
# we destroy/recreate the containers
# to make sure the latest env variable is used
docker-compose stop cyrange queue scheduler
# remove stopped containers
docker-compose rm -f

echo "## Start cyrange ..."
docker-compose up -d

echo "## Configure firewall ..."
ufw allow ssh
ufw allow http
ufw allow https
ufw logging medium
ufw enable

SUBNET=$(docker network inspect cyrange_default | grep Subnet | cut -d '"' -f 4)
echo "cyrange seems running on subnet $SUBNET ..."

# from cyrange to vboxweb-service
ufw allow from "$SUBNET" to any port 18083 proto tcp
# from guacamole to RDP
ufw allow from "$SUBNET" to any port 15000:15999 proto tcp
# from cyrange to temporary SSH port
ufw allow from "$SUBNET" to any port 22000:22999 proto tcp

ufw allow 8080
ufw allow 8081

echo ""
echo "==============================="
echo "cyrange is now installed ..."
echo "http://$IP:8080"
echo "E-mail: cyrange@example.com"
echo "Password: admin"
echo "==============================="
echo "check the online tutorial:"
echo "https://cylab.be/blog/132/installing-the-cyrange-cyber-range-platform#first-steps"
