#!/usr/bin/env bash

if [ -z ${1+x} ]; then
echo -e "Usage: $0 <gdps_id> [plan]\nPlans: press_start (default), continue, boss_fight, final_stage"
exit
fi
gdps_id=$1
gdps_pass=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 12 | head -n 1)"*"
sudo apt install php php-fpm php-curl php-mysql
rm DOCS.md README.md
echo "Making DB..."
mysql -e "CREATE USER 'halgd_${gdps_id}'@'localhost' IDENTIFIED BY '${gdps_pass}';"
mysql -e "CREATE DATABASE gdps_${gdps_id};"
mysql -e "GRANT ALL PRIVILEGES ON gdps_${gdps_id}.* TO 'halgd_${gdps_id}'@'localhost';"
mysql gdps_${gdps_id} < database.sql
rm database.sql
if [ -z ${2+x} ]; then
  gdps_plan="press_start"
else
  gdps_plan=$2
fi

echo "Making Config... [${gdps_plan}]"
if [ $2 == "press_start" ]; then
  c_umax=110
  c_utrig=100
  c_lvlmax=500
  c_commmax=1000
  c_postmax=250
elif [ $2 == "continue" ]; then
  c_umax=550
  c_utrig=500
  c_lvlmax=2000
  c_commmax=20000
  c_postmax=2000
elif [ $2 == "boss_fight" ]; then
  c_umax=2200
  c_utrig=2000
  c_lvlmax=4000
  c_commmax=50000
  c_postmax=10000
elif [ $2 == "final_stage" ]; then
  c_umax=1000000
  c_utrig=50000
  c_lvlmax=1000000
  c_commmax=3000000
  c_postmax=5000000
else
  c_umax=550
  c_utrig=500
  c_lvlmax=2000
  c_commmax=20000
  c_postmax=2000
fi

sed -i -e "s/C_UMAX/${c_umax}/g" conf/limits.php
sed -i -e "s/C_UTRIG/${c_utrig}/g" conf/limits.php
sed -i -e "s/C_LVLMAX/${c_lvlmax}/g" conf/limits.php
sed -i -e "s/C_COMMMAX/${c_commmax}/g" conf/limits.php
sed -i -e "s/C_POSTMAX/${c_postmax}/g" conf/limits.php

echo "Makeing DB Config..."
sed -i -e "s/XDB_USERX/halgd_${gdps_id}/g" conf/dbconfig.php
sed -i -e "s/XDB_PASSX/${gdps_pass}/g" conf/dbconfig.php
sed -i -e "s/XDB_NAMEX/gdps_${gdps_id}/g" conf/dbconfig.php

echo "Making paths..."
mkdir -p files/savedata
touch files/ban_ip.txt
touch files/log.html
chown -R www-data:www-data .
git update-index --skip-worktree conf/
git update-index --skip-worktree files/

echo -e "DB Info:\n\tLogin: halgd_${gdps_id}\n\tPass: ${gdps_pass}"
rm install.sh