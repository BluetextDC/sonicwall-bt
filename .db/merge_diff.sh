git checkout dev
OLDDIR=`pwd`
echo "Dropping merge DB"
mysql -h localhost -u root -proot -e "DROP DATABASE IF EXISTS wp_diff";
mysql -h localhost -u root -proot -e "CREATE DATABASE wp_diff";
echo "Importing current db"
current_dir=`ls ./.db/current/ -1 | sort -r | head -1`
echo importing from $current_dir
cat ./.db/current/$current_dir/*.sql > /tmp/bigmerge.sql
mysql --force -h localhost -u root -proot wp_diff < /tmp/bigmerge.sql
rm /tmp/bigmerge.sql
git checkout -f $1
hash=`php -r 'echo md5(exec("git branch | grep \* | cut -d \" \" -f2"));'`
echo $hash
echo "Running Diff:"
mysql --force -h localhost -u root -proot wp_diff < ./.db/commits/$hash.sql
echo "Exporting current.sql"
mysqldump -h localhost -u root -proot wp_diff -r /tmp/mergeoutput.sql
echo "Export Done, Chunk it"
mkdir /tmp/mergesplit
cd /tmp/mergesplit
/root/splitter/sqlsplitter /tmp/mergeoutput.sql 50000000
for file in *.sql; do dest="${file//[[:space:]]/.}" && mv -i "$file" "${dest//[^[:alnum:]._-]/}"; done
rename 's/mergeoutput/db/' *.sql
cd $OLDDIR
TIMESTAMP=`date +%s`
mkdir ./.db/current/$TIMESTAMP
cp /tmp/mergesplit/*.sql ./.db/current/$TIMESTAMP/
chmod 777 ./.db/current/$TIMESTAMP/*
rm -Rf /tmp/mergesplit
rm ./.db/current/*.sql
git add -A
git commit -m "Merged Database"
git push



